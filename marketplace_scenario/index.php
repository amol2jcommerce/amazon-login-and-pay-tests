<?php
?>
<html>
  <head>
 <title>Marketplace scenario</title>
  </head>
<body>

<p>
  This is the marketplace button <div id="login_with_amazon_button"></div>
  
  You can sign-in to the marketplace with it.
</p>

	<p>
	  This is the merchant button
	<div id="login_with_amazon_button_2"></div>  
	
	>ou can sign in wiht it as well, but if you signed-in against the marketplace, it should not trigger any sign-in request
	</p>
	
	
	<script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("amzn1.application-oa2-client.3af7115907cf43f28c3fa8590cc3ef0b");	
      };
	  
      window.onAmazonPaymentsReady = function() {
         renderMPButton();
         renderMerchantButton();
      };

    function renderMPButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button", "A1P8WV11EWOP9H", {
          type:  "LwA",
          color: "Gold",
          size:  "large",
          language: "en-gb",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: true};
            authRequest = amazon.Login.authorize (loginOptions, "success.php");
          },
          onError: function(error) {
            console.log(error);
          }
        });
     }
     
     function renderMerchantButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button_2", "A3IWQXFKXS2WSY", {
          type:  "PwA",
          color: "Gold",
          size:  "large",
          language: "de-DE",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: true};
            authRequest = amazon.Login.authorize (loginOptions, "success.php");
          },
          onError: function(error) {
            console.log(error);
          }
        });
     }

</script>
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
