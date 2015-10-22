<?php
require_once '../config.php';
?>
<div id="login_with_amazon_button"></div>
    <script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
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
              {scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: "true"};
            authRequest = amazon.Login.authorize (loginOptions, redirect());
          },
          onError: function(error) {
            console.log(error);
          }
        });
      }
      
      function redirect(){
          window.location.href= "widgets.php";
      }
      
    </script>

<script async='async' type='text/javascript' 
src='https://static-eu-beta.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'>
</script> 