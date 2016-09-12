<?php
require_once 'config.php';
?>
<html>
  <head>
 <title>Cancel orderReference sample</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  
  <style type="text/css">
        /* Please include the min-width, max-width, min-height and max-height	 */
        /* if you plan to use a relative CSS unit measurement to make sure the */
        /* widget renders in the optimal size allowed.							           */



      .hidden {
        visibility: hidden;
      }
      
      .button, bigButton {
        background-color: orange;
        width: 150px;
        height: 32px;
        text-align: center;
        vertical-align: middle;
        line-height: 32px;
        cursor: pointer;
      }
      
      .bigButton {
        width: 450px;
      }


        #login_with_amazon_address_widget {min-width: 300px; max-width: 600px; min-height:
        228px; max-height: 400px;}
        #login_with_amazon_payment_widget {min-width: 300px; max-width:600px; min-height: 228px;
        max-height: 400px;}

        /* Smartphone and small window */
        #login_with_amazon_address_widget {width: 100%; height: 228px;}
        #login_with_amazon_payment_widget {width: 100%; height: 228px;}

        /* Desktop and tablet */
        @media only screen and (min-width: 768px) {
            #login_with_amazon_address_widget {width: 400px; height: 228px;}
            #login_with_amazon_payment_widget {width: 400px; height: 228px;}
        }
</style>
<script type='text/javascript' src='https://code.jquery.com/jquery-3.1.0.js'></script>
  </head>
<body>
<h1>Sandbox simulations</h1>
  <p>
    This sample will help to understand the effects of canceling an order reference.<br />
  </p>
	<div id="login_with_amazon_button"></div>
	<br />
	<div id="login_with_amazon_address_widget"></div>
	<br />
	<div id="login_with_amazon_payment_widget"></div>
	<br />
	
	<br />
	<div id="confirmOrder" class="hidden button">Confirm Order</div>
	<br />
	<div id="chargeOrder" class="hidden button">Charge Order</div>
	<br />
	<div id="cancelOrder" class="bigButton">Cancel Order</div>
	<br />
	
	
	
	
	<script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
      };
      window.onAmazonPaymentsReady = function() {
         $("#confirmOrder").click(confirmOrder);
         $("#chargeOrder").click(chargeOrder);
         $("#cancelOrder").click(cancelOrder);
         renderButton();
      };

    function renderButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button", "<?php echo $config['merchant_id']; ?>", {
          type:  "LwA",
          color: "Gold",
          size:  "large",
          language: "en-gb",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: "true"};
            authRequest = amazon.Login.authorize (loginOptions, customerAuthorized);
          },
          onError: function(error) {
            console.log(error);
          }
        });
     }

	 function customerAuthorized(result){
		console.log(result);
		renderAddressWidget();
		$("#logout").removeClass("hidden");
	 }
      
      var paymentRendered = false;
      var orderReferenceId;
      
      function renderAddressWidget(){
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: "<?php echo $config['merchant_id']?>",
            onOrderReferenceCreate: function(orderReference) {
              console.log("in address widget");
              console.log(orderReference);
              orderReferenceId = orderReference.getAmazonOrderReferenceId();
            },
            onAddressSelect: function(orderReference) {
              if(!paymentRendered){
                  renderPaymentWidget();
              }
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error);
            }
          }).bind("login_with_amazon_address_widget");
      }
      
      
      function renderPaymentWidget(){
        new OffAmazonPayments.Widgets.Wallet({
            sellerId: "<?php echo $config['merchant_id']?>",
            onPaymentSelect: function(event) {
              // Replace this code with the action that you want to perform
              // after the payment method is selected.
              console.log("in address widget");
              console.log(event);
              $("#confirmOrder").removeClass("hidden");
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error);
            }
          }).bind("login_with_amazon_payment_widget");
          
          paymentRendered = true;
      }
	  
	  function logout(){
		amazon.Login.logout();
	  }
	  
	  function displayError(error){
        console.log(error);
    }
	  
	  function confirmOrder(){
	    $.post("backend.php", {action: "confirmOrder", data :{ orderReferenceId: orderReferenceId} }).done(function( data ) {
        console.log(data);
       
       $("#chargeOrder").removeClass("hidden");
       
      }).fail(function(error){
        displayError(error);
      });
	    
	  }
	  
	   function chargeOrder(){
	    $.post("backend.php", {action: "chargeOrder", data :{ orderReferenceId: orderReferenceId} }).done(function( data ) {
        console.log(data);
       
      }).fail(function(error){
        displayError(error);
      });
	    
	  }
	  
	   function cancelOrder(){
	    $.post("backend.php", {action: "cancelOrder", data :{ orderReferenceId: orderReferenceId} }).done(function( data ) {
        console.log(data);
       
       
      }).fail(function(error){
        displayError(error);
      });
	    
	  }
      
</script>
	<script async='async' src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

<a id="logout" class="hidden" href="#" onclick="logout()">Logout </a>	

  </body>
</html>