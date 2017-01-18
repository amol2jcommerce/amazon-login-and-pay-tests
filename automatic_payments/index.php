<?php
namespace PayWithAmazon;
session_start();
	require_once 'config.php';
	
    $action = "login";
    $accessToken = "--";
    $orderAmount = "19.99";
    $currency = "GBP";
    $sellerOrderId = "112233-";
    $storeName = "My awesome automatic store";
    
    $agreementType = "OrderReference";
    if(isset($_GET['agreementType'])){
        $agreementType = $_GET['agreementType'];
    }
?>

<input id="currency" type="hidden" value="<?php echo $currency; ?>" />
<input id="orderAmount" type="hidden" value="<?php echo $orderAmount; ?>" />
<input id="storeName" type="hidden" value="<?php echo $storeName; ?>" />
<input id="sellerOrderId" type="hidden" value="<?php echo $sellerOrderId; ?>" />

<?php
if(isset($_GET['access_token'])){
     $action = "selectAddress";
     $accessToken = $_GET['access_token'];
}
?>
<html>
<head>
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.css">
<script type='text/javascript' src='js/jquery-ui/external/jquery/jquery.js'></script>
<script type='text/javascript' src='js/jquery-ui/jquery-ui.js'></script>

<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0"/>
<style type="text/css">
#addressBookWidgetDiv {min-width: 300px; max-width: 600px; min-height:
228px; max-height: 400px;}
#walletWidgetDiv {min-width: 300px; max-width:600px; min-height: 228px;
max-height: 400px;}
/* Smartphone and small window */
#addressBookWidgetDiv {width: 100%; height: 228px;}
#walletWidgetDiv {width: 100%; height: 228px;}
#consentWidgetDiv {width: 100%; height: 228px;}

.po {
    margin-left: 10px;
}

.billingAgreement{
    background-color: #aaa;
}

.orderReference{
    margin-left: 20px;
    background-color: #bbb;
}

.authorization{
    margin-left: 30px;
    background-color: #ccc;
}
/* Desktop and tablet */
@media only screen and (min-width: 768px) {
#addressBookWidgetDiv {width: 400px; height: 228px;}
#walletWidgetDiv {width: 400px; height: 228px;}

#consentWidgetDiv {width: 400px; height: 140px;}

#accordion{width: 50%; float:left;}
input.container{width: 100%;}
#proceedToPayment {opacity: 0;}
#proceedToConsent {opacity: 0;}
#confirmPayment {opacity: 0;}

#contentWrapper {
    width: 100%;
    border: 1px solid black;
    overflow: hidden; 
}
#paymentObjects {
    border: 1px solid green;
    overflow: hidden; 
}
}
</style>
<script type='text/javascript' src='js/automaticPayments.js'></script>
<script type='text/javascript'>
    window.onAmazonLoginReady = function() {
    	amazon.Login.setClientId("<?php echo $config['client_id']; ?>");
    };
    
    $(function() {
        $( "#accordion" ).accordion({
          heightStyle: "content"
        });
        $( "#paymentObjects" ).accordion({
          heightStyle: "content"
        });
        $("#proceedToPayment").button();
        $("#proceedToConsent").button();
        $("#setupPayment").button();
        $("#setupOnetimePayment").button();
        $("#confirmPayment").button().click(confirmPayment);
        $("#logout").button().click(function(){
            amazon.Login.logout();
            window.location="index.php";
            $.post("ajax/billingAgreementFunctions.php", {action: "cleanUp", data :{} }).error(function(error){
                    displayError("Error cleaning up the server.");
                    console.log(error);
                });
        });
    <?php 
    $activeElement = 0;
    if($action == "selectAddress"){
        $activeElement = 1;
?>
        addressBookWidget.bind("addressBookWidgetDiv");
<?php
    } 
    echo '$( "#accordion" ).accordion("option", { active: '.$activeElement.' })';
?>
  });
</script>
<script type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

</head>
<body>
<h1>Automatic Payments Sample</h1>
<p />
<table width="100%">
    <tr>
        <td width = "100px">BillingAgreementId</td>
        <td><input class="container" type="text" id="idContainer" value="--"/></td>
    </tr>
    <tr>
        <td width = "100px">AccessToken</td>
        <td><input class="container" type="text" id="tokenContainer" value="<?php echo $accessToken; ?>"/></td>
    </tr>
     <tr>
        <td width = "100px">Consent status</td>
        <td><input class="container" type="text" value="--" id="consentContainer"/></td>
    </tr>
