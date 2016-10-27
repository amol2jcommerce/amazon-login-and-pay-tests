<?php
namespace PayWithAmazon;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once '../config.php';
require("../PayWithAmazon/Client.php");
require("../Logger.php");

$client = new Client($config);

$logger = new \Psr\Log\Logger();
$client->setLogger($logger);

$action = $_REQUEST['action'];
$data = $_REQUEST['data'];
if($action === "setBillingAgreementId"){
    $_SESSION['billingAgreementId'] = $data['billingAgreementId'];
    echo "OK";
} else if($action === "calculateShipping"){
    calculateShipping($data);
} else if($action === "setBillingAgreementDetails"){
    setBillingAgreementDetails($data);
} else if($action === "confirmBillingAgreement"){
    confirmBillingAgreement($data);
} else if($action === "validateBillingAgreement"){
    validateBillingAgreement($data);
} else if($action === "authorizeOnBillingAgreement"){
    authorizeOnBillingAgreement($data);
} else if($action === "cleanUp"){
    cleanUp($data);
}else if($action === "createOrderReferenceForId"){
    createOrderReferenceForId($data);
}

function cleanUp($data = null){
    session_destroy(); 
    unlink("IPN.log");
}

function calculateShipping($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    $addressConsentToken = $data['accessToken'];
    
    $details = getBillingAgreementDetails();
    
    $destination = $details['Destination']['PhysicalDestination'];

    // simulating we are shipping world wide, only DE is more expensive, the rest costs 3.99 fixed
    $shippingCosts = "3.99";

    $countryCode = $destination['CountryCode'];
    if($countryCode ==="DE"){
        $shippingCosts = "5.99";
    }
    echo '{"shippingCosts" : "'.$shippingCosts.'"}';

}

function getBillingAgreementDetails($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    $consentToken = $data["consentToken"];
    global $client;
    global $config;
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_billing_agreement_id'] = $billingAgreementId;
	
	// get the full amount of the authorization
	$response = $client->getBillingAgreementDetails($requestParameters);

    $billingAgreementDetails = $response->toArray()['GetBillingAgreementDetailsResult']['BillingAgreementDetails'];
    return $billingAgreementDetails;    
}

function setBillingAgreementDetails($data = null){
    $billingAgreementDetails = getBillingAgreementDetails($data);
    $state = $billingAgreementDetails['BillingAgreementStatus']['State'];
    if($state === "Draft"){
        $sellerOrderId = $data['sellerOrderId'];
        $storeName = $data['storeName'];
        $billingAgreementId = $_SESSION['billingAgreementId'];
        global $client;
        global $config;



        $requestParameters['merchant_id'] = $config['merchant_id'];
        $requestParameters['amazon_billing_agreement_id'] = $billingAgreementId;
//        $requestParameters['amount'] - [String]
//        $requestParameters['currency_code'] - [String]
        $requestParameters['seller_billing_agreement_id'] = $sellerOrderId;
        $requestParameters['store_name'] = $storeName;

        $client->setBillingAgreementDetails($requestParameters);
        echo "set details to the agreement";
    } else{
        echo "agreement already open, skipping";
    }
}

function confirmBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $config;
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_billing_agreement_id'] = $billingAgreementId;
    $response = $client->confirmBillingAgreement($requestParameters);
    echo "billing agreement confirmed";
}

function validateBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $config;
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_billing_agreement_id'] = $billingAgreementId;
    
    $response = $client->validateBillingAgreement($requestParameters);
    $result = $response->toArray();
    
    $state = $result['BillingAgreementStatus']['State'];
    $validationResult = $result['ValidationResult'];
    if($state === "Open" && $validationResult === "Success"){
        echo "OK";
    } 
    //else {
    //    header('HTTP/1.0 400 Bad Request'); 
    //}
}

function authorizeOnBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $config;
    
    $currency = $data['currency'];
    $orderTotal = $data['orderTotal'];
    $milliseconds = round(microtime(true) * 1000);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['amazon_billing_agreement_id'] = $billingAgreementId;
	$requestParameters['authorization_reference_id'] = $data['sellerOrderId'].$milliseconds;
	$requestParameters['authorization_amount'] = $orderTotal;
	$requestParameters['currency_code'] = $currency;
	$requestParameters['store_name'] = $storeName;
	$requestParameters['seller_order_id'] = $sellerOrderId;
	
    $response = $client->authorizeOnBillingAgreement($requestParameters);
    $result = $response->toArray()['AuthorizeOnBillingAgreementResult'];
    
    $authorizationDetails = $result['AuthorizationDetails'];
    $oroId = $result['AmazonOrderReferenceId'];
    $authId = $authorizationDetails['AmazonAuthorizationId'];
    $reasonCode = $authorizationDetails['AuthorizationStatus']['ReasonCode'];
    $authState = $authorizationDetails['AuthorizationStatus']['State'];
    $clientInfo = "{\"oroId\": \"".$oroId."\", \"authorizationId\": \"".$authId."\", \"authorizationStatus\": \"".$authState."\", \"reasonCode\": \"".$reasonCode."\"}";
    echo $clientInfo;
}

function createOrderReferenceForId($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $config;
    
    $currency = $data['currency'];
    $orderTotal = $data['orderTotal'];
    $sellerOrderId = $data['sellerOrderId'];
    $storeName = $data['storeName'];
    $milliseconds = round(microtime(true) * 1000);
    
    $requestParameters = array();
	$requestParameters['merchant_id'] = $config['merchant_id'];
	$requestParameters['id'] = $billingAgreementId;
	$requestParameters['id_type'] = "BillingAgreement";
	$requestParameters['inherit_shipping_address'] = true;
	$requestParameters['confirm_now'] = true;
	$requestParameters['amount'] = $orderTotal;
	$requestParameters['currency_code'] = $currency;
	$requestParameters['seller_order_id'] = $sellerOrderId;
	$requestParameters['store_name'] = $storeName;
	
	$response = $client->createOrderReferenceForId($requestParameters);
	print_r($response);
    $oroDetails = $response->toArray()['CreateOrderReferenceForIdResult']['OrderReferenceDetails'];
    
    echo $oroDetails['AmazonOrderReferenceId'];
    
    /*
    
    $chargeParams['merchant_id'] = $config['merchant_id'];
    $chargeParams['amazon_order_reference_id'] = $oroDetails['AmazonOrderReferenceId'];
    $chargeParams['charge_amount'] = $orderTotal;
    $chargeParams['currency_code'] = $currency;
    $chargeParams['authorization_reference_id'] = $data['sellerOrderId'].$milliseconds;

    $response = $client->charge($chargeParams);
    print_r($response);
*/

}