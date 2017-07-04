<?php 
require '../vendor/autoload.php';
require_once '../config.php';
//require_once "Logger.php";

$client = new AmazonPay\Client($config);
//$logger = new \PSR\Log\Logger();
//$client->setLogger($logger);

$action = $_POST['action'];

switch($action){
    case 'placeOrder':
        $total = $_POST['params']['orderTotal'];
        $currency = $_POST['params']['currency'];
        $sellerOrderId = $_POST['params']['sellerOrderId'];
        $storeName = $_POST['params']['storeName'];
        $contractId = $_POST['params']['contract'];
        
        setOrderReferenceDetails($contractId, $total, $currency);
        
        confirmOrderReference($contractId);
        
        authorizeAndCapture($contractId, $total, $currency, $referenceId = null);
        
        closeOrderReference($contractId);
        
        $details = getOrderReferenceDetails($contractId);
        echo $details->toJson();
        
        break;
    default: echo "not implemented yet";
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
	$requestParameters['access_token'] = $accessToken; // use this one to benefit from the new scope
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
	
    $response = $client->authorize($requestParameters);
    
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


function getCurrencyMatchingMarketplace(){
    global $config;
    $currency = "USD";
    switch($config['region']){
        case 'DE':
            $currency = "EUR";
            break;
        case 'UK':
            $currency = "GBP";
            break;
        case 'NA':
            $currency = "USD";
            break;
        case 'FE':
            $currency = "JPY";
            break;
    }
    return $currency;
}