</table>
 <p />
 
 
 <div id="contentWrapper">
    <div id="accordion">
        <h3>Login with Amazon</h3>
       <div>
           <p>Use the Login and Pay with Amazon button to sign in to your Amazon account.</p>
           <div id="AmazonPayButton"></div>
        </div>


        <h3>Select a default shipping address from the address book widget</h3>
        <div>
          <p>Please select an address which will be used as the delivery address for future orders resulting from the automatic payment operations</p>
          <div id="addressBookWidgetDiv"></div>
          <p />
          <button id="proceedToPayment">Select this address</button>
        </div>
        <h3>Select a default payment method from the wallet widget</h3>
        <div>
          <div id="walletWidgetDiv"></div>
          <p />
          <button id="proceedToConsent">Select this payment method</button>
        </div>
        <h3>Confirm your consent for future automatic payments</h3>
        <div>
          <div id="consentWidgetDiv"></div>
          <p />
          <button id="confirmPayment">Confirm your selection</button>
        </div>  
        <h3>Complete the payment</h3>
        <div>
          <p>
          Please complete the payment by pressing the button below.<br />
          If you decided to setup a recurring payment, the first payment will be executed immediately. You can change the order total below, if you want to deduct a different amount with your next payment.
          
          
          <table width="100%">
           
            <tr>
                <td width = "100px">Order value</td>
                <td><input class="container" type="text" value="<?php echo $orderAmount ?>" id="orderValue"/></td>
            </tr>
            <tr>
                <td width = "100px">Shipping Costs</td>
                <td><input class="container" type="text" value="--" id="shippingCosts"/></td>
            </tr>
            <tr>
                <td width = "100px">Order Total</td>
                <td><input class="container" type="text" value="<?php echo $orderAmount ?>" id="orderTotal"/></td>
            </tr>
        </table>
          </p>
          <button id="setupPayment">Setup recurring payment now</button>
          <button id="setupOnetimePayment">Setup one-time payment now</button>
        </div>
      </div>
    <div id="paymentObjects">
        <h3>Payment Objects</h3>
            <div id="paymentObjectContainer">
                
            </div>
         <h3>Payment Notifications</h3>
            <div>
            <p>
            Sed non urna.
            </p>
            </div>
    </div>
 </div>
 <p />
 <button id="logout">Logout</button>

 <script type="text/javascript">
 var accessToken;
    function signedIn(resp){
        accessToken = resp.access_token;
    }
 
	var authRequest;
	var billingAgreementId;
	OffAmazonPayments.Button("AmazonPayButton", "<?php echo $config['merchant_id']; ?>", {
    type: "pwa",
	color: "gold",
	size: "small",
	
	authorization: function() {
		loginOptions =
		{scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: "true"};
		authRequest = amazon.Login.authorize(loginOptions, window.location.href);
	},
	onError: function(error) {
    	// your error handling code
    	console.log(error);
	}
	});
        
        
	var addressBookWidget = new OffAmazonPayments.Widgets.AddressBook({
	sellerId: "<?php echo $config['merchant_id']; ?>",
	agreementType: '<?php echo $agreementType; ?>',
	onReady: function(contract) {
        // when receiving a billing agreement id, enter it in the text field for reference, 
        // add it to the consent widget
        var getContractId = contract.getAmazonBillingAgreementId === undefined ? contract.getAmazonOrderReferenceId : contract.getAmazonBillingAgreementId;
        billingAgreementId = getContractId();
        // store it on the server
        $.post("ajax/billingAgreementFunctions.php", {action: "setBillingAgreementId", data :{ billingAgreementId: billingAgreementId} }).done(function( data ) {
            console.log("ba id: " + billingAgreementId);
        }).error(function(error){
                displayError("billingAbreementId was not set on server");
            });
        $("#idContainer").val(billingAgreementId);
<?php 
    if($agreementType === "BillingAgreement"){
?>
        consentWidget.setContractId(billingAgreementId);
<?php
    }
?>
	},
	onAddressSelect: function(billingAgreement) {
            // when an address was selected, calculate some shipping costs on the server, 
            // calculate the new order value and enable the next sthep
            $.get( "ajax/billingAgreementFunctions.php", { action: "calculateShipping", data : {accessToken: "<?php echo $accessToken; ?>" }}, function( data ) {
                var shippingCosts = Number(data['shippingCosts']);
                $("#shippingCosts").val(shippingCosts);
                var orderValue = shippingCosts + Number($("#orderValue").val());
                orderValue = Math.round(orderValue * 100) / 100;
                $("#orderTotal").val(orderValue);
                $("#proceedToPayment").css("opacity", 1).css("cursor", "pointer");
                $("#proceedToPayment").click(proceedToPaymentSelection);
            }, "json");
	},
	design: {
            designMode: 'responsive'
	},
	onError: function(error) {
            displayError(error);
	}
    });


    var walletWidget = new OffAmazonPayments.Widgets.Wallet({
	sellerId: '<?php echo $config['merchant_id']; ?>',
	onPaymentSelect: function(billingAgreement) {
            // enable the next step, when a payment method was selected
            $("#proceedToConsent").css("opacity", 1).css("cursor", "pointer");
            $("#proceedToConsent").click(proceedToConsent);
	},
	design: {
            designMode: 'responsive'
	},
	onError: function(error) {
            displayError(error);
	}
    });
        
<?php
    if($agreementType == "BillingAgreement"){
?>
    var buyerBillingAgreementConsentStatus;
	var consentWidget = new OffAmazonPayments.Widgets.Consent({
	sellerId: '<?php echo $config['merchant_id']; ?>',
	// amazonBillingAgreementId obtained from the Amazon Address Book widget.
	amazonBillingAgreementId: billingAgreementId,
	design: {
		designMode: 'responsive'
	},
	onReady: function(billingAgreementConsentStatus){
            // if we already have a consent status, handle it, else do nothing
            // Called after widget renders
            if(billingAgreementConsentStatus && billingAgreementConsentStatus.getConsentStatus){
                buyerBillingAgreementConsentStatus = handleConsentState(billingAgreementConsentStatus);
        }
           
	},
	onConsent: function(billingAgreementConsentStatus) {
            // handle any change on the consten status
            buyerBillingAgreementConsentStatus = handleConsentState(billingAgreementConsentStatus);
	},
	onError: function(error) {
            displayError(error);
	}
    });
    
<?php
    }
?>
    function displayError(error){
        console.log(error);
    }
       
    </script>
</body>
</html>
