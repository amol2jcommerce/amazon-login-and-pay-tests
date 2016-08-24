/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function proceedToPaymentSelection(){
     $( "#accordion" ).accordion("option", { active: 2 });
    walletWidget.bind("walletWidgetDiv");
}

function proceedToConsent(){
     $( "#accordion" ).accordion("option", { active: 3 });
    consentWidget.bind("consentWidgetDiv");
}

function confirmPayment(){
    $( "#accordion" ).accordion("option", { active: 4 });
    $("#setupPayment").css("opacity", 1).css("cursor", "pointer").click(setupAutmaticPayment);
}

function handleConsentState(billingAgreementConsentStatus){
 var buyerBillingAgreementConsentStatus = billingAgreementConsentStatus.getConsentStatus();
    $("#consentContainer").val(buyerBillingAgreementConsentStatus);
    if(buyerBillingAgreementConsentStatus === true || buyerBillingAgreementConsentStatus === "true" ){
        $("#confirmPayment").click(confirmPayment);
        $("#confirmPayment").css("opacity", 1).css("cursor", "pointer").on();   
    } else {
        $("#confirmPayment").css("opacity", 0.5).css("cursor", "auto").off();   
    }
   return buyerBillingAgreementConsentStatus;
}

function setupAutmaticPayment(){
    var sellerOrderId = $("#sellerOrderId").val();
    var storeName = $("#storeName").val();
    var consentToken = $("#tokenContainer").val();
    // set the order total on the billingAgreement, on success enable the next step
    $.post("ajax/billingAgreementFunctions.php", {action: "setBillingAgreementDetails", data :{sellerOrderId: sellerOrderId, storeName : storeName, consentToken: consentToken} }).done(function( data ) {
        console.log(data);
        $.post("ajax/billingAgreementFunctions.php", {action: "confirmBillingAgreement",  data :{sellerOrderId: sellerOrderId, storeName : storeName, consentToken: consentToken} }).done(function( data ) {
            $.post("ajax/billingAgreementFunctions.php", {action: "validateBillingAgreement", data :{} }).done(function( data ) {
                console.log("billing agreement setup and validated");
                var billingAgreementId = $("#idContainer").val();
                if($("#ba_" + billingAgreementId).length){
                    console.log("ba already exists");
                } else {
                    $("#paymentObjectContainer").append("<div class=\"po billingAgreement\" id=\"ba_" + billingAgreementId + "\">Billing Agreement (" + billingAgreementId + ")</div>");
                }
                requestPayment();
            } ).error(function(error){
                alert("error validating the billing agreement");
            });
            
        }
        ).error(function(error){
           alert("error confirming the billing agreement");
        });
    }
    ).error(function(error){
       alert("error setting the billing agreement details");
    });
}

function requestPayment(){
    var currency = $("#currency").val();
    var orderTotal = $("#orderTotal").val();
    var sellerOrderId = $("#sellerOrderId").val();
    var billingAgreementId = $("#idContainer").val();
    
    $.post("ajax/billingAgreementFunctions.php", {action: "authorizeOnBillingAgreement", data :{sellerOrderId: sellerOrderId, currency: currency, orderTotal : orderTotal} }).done(function( data ) {
        var objectInfo = JSON.parse(data);
        $("#ba_" + billingAgreementId).append("<div class=\"po orderReference\" id=\"oro_" + objectInfo.oroId + "\">OrderReference (" + objectInfo.oroId + ")</div>")
        $("#oro_" + objectInfo.oroId).append("<div class=\"po authorization\" id=\"auth_" + objectInfo.authorizationId + "\">Authorization (" + objectInfo.authorizationId + ")</div>")
        
    }).error(function(error){
       alert("error setting the billing agreement details");
    });
}