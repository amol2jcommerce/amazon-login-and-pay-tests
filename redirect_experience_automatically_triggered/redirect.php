<?php
require_once 'config.php';
?>
<html>
<body>
	<script>
      window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
        amazon.Login.setUseCookie(true);
        amazon.Login.setRegion(amazon.Login.Region.Europe);
        amazon.Login.setSandboxMode(true);
      };
	  
      window.onAmazonPaymentsReady = function() {
         startRedirect();
      };

    var startRedirect = function() {
      loginOptions = {
        scope : "profile payments:widget payments:shipping_address payments:billing_address",
				popup : false,
				state: "this should now be static for security reasons"
			};
			amazon.Login.authorize(loginOptions, "widgets.php");
		};
			

</script>
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
