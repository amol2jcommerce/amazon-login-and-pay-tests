<?php
require_once '../config.php';

require_once("PayWithAmazon/AmazonPay/Client.php");
require_once("Logger.php");

$client = new AmazonPay\Client($config);

$logger = new \PSR\Log\Logger();
$client->setLogger($logger);

$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
if($action === "getOrderReferenceDetails"){
    $oroId = $data['orderReferenceId'];
    $accessToken = $data['access_token'];
    
    $response = getOrderReferenceDetails($oroId, $accessToken);
    echo $response->toJson();
    
} else if($action === "setOrderReferenceDetails"){
	$oroId = $data['orderReferenceId'];
	$amount = $data['ordertotal'];
	
	$result = setOrderReferenceDetails($oroId, $amount, "GBP");
} else if($action === "purchase"){
	$oroId = $data['orderReferenceId'];
	$amount = $data['ordertotal'];
	
	confirmOrderReference($oroId);
    
    authorizeAndCapture($oroId, $amount, "GBP");
    
    closeOrderReference($oroId);
    
    echo "OK";
} else if("createOrderReference"){
	$accessToken = $data['access_token'];
	$result = createOrderReference($accessToken);
	
	echo $result;
}

function createOrderReference($accessToken){
	global $config;
	$merchantId = $config['merchant_id'];
	$url = "https://payments-eu.amazon.com/api/v1/orderReferences";
	$jsonRequestBody =" {'securityContext': 'eydzZGtWZXJzaW9uJzogJzIuMScsJ3Byb2R1Y3RUeXBlJzogJ1BXQScsJ3N1YlByb2R1Y3RUeXBlJzogJ01PQklMRV9TREsnfQ==','accessToken': '".$accessToken."','associateShippingAddress': true,'merchantId': 'AUFORHVDBNC4T','paymentRegion': 'EU_GBP','merchantPreferenceLanguage': 'en_GB','buyerPreferenceLanguage': 'en_GB'}";
		
	$postBody= "securityContext=eydzZGtWZXJzaW9uJzogJzIuMScsJ3Byb2R1Y3RUeXBlJzogJ1BXQScsJ3N1YlByb2R1Y3RUeXBlJzogJ01PQklMRV9TREsnfQ==&accessToken=".$accessToken."&associateShippingAddress=true&merchantId=".$merchantId."&paymentRegion=EU_GBP&merchantPreferenceLanguage=en_GB&buyerPreferenceLanguage=en_GB";
	$postBody= "accessToken=".$accessToken."&associateShippingAddress=true&merchantId=".$merchantId."&paymentRegion=EU_GBP&merchantPreferenceLanguage=en_GB&buyerPreferenceLanguage=en_GB";
	//open connection
	$ch = curl_init($url);
	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be true!!! TODO
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
	curl_setopt($ch, CURLOPT_HEADER ,0); // DO NOT RETURN HTTP HEADERS
	curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1); // RETURN THE CONTENTS OF THE CALL
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // Timeout on connect (2 minutes)
	
	$result = curl_exec($ch);
	
	$response = json_decode($result, true);
	return $result;
}

function prepareRequestParameters(){
	global $config;
	$requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	return $requestParameters;
}

function getOrderReferenceDetails($oroId, $accessToken = null){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['access_token'] = $accessToken;
//	$requestParameters['address_consent_token'] = $accessToken;
	
	return $client->getOrderReferenceDetails($requestParameters);
}

function setOrderReferenceDetails($oroId, $amount, $currency){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['amount'] = $amount;
	$requestParameters['currency_code'] = $currency;
	
	return $client->setOrderReferenceDetails($requestParameters);
}

function confirmOrderReference($oroId){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	
	return $client->confirmOrderReference($requestParameters);
}

function authorizeAndCapture($oroId, $amount, $currency, $referenceId = null){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['authorization_amount'] = $amount;
	$requestParameters['currency_code'] = $currency;
	if($referenceId == null){
		$milliseconds = round(microtime(true) * 1000);
		$referenceId = $oroId.$milliseconds;
	}
	$requestParameters['authorization_reference_id'] = $referenceId;
	$requestParameters['transaction_timeout'] = 0;
	$requestParameters['capture_now'] = true;
	
    $client->authorize($requestParameters);
}

function closeOrderReference($oroId){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	return $client->closeOrderReference($requestParameters);
}
    
    
?>
    