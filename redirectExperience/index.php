<?php
require_once 'config.php';
?>
<html>
  <head>
 <title>Redirect - button</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  
  <style type="text/css">
        /* Please include the min-width, max-width, min-height and max-height	 */
        /* if you plan to use a relative CSS unit measurement to make sure the */
        /* widget renders in the optimal size allowed.							           */

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
  </head>
<body>

	<div id="login_with_amazon_button"></div>
	
	<script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
		amazon.Login.setUseCookie(true);
      };
	  
      window.onAmazonPaymentsReady = function() {
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
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: false, state: "bubu"};
            authRequest = amazon.Login.authorize (loginOptions, "widgets.php");
          },
          onError: function(error) {
            console.log(error);
          }
        });
     }

</script>
	<script async='async' src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
