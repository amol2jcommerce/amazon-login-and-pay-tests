<?php
require_once 'config.php';
?>
<html>
  <head>
    <title>Standard digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" href="main.css" type="text/css" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  </head>
  <body>

    <p />
  	<div id="login_with_amazon_button"></div>
  	
  	<p id="payment_message" class="gone">
      Thanks for using Amazon Pay.<br /><br /> Please select your payment instrument below.
    </p>
    <p id="payment_message_success" class="gone">
      Thanks for your purchase. For security reasons you have been disconnected from your Amazon account.
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
    <div id="pay_with_amazon_payment_widget"></div>
    
    <div id="buy" name="buy" class="gone">Complete purchase</div>
    <input type="hidden" name="accesstoken" id="accesstoken" />
  	
	  <script>
      window.onAmazonLoginReady = function(){
        // set the client id
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
      };
      
      window.onAmazonPaymentsReady = function() {
        //render the pay button
         renderButton();
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
                {scope: "payments:widget", popup: "true"};
                
              authRequest = amazon.Login.authorize (loginOptions, customerAuthorized);
            },
            onError: function(error) {
              console.log(error.getErrorMessage());
            }
          });
       }
  
  
     /**
      * method called when and if the customer authorized or denied consent
      */
  	 function customerAuthorized(result){
  	   // something went wrong here, e.g. no consent. Please add handling
  		if(result.error){
  		  // do something
  		  
  		} else {
  		  $("#accesstoken").val(result.access_token);
  		  
  		  // this hides the button and renders the widget as a callback after the fading.
  		  $("#login_with_amazon_button").fadeOut("slow", renderPaymentWidget);
  		}
  	 }
        
      var orderReferenceId;
      
      /**
       * render the payment widget
       */
      function renderPaymentWidget(){
        window.walletWidget = new OffAmazonPayments.Widgets.Wallet({
            sellerId: '<?php echo $config["merchant_id"]?>',
            scope: "payments:widget",
            onOrderReferenceCreate: oroCreateCallback,
            onPaymentSelect: paymentSelectCallback,
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
        // store the orderReference. You will need this id for all subsequent transactions
        orderReferenceId = orderReference.getAmazonOrderReferenceId();
        
        // set the details of the transaction, amount and currency is the only mandatory information.
        //Currency hardcoded in the backend here.
        $.post("server/backend.php", 
          {action: "setOrderReferenceDetails", 
            data : { orderReferenceId: orderReferenceId
              , ordertotal: 1
            }
          })
          .done(function( data ) {
            $("#payment_message").fadeIn("slow").addClass("success");
            // add click handler for the buy button to the purchae function
            $("#buy").click(purchaseAJAX);
          })
          .fail(function(error){
             console.log(error.getErrorMessage());
          });
      }
      
      /**
       * This is the handler which is called when a payment instrument was selected. Main task: update the button to place the order.
       */
      function paymentSelectCallback(orderReference) {
        // UI handle buy button visuals
        $("#buy").removeClass("gone").addClass("button", 1000);
        $("#buy").fadeIn("slow");
        
        // retrieve details about the transaction. This is only necessary
        getORODetailsAJAX();
      }

      /**
       * This is the way to logout a customer from Amazon Pay
       */
  	  function logout(reload){
  		  amazon.Login.logout();
  		  if(reload){
  		    location.reload();
  		  }
  	  }
  	  
  	  
  	  /**
  	   * AJAX call to retrieve information about the transaction
  	   */
  	  function getORODetailsAJAX(){
  	    $.post("server/backend.php", 
          {action: "getOrderReferenceDetails", 
            data : { orderReferenceId: orderReferenceId
              , access_token: $("#accesstoken").val()
            }
          })
          .done(function( data ) {
            var response = JSON.parse(data); 
            // JSON response can be used to parse the data returned - optional
          })
          .fail(function(error){
            console.log(error);
          });
  	  }
  	  
  	  
  	  /**
  	   * AJAX call to close a payment once it was completed
  	   */
  	  function closeOROAjax(){
  	    $.post("server/backend.php", 
          {action: "closeOrderReference", 
             data : { orderReferenceId: orderReferenceId }
          })
          .done(function( data ) {
              console.log(data);
          })
          .fail(function(error){
            console.log(error);
          });
  	  }
  	  
  	  /**
  	   * AJAX call to cancel a payment once it was completed
  	   */
  	  function cancelOROAjax(){
  	    $.post("server/backend.php", 
          {action: "cancelOrderReference", 
             data : { orderReferenceId: orderReferenceId }
          })
          .done(function( data ) {
              console.log(data);
          })
          .fail(function(error){
            console.log(error);
          });
  	  }
  	  
  	  /**
  	   * AJAX call to purchase. Executes COnfirm AUthorize and Capture at once.
  	   * Also handles the declines.
  	   */ 
  	  function purchaseAJAX(){
  	    $.post("server/backend.php", 
          {action: "purchase", 
            data : { orderReferenceId: orderReferenceId
                , ordertotal: 1
            }
          })
          .done(function( data ) {
            var authResult = JSON.parse(data);
            console.log(authResult);
              // If the Authorization was closed with the reasonCode MaxCapturesProcessed, the payment was SUCCESSFUL
              if(authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.State == "Closed" &&
                authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "MaxCapturesProcessed") {
                  purchaseSuccessfull(authResult);
                  
                // InvalidPaymentMethod declines, can be handled in the frontend, will re-display the widget and infrom the customer
              } else if (authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.State == "Declined" &&
                authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "InvalidPaymentMethod"){
                handleIPMDecline(authResult);
                
                // For other reasons, decline the payment, Amazon Pay cannot be used here.
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
  	  
  	  
  	  /**
  	   * Handler for completed payments.
  	   * Main Task: Close the OrderReference, update the UI and log the customer out.
  	   */ 
  	  function purchaseSuccessfull(authResult){
  	    
  	    // this is essential. Always close an orderReference once the payment was done.
  	    closeOROAjax();
  	    
  	    // UI handling 
        $("#payment_message").fadeOut("slow");
        $("#payment_message_success").fadeIn("slow").removeClass("gone").addClass("success");
        $("#buy").fadeOut("slow");
        $("#payment_message_declined_IPM").fadeOut("slow");
        $("#pay_with_amazon_payment_widget").fadeOut('slow');
        
        // logout from Amazon Pay
        logout();
  	  }
  	  
  	  
  	  /**
  	   * Handle the InvalidPaymentMethodDecline - re-display the wallet widget
  	   */ 
  	  function handleIPMDecline(authResult){
  	    // UI handling
  	    $("#payment_message").fadeOut("slow");
  	    $("#buy").fadeOut("slow");
        $("#payment_message_declined_IPM").fadeIn("slow").removeClass("gone").addClass("info");
        
        // re-redner and display the widget
        window.walletWidget.setContractId(orderReferenceId);
        window.walletWidget.bind("pay_with_amazon_payment_widget");
  	  }
  	  
  	  
  	  /**
  	   * Handle theall other declines
  	   */ 
  	  function handleHardDecline(authResult){
  	    $("#payment_message").fadeOut("slow");
  	    $("#buy").fadeOut("slow");
        $("#payment_message_declined_hard").fadeIn("slow").removeClass("gone").addClass("info");
        
        if(authResult.AuthorizeResult.AuthorizationDetails.AuthorizationStatus.ReasonCode == "TransactionTimedOut"){
          // essential, cancel the transaction here
          cancelOROAjax();
        }
  	  }
        
    </script>
    
  	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'></script>
  	
  	<br />
    <a href="#" onclick="logout(true)">Logout </a>	
   
    </p>

  </body>
</html>