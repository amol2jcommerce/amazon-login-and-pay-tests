<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
require_once("../lpa.config.php");

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
}

function cleanUp($data = null){
    session_destroy(); 
    unlink("IPN.log");
}

function calculateShipping($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    $addressConsentToken = $data['accessToken'];
    global $merchantId;
    global $client;
    
    $request = new OffAmazonPaymentsService_Model_GetBillingAgreementDetailsRequest();

    $request->withAmazonBillingAgreementId($billingAgreementId)
            ->withSellerId($merchantId)
            ->withAddressConsentToken($addressConsentToken);

    $response = $client->getBillingAgreementDetails($request);
    
    $details = $response->getGetBillingAgreementDetailsResult()
            ->getBillingAgreementDetails();
    $destination = $details->getDestination()->getPhysicalDestination();

    // simulating we are shipping world wide, only DE is more expensive, the rest costs 3.99 fixed
    $shippingCosts = "3.99";

    $countryCode = $destination->getCountryCode();
    if($countryCode ==="DE"){
        $shippingCosts = "5.99";
    }
    echo '{"shippingCosts" : "'.$shippingCosts.'"}';

}

function getBillingAgreementDetails($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    $consentToken = $data["consentToken"];
    global $client;
    global $merchantId;
    
    $request = new OffAmazonPaymentsService_Model_GetBillingAgreementDetailsRequest();
    $request->withAddressConsentToken($consentToken)
            ->withSellerId($merchantId)
            ->withAmazonBillingAgreementId($billingAgreementId);
    $response = $client->getBillingAgreementDetails($request);
    $billingAgreementDetails = $response->getGetBillingAgreementDetailsResult()->getBillingAgreementDetails();
    
    return $billingAgreementDetails;    
}

function setBillingAgreementDetails($data = null){
    $billingAgreementDetails = getBillingAgreementDetails($data);
    $state = $billingAgreementDetails->getBillingAgreementStatus()->getState();
    if($state === "Draft"){
        $sellerOrderId = $data['sellerOrderId'];
        $storeName = $data['storeName'];
        $billingAgreementId = $_SESSION['billingAgreementId'];
        global $client;
        global $merchantId;

        $request = new OffAmazonPaymentsService_Model_SetBillingAgreementDetailsRequest();
        $sellerAttributes = new OffAmazonPaymentsService_Model_SellerBillingAgreementAttributes();
        $sellerAttributes->withSellerBillingAgreementId($sellerOrderId)
                ->withStoreName($storeName);

        $attributes = new OffAmazonPaymentsService_Model_BillingAgreementAttributes($data);
        $attributes->withSellerBillingAgreementAttributes($sellerAttributes);

        $request->withAmazonBillingAgreementId($billingAgreementId)
                ->withSellerId($merchantId)
                ->withBillingAgreementAttributes($attributes);
        $response = $client->setBillingAgreementDetails($request);
        echo "set details to the agreement";
    } else{
        echo "agreement already open, skipping";
    }
}

function confirmBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $merchantId;
    
    $billingAgreementDetails = getBillingAgreementDetails($data);
    //if($billingAgreementDetails->getBillingAgreementStatus()->getState() === "Draft"){
        $request = new OffAmazonPaymentsService_Model_ConfirmBillingAgreementRequest();
        $request->withSellerId($merchantId)
                ->withAmazonBillingAgreementId($billingAgreementId);

        $response = $client->confirmBillingAgreement($request);
        echo "billing agreement confirmed";
    //} else {
      //  echo "agreement already confirmed";
    //}
}

function validateBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $merchantId;
    
    
    $request = new OffAmazonPaymentsService_Model_ValidateBillingAgreementRequest();
    $request->withSellerId($merchantId)
            ->withAmazonBillingAgreementId($billingAgreementId);
    
    $response = $client->validateBillingAgreement($request);
    $result = $response->getValidateBillingAgreementResult();
    $state = $result->getBillingAgreementStatus()->getState();
    $validationResult = $result->getValidationResult();
    if($state === "Open" && $validationResult === "Success"){
        echo "OK";
    } else {
        header('HTTP/1.0 400 Bad Request'); 
    }
}

function authorizeOnBillingAgreement($data = null){
    $billingAgreementId = $_SESSION['billingAgreementId'];
    global $client;
    global $merchantId;
    
    $currency = $data['currency'];
    $orderTotal = $data['orderTotal'];
    $milliseconds = round(microtime(true) * 1000);
    $authReferenceId = $data['sellerOrderId'].$milliseconds;

    $sellerAttributes = new OffAmazonPaymentsService_Model_SellerOrderAttributes();
    $sellerAttributes->withSellerOrderId($sellerOrderId)
            ->withStoreName($storeName);
    
    $amount = new OffAmazonPaymentsService_Model_Price();
    $amount->withAmount($orderTotal)
            ->withCurrencyCode($currency);
    
    $request = new OffAmazonPaymentsService_Model_AuthorizeOnBillingAgreementRequest();
    $request->withSellerId($merchantId)
            ->withAmazonBillingAgreementId($billingAgreementId)
            ->withSellerOrderAttributes($sellerAttributes)
            ->withAuthorizationAmount($amount)
            ->withAuthorizationReferenceId($authReferenceId);
    
    $response = $client->authorizeOnBillingAgreement($request);
    $result = $response->getAuthorizeOnBillingAgreementResult();
    $authorizationDetails = $result -> getAuthorizationDetails();
    $oroId = $result ->getAmazonOrderReferenceId();
    $authId = $authorizationDetails->getAmazonAuthorizationId();
    $reasonCode = $authorizationDetails->getAuthorizationStatus()->getReasonCode();
    $authState = $authorizationDetails->getAuthorizationStatus()->getState();
    $clientInfo = "{\"oroId\": \"".$oroId."\", \"authorizationId\": \"".$authId."\", \"authorizationStatus\": \"".$authState."\", \"reasonCode\": \"".$reasonCode."\"}";
    echo $clientInfo;
}
