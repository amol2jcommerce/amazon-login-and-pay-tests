<?php
require_once 'config.php';

require("PayWithAmazon/Client.php");
require("Logger.php");

$client = new PayWithAmazon\Client($config);

$logger = new \Psr\Log\Logger();
$client->setLogger($logger);

$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
if($action === "confirmOrder"){
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
    
    echo "OK";
} else if($action === "chargeOrder"){
	$oroId = $data['orderReferenceId'];
	
	$requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;
	$requestParameters['authorization_amount'] = "1234";
	$requestParameters['currency_code'] = "GBP";
	$milliseconds = round(microtime(true) * 1000);
	$requestParameters['authorization_reference_id'] = $oroId.$milliseconds;
	$requestParameters['seller_authorization_note'] = $data["simulationString"];
	$requestParameters['transaction_timeout'] = 0;
	$requestParameters['capture_now'] =true;

    $client->authorize($requestParameters);
    
    echo "OK";
	
} else if($action === "cancelOrder"){
	$oroId = $data['orderReferenceId'];
	$requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_order_reference_id'] = $oroId;

    $client->cancelOrderReference($requestParameters);
    
    echo "OK";
	
}
    ?>
    