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
} else if($action === "closeOrderReference"){
	$oroId = $data['orderReferenceId'];
	closeOrderReference($oroId);
}  else if($action === "cancelOrderReference"){
	$oroId = $data['orderReferenceId'];
	cancelOrderReference($oroId);
} else if($action === "purchase"){
	$oroId = $data['orderReferenceId'];
	$amount = $data['ordertotal'];
	
	$confirm = confirmOrderReference($oroId);
//	print_r($confirm);
    
    $response = authorizeAndCapture($oroId, $amount, "GBP");

    echo $response->toJson();
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
	$requestParameters['address_consent_token'] = $accessToken;
	
	return $client->getOrderReferenceDetails($requestParameters);
}

function getCaptureDetails($captureId){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_capture_id'] = $captureId;
	
	return $client->getCaptureDetails($requestParameters);
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
	
    $response = $client->authorize($requestParameters);
    
    // also check the capture details
    $details = $response->toArray();
    getCaptureDetails($details['AuthorizeResult']['AuthorizationDetails']['IdList']['member']);
    
    return $response;
}

function closeOrderReference($oroId){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	return $client->closeOrderReference($requestParameters);
}

function cancelOrderReference($oroId){
	global $client;
	$requestParameters = prepareRequestParameters();
	$requestParameters['amazon_order_reference_id'] = $oroId;
	return $client->cancelOrderReference($requestParameters);
}
    
    
?>
    