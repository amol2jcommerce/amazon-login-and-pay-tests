<?php
require_once 'config.php';
?>
<html>
  <head>
 <title>Authorization Simulation Strings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  
  <style type="text/css">
        /* Please include the min-width, max-width, min-height and max-height	 */
        /* if you plan to use a relative CSS unit measurement to make sure the */
        /* widget renders in the optimal size allowed.							           */



      .hidden {
        visibility: hidden;
      }
      
      #placeOrder {
        background-color: orange;
        width: 100px;
        height: 32px;
        text-align: center;
        vertical-align: middle;
        line-height: 32px;
        cursor: pointer;
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
    This sample will help you understand the effect of decline simulations for authorizations.<br />
    You can either use the payment instruments for the simulations in the widgets or paste the simulation string to the field below before placing an order.<br /><br />
    
    A list of available strings can be taken from the <a href="https://payments.amazon.co.uk/developer/documentation/lpwa/201956480">guide</a>. 
  </p>
	<div id="login_with_amazon_button"></div>
	<br />
	<div id="login_with_amazon_address_widget"></div>
	<br />
	<div id="login_with_amazon_payment_widget"></div>
	<br />
	<div id="simulationArea" class="hidden">
  	<h3>Please add your authorization simulation string below or use a payment instrument to simualte a decline</h3>
  	<textarea name="simulationString" id="simulationString" cols="150" rows="8"></textarea>
	</div>
	<br />
	<div id="placeOrder" class="hidden">Place Order</div>
	<br />
	
	
	
	<script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
      };
      window.onAmazonPaymentsReady = function() {
         $("#placeOrder").click(processPayment);
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
            sellerId: '<?php echo $config['merchant_id']?>',
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
            sellerId: '<?php echo $config['merchant_id']?>',
            onPaymentSelect: function(event) {
              // Replace this code with the action that you want to perform
              // after the payment method is selected.
              console.log("in address widget");
              console.log(event);
              showSimulationField();
              $("#placeOrder").removeClass("hidden");
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
	  
	  function showSimulationField(){
	    $("#simulationArea").removeClass("hidden");
	  }
	  
	  function displayError(error){
        console.log(error);
    }
	  
	  function processPayment(){
	    $.post("backend.php", {action: "processPayment", data :{ orderReferenceId: orderReferenceId, simulationString: $("#simulationString").val()} }).done(function( data ) {
        console.log(data);
       
        alert("Please check Seller Central for results. Status page not yet implemented");
        window.location.reload();
      }).fail(function(error){
        displayError(error);
      });
	    
	  }
      
</script>
	<script async='async' src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

<a id="logout" class="hidden" href="#" onclick="logout()">Logout </a>	

  </body>
</html>