<?php
include("config.inc.php");
require_once("SDK-php-1.0.11_DE/src/OffAmazonPaymentsService/Samples/.config.inc.php");
?>
<html>
	<head>
		<style type="text/css">
			#information {
				width: 66%; 
				margin-left:auto; 
				margin-right:auto;
				background: #dddd11;
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
		$oro = $_GET['oro'];
		
		function getConsentInformation(){
			global $accessToken;
			global $clientId;
			$c = curl_init('https://api.sandbox.amazon.de/auth/o2/tokeninfo?access_token='.urlencode($accessToken));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			// hack: only set, so no valid ca-file is neccessary
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	/* debug
			curl_setopt($c, CURLOPT_VERBOSE, true);
			$verbose = fopen('temp', 'a');
			curl_setopt($c, CURLOPT_STDERR, $verbose);
	*/
			$r = curl_exec($c);
			curl_close($c);
			$d = json_decode($r);
			
			if ($d->aud != $clientId) {
			  // the access token does not belong to us
			  header('HTTP/1.1 404 Not Found');
			  echo 'Page not found';
			  exit;
			}
			
			// exchange the access token for user profile
			$c = curl_init('https://api.sandbox.amazon.de/user/profile');
			curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer ' . $accessToken));
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			// hack: only set, so no valid ca-file is neccessary
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
			$r = curl_exec($c);
			curl_close($c);
			echo $r;
			$d = json_decode($r);
			$html = "<table><tr><td width='50%'>user_id</td><td>";
			if(isset($d->user_id)) $html.= $d->user_id;
			$html .= "</td></tr><tr><td>email</td><td>";
			if(isset($d->email)) $html  .= $d->email;
			$html .= "</td></tr><tr><td>name</td><td>";
			if(isset($d->name)) $html .= $d->name;
			$html .= "</td></tr><tr><td>postal_code</td><td>";
			if(isset($d->postal_code)) $html .= $d->postal_code;
			$html .= "</td></tr></table>";
			
			return str_replace("\"", "", $r);
		}
		getConsentInformation();		
		
		// create the client to issue the getORODetails call
		$client = new OffAmazonPaymentsService_Client();
		echo ("<br /><br />Trying to get the selected delivery address ...\n\n<br /><br />");
		$getOrderReferenceDetailsRequest = new OffAmazonPaymentsService_Model_GetOrderReferenceDetailsRequest();
		$getOrderReferenceDetailsRequest->setSellerId($sellerId); 
		$getOrderReferenceDetailsRequest->setAmazonOrderReferenceId($oro);
		$getOrderReferenceDetailsRequest->setAddressConsentToken($accessToken);
		$referenceDetailsResultWrapper = $client->getOrderReferenceDetails($getOrderReferenceDetailsRequest);
		$physicalDestination = $referenceDetailsResultWrapper->GetOrderReferenceDetailsResult->getOrderReferenceDetails()->getDestination()->getPhysicalDestination();
	
	echo ("Name: \t\t" . $physicalDestination->GetName() . "\n<br />");
	echo ("AddressLine1: \t" . $physicalDestination->GetAddressLine1() . "\n<br />");	
	echo ("AddressLine2: \t" . $physicalDestination->GetAddressLine2() . "\n<br />");	
	echo ("AddressLine3: \t" . $physicalDestination->GetAddressLine3() . "\n<br />");		
	echo ("City: \t\t" . $physicalDestination->GetCity() . "\n<br />");
	echo ("County: \t\t" . $physicalDestination->GetCounty() . "\n<br />");	
	echo ("District: \t\t" . $physicalDestination->GetDistrict() . "\n<br />");		
	echo ("StateOrRegion: \t\t" . $physicalDestination->GetStateOrRegion() . "\n<br />");		
	echo ("PostalCode: \t" . $physicalDestination->GetPostalCode() . "\n<br />");
	echo ("CountryCode: \t" . $physicalDestination->GetCountryCode() . "\n<br />");
	echo ("Phone: \t\t" . $physicalDestination->GetPhone() . "\n\n<br /><br />");	
	echo ("\n<br>Retrieved selected address.\n<br>");
	}
?>
<br />
<a href = "index.php">Start over again </a>
		
	</body>
</html>