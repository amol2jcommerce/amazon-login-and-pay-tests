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
        amazon.Login.setClientId("<your marketplace client-id>");	
      };
	  
      window.onAmazonPaymentsReady = function() {
         renderMPButton();
         renderMerchantButton();
      };
      
      function signedIn(){
        
      }

    function renderMPButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button", "<your marketplace merchant id>", {
          type:  "LwA",
          color: "Gold",
          size:  "large",
          language: "en-gb",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: true};
            authRequest = amazon.Login.authorize (loginOptions, signedIn);
          },
          onError: function(error) {
            console.log(error);
          },
          onSignIn: function(oro){
            var oroId= oro.getAmazonOrderReferenceId()
            console.log(oroId);
            document.location = "success.php?oro=" + oroId;
          }
        });
     }
     
     function renderMerchantButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button_2", "<your merchant's merchantid>", {
          type:  "PwA",
          color: "Gold",
          size:  "large",
          language: "de-DE",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: true};
            authRequest = amazon.Login.authorize (loginOptions, signedIn);
          },
          onError: function(error) {
            console.log(error);
          },
           onSignIn: function(oro){
            var oroId= oro.getAmazonOrderReferenceId()
            console.log(oroId);
            document.location = "success.php?oro=" + oroId;
          }
        });
     }

</script>
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
