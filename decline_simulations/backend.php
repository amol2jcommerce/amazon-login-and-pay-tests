<?php
require_once 'config.php';

require("PayWithAmazon/Client.php");
require("Logger.php");

$client = new PayWithAmazon\Client($config);

$logger = new \Psr\Log\Logger();
$client->setLogger($logger);

$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
if($action === "processPayment"){
    $oroId = $data['orderReferenceId'];
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['amount'] = "1234";
	$requestParameters['currency_code'] = "GBP";
    
    $client->setOrderReferenceDetails($requestParameters);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
    $client->confirmOrderReference($requestParameters);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['authorization_amount'] = "1234";
	$requestParameters['currency_code'] = "GBP";
	$milliseconds = round(microtime(true) * 1000);
	$requestParameters['authorization_reference_id'] = $oroId.$milliseconds;
	$requestParameters['seller_authorization_note'] = $data["simulationString"];

    $client->authorize($requestParameters);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
    $client->closeOrderReference($requestParameters);
    
    echo "OK";
} 
    ?>
    