<?php
$sdkPath = "SDK-php-1.0.13_UK";
$incPath = get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__)) . "/" . $sdkPath . "/src";

set_include_path($incPath);    

include $sdkPath."/src/OffAmazonPaymentsService/Client.php";

// next ones are needed for automatic only and are missing in the Client.php
require_once 'OffAmazonPaymentsService/Model/BillingAgreementAttributes.php';
require_once 'OffAmazonPaymentsService/Model/SellerBillingAgreementAttributes.php';
require_once 'OffAmazonPaymentsService/Model/Price.php';
require_once 'OffAmazonPaymentsService/Model/AuthorizationDetails.php';
require_once 'OffAmazonPaymentsService/Model/SellerOrderAttributes.php';


$client = new OffAmazonPaymentsService_Client();
$merchantId = $client->getMerchantValues()->getMerchantId();
$clientId = $client->getMerchantValues()->getClientId();
$currencyCode = $client->getMerchantValues()->getCurrency();


$environment = $client->getMerchantValues()->getEnvironment();
$region = $client->getMerchantValues()->getRegion();
$serviceUrl = $client->getMerchantValues()->getServiceUrl();
$widgetUrl = $client->getMerchantValues()->getWidgetUrl();

if ($region == "UK"){
	if ($environment == "sandbox"){
		$url_auth_o2 = "https://api.sandbox.amazon.co.uk/auth/o2/tokeninfo?access_token=";
		$url_user_profile = "https://api.sandbox.amazon.co.uk/user/profile";
		
	}else{
		$url_auth_o2 = "https://api.amazon.co.uk/auth/o2/tokeninfo?access_token=";
		$url_user_profile = "https://api.amazon.co.uk/user/profile";        
	}
}else{
	if ($environment == "sandbox"){
		$url_auth_o2 = "https://api.sandbox.amazon.de/auth/o2/tokeninfo?access_token=";
		$url_user_profile = "https://api.sandbox.amazon.de/user/profile";
		
	}else{
		$url_auth_o2 = "https://api.amazon.de/auth/o2/tokeninfo?access_token=";
		$url_user_profile = "https://api.amazon.de/user/profile";        
	}
}

