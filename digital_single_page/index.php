<?php
require_once 'config.php';
?>
<html>
  <head>
    <title>Smooth digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  
    <style type="text/css">
        /* Please include the min-width, max-width, min-height and max-height	 */
        /* if you plan to use a relative CSS unit measurement to make sure the */
        /* widget renders in the optimal size allowed.							           */

        #login_with_amazon_payment_widget {min-width: 300px; max-width:600px; min-height: 228px;
        max-height: 400px;}

        /* Smartphone and small window */
        #login_with_amazon_payment_widget {width: 100%; height: 228px;}

        /* Desktop and tablet */
        @media only screen and (min-width: 768px) {
            #login_with_amazon_payment_widget {width: 400px; height: 228px;}
        }
        
        .gone {
          display: none;
        }
        
        .offscreen {
          position: absolute; top: -9999px; left: -9999px;
        }
        
             
        
        /* Start by setting display:none to make this hidden.
           Then we position it in relation to the viewport window
           with position:fixed. Width, height, top and left speak
           for themselves. Background we set to 80% white with
           our animation centered, and no-repeating */
        .modal {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            z-index:    1000;
            background: rgba( 255, 255, 255, .8 ) 
                        url('https://i.stack.imgur.com/FhHRx.gif') 
                        50% 50% 
                        no-repeat;
        }
        
        .modalcontainer {
          position: relative;
        }
        
        /* When the body has the loading class, we turn
           the scrollbar off with overflow:hidden */
        body.loading {
            overflow: hidden;   
        }
        
        /* Anytime the body has the loading class, our
           modal element will be visible */
        body.loading .modal {
            display: block;
        }
        
        .button, .info, .success, .error {
            background-color: #ff9900;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: bold;
            display: inline-block;
            font-size: 18px;
            cursor: pointer;
        }
        
        .info, .success, .error {
          color: black;
          text-align: left;
        }
        
        .info {
          background-color: #e3f7fc;
        }
        
        .success {
          background-color: #e9ffd9;
        }
        
        .error {
          background-color: #ffecec;
        }
        
        .status {
          font-size: 12pt;
          font-style: italic;
          width: 100%;
        }
        
        .button:hover {
          background-color: #ffaa11;
        }
        
        img.blur {
            filter: url(blur.svg#blur);
            -webkit-filter: blur(1px) grayscale(100%);
            filter: blur(1px) grayscale(100%);
            filter: progid: DXImageTransform.Microsoft.Blur(PixelRadius='2');
        
            z-index: -2;
        }
        
        .clickable {
            cursor: pointer;
        }
        
        #payment_descriptor_name, #payment_descriptor_tail {
          font-style: italic;
        }

    </style>
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
      Unfortunately something went wrong with your pyments. Please try again. If the issue persists, please get in touch.
    </p>
    <div id="login_with_amazon_payment_widget" class="offscreen"></div>
    
    <div id="buy" name="buy" class="gone">Complete purchase</div>
    <input type="hidden" name="accesstoken" id="accesstoken" />
  	
	  <script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
      };
      window.onAmazonPaymentsReady = function() {
         renderButton();
         $("#status").html("Please sign in using your Amazon account.");
      };

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
              console.log(error);
            }
          });
          $("#login_with_amazon_button img").fadeIn("slow", function() {
            $(this).removeClass("blur").addClass("clickable");
          });
       }
  
  	 function customerAuthorized(result){
  		console.log(result);
  		if(result.error){
  		  console.log("no consent or similar");
  		  $("#status").html("Authentication failed, please accept all consents to go on.");
  		} else {
  		  $("#accesstoken").val(result.access_token);
  		  $("#login_with_amazon_button").fadeOut("slow", renderPaymentWidget);
  		  $("#status").html("Welcome! We are preparing your oder now: ");
  		  //createOrderReference();
  		}
  		$("body").removeClass("loading");
  	 }
        
      var orderReferenceId;
      function renderPaymentWidget(){
        new OffAmazonPayments.Widgets.Wallet({
            sellerId: '<?php echo $config["merchant_id"]?>',
            scope: "profile payments:widget payments:shipping_address payments:billing_address payments:instrument_descriptor",
            onOrderReferenceCreate: function(orderReference) {
              orderReferenceId = orderReference.getAmazonOrderReferenceId();
              console.log(orderReferenceId);
              $("#status").html($("#status").html() + "setting order details... ");
              $.post("server/backend.php", 
                {action: "setOrderReferenceDetails", 
                  data : { orderReferenceId: orderReferenceId
                    , ordertotal: 1000
                  }
                })
                .done(function( data ) {
                  $("#status").html($("#status").html() + "retreiving data from Amazon... ");
                    getORODetailsAJAX(function(){
                      $("#payment_message").fadeIn("slow").addClass("success");
                      $("#buy").removeClass("gone").addClass("button", 1000);
                      $("#buy").click(purchaseAJAX);
                      $("#status").html($("#status").html() + "Done! ");
                      $("#status").removeClass("info", 1000).addClass("success", 1000);

                    });
                })
                .fail(function(error){
                   $("#status").html("An unexpected error occured.");
                   $("#status").removeClass("info", 1000).addClass("error", 1000);
                  console.log(error);
                });
            },
            onPaymentSelect: function(orderReference) {
              console.log("payment select");
              
              var callback = null;
              // only after placing the widget in the visual area (we set the class gone at that time), react
              if($("#login_with_amazon_payment_widget").hasClass("gone")){
                toggleWidget();
                $("#status").html("Payment details changed. ");
                $("#status").removeClass("error success", 1000).addClass("info", 1000);
                $("#status").html($("#status").html() + "Refreshing data from Amazon... ");
                callback = function(){
                  $("#status").html($("#status").html() + "Done!");
                  $("#status").removeClass("error info", 1000).addClass("success", 1000);
                };
              } else {
                // now that the widget was rendered and we have an ORO, make it gone, but in the desired spot
                $("#login_with_amazon_payment_widget").addClass("gone").removeClass("offscreen");
              }
              getORODetailsAJAX(callback);
            },
            onReady: function(){
              $("#toggleWidget").click(toggleWidget);
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error);
            }
          }).bind("login_with_amazon_payment_widget");
      }
  	  
  	  function logout(reload){
  		  amazon.Login.logout();
  		  if(reload){
  		    location.reload();
  		  }
  	  }
  	  
  	  function toggleWidget(){
  	    $("#login_with_amazon_payment_widget").fadeToggle('slow');
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
                        callback();
                      }
                  })
                  .fail(function(error){
                    console.log(error);
                  });
  	  }
  	  
  	  function purchaseAJAX(){
  	     $("#status").html("Processign your payments now...");
  	     $("#status").removeClass("error success", 1000).addClass("info", 1000);
  	    $.post("server/backend.php", 
                  {action: "purchase", 
                    data : { orderReferenceId: orderReferenceId
                        , ordertotal: 1000
                    }
                  })
                  .done(function( data ) {
                      console.log(data);
                      $("#payment_message").fadeOut("slow");
                      $("#payment_message_success").fadeIn("slow").removeClass("gone").addClass("success");
                      $("#buy").fadeOut("slow");
                      $("#status").html("Done!");
                      $("#status").removeClass("error info", 1000).addClass("success", 1000);
                      logout();
                  })
                  .fail(function(error){
                    console.log(error);
                    $("#payment_message").fadeOut("slow");
                    $("#payment_message_failed").fadeIn("slow");
                  });
  	  }
        
    </script>
  	<script async='async' src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>
  	<br />
    <a href="#" onclick="logout(true)">Logout </a>	
    <br />
    <br />
    <br />
    <br />
        <br />
    <br />
    <br />
    <br />
        <br />
    <br />
    <br />
    <br />
    <p>
      TODO:
      <ul>
        <li>creteORO call?</li>
        <li>detect closing of popup</li>
      </ul>
    </p>

  </body>
</html>