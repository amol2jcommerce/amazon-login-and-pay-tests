<?php
require_once 'config.php';
?>
<html>
  <head>
    <title>Smooth digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" href="main.css" type="text/css" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  </head>
  <body>

    <div id="status" class="status info">Preparing...</div>
    <br />
    <p />
  	<div id="login_with_amazon_button" class="modalcontainer">
  	  <div class="modal"></div>
  	  <img id="pre_image" src="https://d23yuld0pofhhw.cloudfront.net/uk/sandbox/amazonpay/gold/large/button_T1.png" class="blur"></img>
  	</div>
  	<p id="payment_message" class="gone">
      Thanks for using Amazon Pay.<br /><br /> We will charge your <span id="payment_descriptor_name"></span> ending in <span id="payment_descriptor_tail"></span>.<br />
      <span style="font-size: 10pt">To change the payment instrument, please click <i><a href="#" id="toggleWidget">here</a></i>.</span>
    </p>
    <p id="payment_message_success" class="gone">
      Thanks for your purchase. For security reasons you have been disconnected from your Amazon account.<br />Please <a href=".">start over</a> to place another purchase.
    </p>
    <p id="payment_message_failed" class ="gone">
      Unfortunately something went wrong with your payments. Please try again. If the issue persists, please get in touch.
    </p>
    <p id="payment_message_declined_IPM" class ="gone">
      Unfortunately the payment isntrument selected was declined, Please select another isntrument from the widget below.
    </p>
    <p id="payment_message_declined_hard" class ="gone">
      Unfortunately something went wrong with your pyments. Please get in touch to find an alternative way of payment.
    </p>
    <div id="pay_with_amazon_payment_widget" class="offscreen"></div>
    
    <div id="buy" name="buy" class="gone">Complete purchase</div>
    <input type="hidden" name="accesstoken" id="accesstoken" />
  	
	  <script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
      };
      
      window.onAmazonPaymentsReady = function() {
         renderButton();
         setStatusMessage("Please sign in using your Amazon account.");
      };


      /**
       * Render the Amazon Pay button
       */
      function renderButton(){
          var authRequest;
          OffAmazonPayments.Button("login_with_amazon_button", "<?php echo $config['merchant_id']; ?>", {
            type:  "PwA",
            color: "Gold",
            size:  "large",
            language: "en-gb",
  
            authorization: function() {
              loginOptions =
                {scope: "profile payments:widget payments:shipping_address payments:billing_address payments:instrument_descriptor", popup: "true"};
                
              // add extra functionality to the authRequest to display the loading indicator
              authRequest = function(){
                $("body").addClass("loading");
                $("#login_with_amazon_button .modal").height($("#login_with_amazon_button img").height());
                $("#login_with_amazon_button .modal").width($("#login_with_amazon_button img").width());
                $("#status").html("Initiated user authentication");
                return amazon.Login.authorize (loginOptions, customerAuthorized);
              }();
            },
            onError: function(error) {
              console.log(error.getErrorMessage());
            }
          });
          
          // loading animation, move the button from blurred to normal
          $("#login_with_amazon_button img").fadeIn("slow", function() {
            $(this).removeClass("blur").addClass("clickable");
          });
       }
  
  
     /**
      * method called when and if the customer authorized or denied consent
      */
  	 function customerAuthorized(result){
  	   // on error log what happened
  		if(result.error){
  		  console.log("no consent or similar");
  		  console.log(result);
  		  setStatusMessage("Authentication failed, please accept all consents to go on.");
  		  // on success, save the acess_token and render the widget
  		} else {
  		  $("#accesstoken").val(result.access_token);
  		  $("#login_with_amazon_button").fadeOut("slow", renderPaymentWidget);
  		  setStatusMessage("Welcome! We are preparing your oder now: ");
  		}
  		$("body").removeClass("loading");
  	 }
        
      var orderReferenceId;
      var keepWalletOpen = false;
      
      /**
       * render the payment widget
       */
      function renderPaymentWidget(){
        window.walletWidget = new OffAmazonPayments.Widgets.Wallet({
            sellerId: '<?php echo $config["merchant_id"]?>',
            scope: "profile payments:widget payments:shipping_address payments:billing_address payments:instrument_descriptor",
            onOrderReferenceCreate: oroCreateCallback,
            onPaymentSelect: paymentSelectCallback,
            onReady: function(){
              $("#toggleWidget").click(toggleWidget);
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error);
            }
          });
          window.walletWidget.bind("pay_with_amazon_payment_widget");
      }
      
      function oroCreateCallback(orderReference) {
        // sotre the oro
        orderReferenceId = orderReference.getAmazonOrderReferenceId();
        
        addStatusMessage("setting order details... ")
        
        // set the details of the transaction
        $.post("server/backend.php", 
          {action: "setOrderReferenceDetails", 
            data : { orderReferenceId: orderReferenceId
              , ordertotal: 1000
            }
          })
          .done(function( data ) {
            // get the details of the transaction
            addStatusMessage("retreiving data from Amazon... ");
              getORODetailsAJAX(/* this is the callback for getORODetails*/ function(result){
                $("#payment_message").fadeIn("slow").addClass("success");
                $("#buy").click(purchaseAJAX);
                addStatusMessage("Done! ");
                $("#status").removeClass("info error", 1000).addClass("success", 1000);

              });
          })
          .fail(function(error){
             setStatusMessage("An unexpected error occured.");
             $("#status").removeClass("info success warning", 1000).addClass("error", 1000);
            console.log(error.getErrorMessage());
          });
      }
      
      function paymentSelectCallback(orderReference) {
        console.log("payment select");
        
        var callback = null;
        // only after placing the widget in the visual area (we set the class gone at that time), react
        if($("#pay_with_amazon_payment_widget").hasClass("gone")){
          // only toggl the widget if we not explicitly prevent this
          if(!keepWalletOpen){
            toggleWidget();
          }
          setStatusMessage("Payment details changed. ");
          $("#status").removeClass("error success", 1000).addClass("info", 1000);
          addStatusMessage("Refreshing data from Amazon... ");
          callback = function(){
            addStatusMessage("Done!");
            $("#status").removeClass("error info", 1000).addClass("success", 1000);
          };
        } else {
          // now that the widget was rendered and we have an ORO, make it gone, but in the desired spot
          $("#pay_with_amazon_payment_widget").addClass("gone").removeClass("offscreen");
        }
        $("#buy").removeClass("gone").addClass("button", 1000);
        $("#buy").fadeIn("slow");
        getORODetailsAJAX(callback);
      }

  	  function logout(reload){
  		  amazon.Login.logout();
  		  if(reload){
  		    location.reload();
  		  }
  	  }
  	  
  	  
  	  function addStatusMessage(message){
  	    $("#status").html($("#status").html() + message);
  	  }
  	  
  	  function setStatusMessage(message){
  	    $("#status").html(message);
  	  }
  	  
  	  function toggleWidget(){
  	    $("#pay_with_amazon_payment_widget").fadeToggle('slow');
  	  }
  	  
  	  function showWidget(){
  	    $("#pay_with_amazon_payment_widget").fadeIn('slow');
  	  }
  	  
  	  function hideWidget(){
  	    $("#pay_with_amazon_payment_widget").fadeOut('slow');
  	  }
  	  
  	  function getORODetailsAJAX(callback){
  	    $.post("server/backend.php", 
                  {action: "getOrderReferenceDetails", 
                    data : { orderReferenceId: orderReferenceId
                      , access_token: $("#accesstoken").val()
                    }
                  })
                  .done(function( data ) {
                    var response = JSON.parse(data); 
                      console.log(response.GetOrderReferenceDetailsResult.OrderReferenceDetails.PaymentDescriptor);
                      $("#payment_descriptor_name").html(response.GetOrderReferenceDetailsResult.OrderReferenceDetails.PaymentDescriptor.Name);
                      $("#payment_descriptor_tail").html(response.GetOrderReferenceDetailsResult.OrderReferenceDetails.PaymentDescriptor.AccountNumberTail);
                      if(callback != null){
                        callback();
                      }
                  })
                  .fail(function(error){
                    console.log(error);
                  });
  	  }
  	  
  	  function createOrderReference(callback){
  	    $.post("server/backend.php", 
                  {action: "createOrderReference", 
                    data : { access_token: $("#accesstoken").val() }
                  })
                  .done(function( data ) {
                      console.log(data);
                      if(callback != null){
                        callback(data);
                      }
                  })
                  .fail(function(error){
                    console.log(error);
                  });
  	  }
  	  
  	  function closeOROAjax(callback){
  	    $.post("server/backend.php", 
                  {action: "closeOrderReference", 
                     data : { orderReferenceId: orderReferenceId }
                  })
                  .done(function( data ) {
                      console.log(data);
                      if(callback != null){
                        callback(data);
                      }
                  })
                  .fail(function(error){
                    console.log(error);
                  });
  	  }
  	  
  	  function cancelOROAjax(callback){
  	    $.post("server/backend.php", 
                  {action: "cancelOrderReference", 
                     data : { orderReferenceId: orderReferenceId }
                  })
                  .done(function( data ) {
                      console.log(data);
                      if(callback != null){
                        callback(data);
                      }
                  })
                  .fail(function(error){
                    console.log(error);
                  });
  	  }
  	  
  	  function purchaseAJAX(){
  	    keepWalletOpen = false;
  	    $("#status").html("Processign your payments now...");
  	   $("#status").removeClass("error success", 1000).addClass("info", 1000);
  	    $.post("server/backend.php", 
                  {action: "purchase", 
                    data : { orderReferenceId: orderReferenceId
                        , ordertotal: 1000
                    }
                  })
                  .done(function( data ) {
                    var authResult = JSON.parse(data);
                      console.log(authResult);
                      if(authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.State == "Closed" &&
                        authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "MaxCapturesProcessed") {
                          purchaseSuccessfull(authResult);
                      } else if (authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.State == "Declined" &&
                        authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "InvalidPaymentMethod"){
                        handleIPMDecline(authResult);
                      } else if (authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.State == "Declined"){
                        handleHardDecline(authResult);
                      }
                      
                  })
                  .fail(function(error){
                    console.log(error);
                    $("#payment_message").fadeOut("slow");
                    $("#payment_message_failed").fadeIn("slow");
                  });
  	  }
  	  
  	  function purchaseSuccessfull(authResult){
  	    closeOROAjax();
        $("#payment_message").fadeOut("slow");
        $("#payment_message_success").fadeIn("slow").removeClass("gone").addClass("success");
        $("#buy").fadeOut("slow");
        $("#status").html("Done!");
        $("#status").removeClass("error info", 1000).addClass("success", 1000);
        $("#payment_message_declined_IPM").fadeOut("slow");
        $("#pay_with_amazon_payment_widget").fadeOut('slow');
        // TODO add me in again
        //logout();
  	  }
  	  
  	  function handleIPMDecline(authResult){
  	    // payment descriptor is not available for suspended OROs, this is a hack to keep the widget open in this case
  	    keepWalletOpen = true;
  	    $("#payment_message").fadeOut("slow");
  	    $("#buy").fadeOut("slow");
        setStatusMessage("We received a soft decline on the authorization, please follow the guidance below.");
        $("#status").removeClass("success info", 1000).addClass("error", 1000);
        $("#payment_message_declined_IPM").fadeIn("slow").removeClass("gone").addClass("info");
        
        // re-redner and display the widget
        window.walletWidget.setContractId(orderReferenceId);
        window.walletWidget.bind("pay_with_amazon_payment_widget");
        showWidget();
  	  }
  	  
  	  function handleHardDecline(authResult){
  	    $("#payment_message").fadeOut("slow");
  	    $("#buy").fadeOut("slow");
        setStatusMessage("We received a hard decline on the authorization, please follow the guidance below. ");
        $("#status").removeClass("success error", 1000).addClass("info", 1000);
        $("#payment_message_declined_hard").fadeIn("slow").removeClass("gone").addClass("info");
        hideWidget();
        
        if(authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "TransactionTimedOut"){
          addStatusMessage("Canceling the payment... ");
  	      cancelOROAjax(/*this is the callback for the cancel call*/function(result){
  	        addStatusMessage("Done!");
  	        $("#status").removeClass("error info", 1000).addClass("success", 1000);
  	      });
        }
  	  }
        
    </script>
  	<script async='async' src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>
  	<br />
    <a href="#" onclick="logout(true)">Logout </a>	
    <ul>
      <li>how to detect closing of popup?</li>
    </ul>
    </p>

  </body>
</html>