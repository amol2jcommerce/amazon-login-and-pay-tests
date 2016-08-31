<?php
require_once("config.inc.php");

?>
<html>
	<head>
		<style type="text/css">
			
			#addressBookWidgetDiv{
				width: 400px; 
				height: 228px;
			}
		</style>
		
		<script type='text/javascript'>
		  window.onAmazonLoginReady = function() {
			amazon.Login.setClientId('amzn1.application-oa2-client.c99387f150104b63b64919c18c0980a6');
		  };
		</script>
		<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'></script>

	</head>
	<body>
<?php
	if(isset($_GET['access_token'])){
		$accessToken = $_GET['access_token'];
?>

<div id="addressBookWidgetDiv">
</div> 
	<script>
	var oro = '';
		new OffAmazonPayments.Widgets.AddressBook({
		  sellerId: '<?php echo $sellerId; ?>',
		  onOrderReferenceCreate: function(orderReference) {
				   oro = orderReference.getAmazonOrderReferenceId();
		  },
		  onAddressSelect: function(orderReference) {
			console.log("onAddressSelected");
		  },
		  design: {
			 designMode: 'responsive'
		  },
		  onError: function(error) {
		   // your error handling code
		  }
		}).bind("addressBookWidgetDiv");
	</script>
	
	<a href="success.php?access_token=<?php echo $accessToken; ?>" onclick="location.href=this.href + '&oro=' + oro; return false;">weiter</a>
<?php
	
	}
?>
		
	</body>
</html>