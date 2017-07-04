<?php
require_once 'config.php';
?>
<html>
  <head>
 <title>Redirect - Experience</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  

      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/base-min.css">
      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
  </head>
<body>
  <h1>Redirect experience</h1>
  <p>
    This button will ask you to sign-in using the redirect experience. The integration follows the best practices given in the official AmazonPay integration guide.
    <br />Exception: The state parameter is fixed in this sample. For security reasons this should not be the case, please check the Request Frogery section of the guide for this.
  </p>
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
          type:  "PwA",
          color: "Gold",
          size:  "large",
          language: "en-gb",

          authorization: function() {
            loginOptions =
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: false, state: "this should not be fixed"};
              authRequest = amazon.Login.authorize (loginOptions, "widgets.php");
          },
          onError: function(error) {
            console.log(error.getErrorMessage());
          }
        });
     }

</script>
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
