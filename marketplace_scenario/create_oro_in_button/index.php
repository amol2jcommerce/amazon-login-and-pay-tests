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
        amazon.Login.setClientId("amzn1.application-oa2-client.8d5f99bf3e31412f97756d7d928a3ce1");	
      };
	  
      window.onAmazonPaymentsReady = function() {
         renderMPButton();
         renderMerchantButton();
      };
      
      function signedIn(){
        
      }

    function renderMPButton(){
        var authRequest;
        OffAmazonPayments.Button("login_with_amazon_button", "ABEHN0MDIQQOW", {
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
        OffAmazonPayments.Button("login_with_amazon_button_2", "A1XCVOHP3URKFF", {
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
