<?php
require_once("lpa.config.php");

?>
<html>
	<head>
		<style type="text/css">
			#input {
				width: 66%; 
				margin-left:auto; 
				margin-right:auto;
			}
		
		</style>
		
		<script type='text/javascript'>
		  window.onAmazonLoginReady = function() {
			amazon.Login.setClientId('<?php echo $clientId; ?>');
		  };
		</script>
		<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'></script>

	</head>
	<body>
		<div id="input">
			<form method="get" action="">
				<table width="100%">
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="3" align="left">Please select one or more scopes (use STRG for multiselect, or STRG+A for all)</td>
						<td>
					</tr>
					<tr>
						<td align="right">Scopes</td>
						<td>&nbsp;</td>
						<td>
							<select name="scopes[]" id="scopes" multiple="multiple" size="5">
								<option value="payments:widget" selected="selected">payments:widget</option>
								<option value="profile" selected="selected">profile</option>
								<option value="postal_code">postal_code</option>
								<option value="payments:shipping_address" selected="selected">payments:shipping_address</option>
								<option value="payments:billing_address" selected="selected">payments:billing_address</option>
							</select>
						</td>
						<td>Width:</td>
							
						<td><input type="text" id="width" name="width" value="200" />px</td>
						<td>Height:</td>
						<td><input type="text" id="height" name="height" value="50" />px</td>
						
						<td><input type="checkbox" name="aspect" id="aspect" checked="checked" value="true">Keep aspect</input></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align = "left"><button type="submit">Set up</button></td>
					</tr>
				</table>
			</form>
		</div>

<?php
	if(isset($_GET['scopes'])){
		$scopes = $_GET['scopes'];
		$scopeString = "";
		foreach($scopes as $scope){
			$scopeString .= $scope;
			$scopeString .= " ";
		}
		
		$protocol = "https";
		if($_SERVER['SERVER_NAME'] == "localhost"){
			$protocol = "http";
		}
		$redirectUrl = $protocol."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$last = strrpos($redirectUrl, "/");
		$redirectUrl = substr($redirectUrl, 0, $last);
		$redirectUrl .= "/selectAddress.php";
	
?>
	<div id="AmazonPayButton"/>
	<script type="text/javascript">
	// make sure e are logged out
	amazon.Login.logout();
	  var authRequest;
	  OffAmazonPayments.Button("AmazonPayButton", "<?php echo $merchantId; ?>", {
		type:  "PwA",
		color: "Gold",
<?php
	if($_GET['width'] === "" || $_GET['height'] === ""){
		echo "size:  \"large\",";
	} else {
		if($_GET['width'] != ""){
			echo "width: ".$_GET['width'].",";
		}
		if($_GET['height'] != ""){
			echo "height: ".$_GET['height'].",";
		}
		$ignoreAspect = !isset($_GET['aspect']) || $_GET['aspect'] != true;
		if($ignoreAspect){
			echo "aspectRatio: false,";
		} else {
			echo "aspectRatio: true,";
		}
	}
?>

			authorization: function() {
		  loginOptions =
			{scope: "<?php echo $scopeString; ?>", popup: true};
		  authRequest = amazon.Login.authorize (loginOptions, "<?php echo $redirectUrl; ?>");
		},
		onError: function(error) {
		  // your error handling code
		}
	  });
	</script>
<?php
}
?>		
	</body>
</html>