<?php
require_once '../config.php';

require("PayWithAmazon/Client.php");
require("Logger.php");

$client = new PayWithAmazon\Client($config);

$logger = new \Psr\Log\Logger();
$client->setLogger($logger);

$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
if($action === "getOrderReferenceDetails"){
    $oroId = $data['orderReferenceId'];
    $accessToken = $data['access_token'];
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['address_consent_token'] = $accessToken;
	
	$response= $client->getOrderReferenceDetails($requestParameters);
    
    print_r($response);
    
} else if($action === "setOrderReferenceDetails"){
	$oroId = $data['orderReferenceId'];
	$amount = $data['ordertotal'];
	
	$requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['amount'] = $amount;
	$requestParameters['currency_code'] = "GBP";
    
    $client->setOrderReferenceDetails($requestParameters);
} else if($action === "purchase"){
	$oroId = $data['orderReferenceId'];
	$amount = $data['ordertotal'];
	
	$requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
    
    $client->confirmOrderReference($requestParameters);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['authorization_amount'] = $amount;
	$requestParameters['currency_code'] = "GBP";
	$milliseconds = round(microtime(true) * 1000);
	$requestParameters['authorization_reference_id'] = $oroId.$milliseconds;
	$requestParameters['transaction_timeout'] = 0;
	$requestParameters['capture_now'] = true;
	

    $client->authorize($requestParameters);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
    $client->closeOrderReference($requestParameters);
    
    echo "OK";
}
    ?>
    