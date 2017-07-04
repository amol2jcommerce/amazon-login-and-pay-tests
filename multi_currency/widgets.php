<?php
require_once 'config.php';
?>
<html>
  <head>
    <title>Multi currency - sample</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <style type="text/css">
        /* Please include the min-width, max-width, min-height and max-height	 */
        /* if you plan to use a relative CSS unit measurement to make sure the */
        /* widget renders in the optimal size allowed.							           */

        #login_with_amazon_widget1 {min-width: 300px; max-width: 600px; min-height:
        228px; max-height: 400px;}
        #login_with_amazon_widget2 {min-width: 300px; max-width:600px; min-height: 228px;
        max-height: 400px;}

        /* Smartphone and small window */
        #login_with_amazon_widget1 {width: 100%; height: 228px;}
        #login_with_amazon_widget2 {width: 100%; height: 228px;}

        /* Desktop and tablet */
        @media only screen and (min-width: 768px) {
            #login_with_amazon_widget1 {width: 400px; height: 228px;}
            #login_with_amazon_widget2 {width: 400px; height: 228px;}
        }
        
        body {
          padding: 15px;
        }
        
    </style>
      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/base-min.css">
      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
      <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
      <script type="text/javascript" src="https://storage.googleapis.com/google-code-archive-downloads/v2/code.google.com/vkbeautify/vkbeautify.0.99.00.beta.js"></script>
      <script>
	
	  function getURLParameter(name, source) {
  		return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
  		  '([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,
  		  '%20')) || null;
	  }

	  var accessToken = getURLParameter("access_token", location.hash);
	  var state = getURLParameter("state", location.hash);

	  if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
		  document.cookie = "amazon_Login_accessToken=" + accessToken + ";secure";
	  }
	  
      
  </script>
  </head>
<body>

<br />
<h1>Presentment Currency sample</h1>
  <p>
    This sample demonstrates the use of the Amazon Pay multi currency (presentment currency) feature.<br />
    The merchant account will still be operating in the currency it was created for, the presentment currency the buyer will be charged in can differ from this currency.<br />
    A currency conversion will be done automatically by Amazon Pay.<br /><br />
    
    This sample concentrates on the happy flow only and does <b>not</b> take e.g. currency mismatches into account.
  </p>
  <br /> <br />
      <div id="widgetContent">
        <form class="pure-form pure-form-aligned">
          <div>Please specify the amount and the currency to transact in.</div>
          <fieldset>
              <div class="pure-control-group">
                  <label for="amount">Amount</label>
                  <input id="amount" type="text" placeholder="Amount" value="100">
                  <span class="pure-form-message-inline">*required</span>
              </div>
      
              <div class="pure-control-group">
                  <label for="currency">Presentment currency</label>
                   <select id="currency">
                      <option>CHF</option>
                      <option>EUR</option>
                      <option>GBP</option>
                      <option>USD</option>
                  </select>
                  <span class="pure-form-message-inline">*required</span>
                  <span class="pure-form-message-inline"> **only a selection of possible currencies</span>
              </div>
              
      
      <br />
      	<div id="login_with_amazon_widget1"></div>
      	<div id="login_with_amazon_widget2"></div>
      	
      	
      	<div class="pure-button" id="logout">Logout</div>
      	<div class="pure-button" id="check">Check login state</div>
      	<div class="pure-button" id="back">Back to button page</div>
      	<div id="state"></div>
        <br />
      	
      	
              <div class="pure-controls">
                  <button type="submit" class="pure-button pure-button-primary" id="processPayment">Submit</button>
              </div>
      	    </fieldset>
      </form>
    </div>
    <div id="successContent" style="display: none;">
      <h2>Order was placed, here is the result</h2>
    <pre></pre>
    </div>




	<script>
	  $('#logout').click(function() {
		  amazon.Login.logout();
		  document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
		  window.location = 'index.php';
	  });
	  
	  $('#back').click(function() {
		  window.location = 'index.php';
	  });
	  
	  $('#check').click(function() {
		  options = { scope: "profile payments:widget payments:shipping_address payments:billing_address", popup: true, interactive: 'never' };
		  amazon.Login.authorize(options, function(response) {
		    if ( response.error ) {
			    //no active Amazon Session
		      alert(response.error);
			    return;
		    }
		    alert("active");
		    //active session
		  });
	  });
	  
	  $('#processPayment').click(function() {
		  var postData = { 
                action: "placeOrder", 
                params: {
                    orderTotal: $('#amount').val(),
                    currency: $('#currency').find(":selected").text(),
                    sellerOrderId: "12345",
                    storeName: "Multi currency sample",
                    contract: orderReferenceId
                } 
            };
            $.post( "backend/paymentFacade.php", postData)
            .done(function( data ) {
                console.log(JSON.parse(data));
                $("#widgetContent").fadeOut();
                $("#successContent pre").text(vkbeautify.json(data));
                $("#successContent").fadeIn();
                // TODO: update the frontend
            });
	  });
	  
	  window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("<?php echo $config['client_id']; ?>");	
        amazon.Login.setUseCookie(true);
      };
	  
	    var paymentRendered = false;
      var orderReferenceId;
      
      function renderAddressWidget(){
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: "<?php echo $config['merchant_id']?>",
            scope: "profile payments:widget payments:shipping_address payments:billing_address",
            onOrderReferenceCreate: function(orderReference) {
              orderReferenceId = orderReference.getAmazonOrderReferenceId();
            },
            onAddressSelect: function(orderReference) {
              if(!paymentRendered){
                  renderPaymentWidget();
              }
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error.getErrorMessage());
            }
          }).bind("login_with_amazon_widget1");
      }
      
      
      function renderPaymentWidget(){
        new OffAmazonPayments.Widgets.Wallet({
            sellerId: "<?php echo $config['merchant_id']?>",
            scope: "profile payments:widget payments:shipping_address payments:billing_address",
            presentmentCurrency: $('#currency').find(":selected").text(),
            onPaymentSelect: function(orderReference) {
              // Replace this code with the action that you want to perform
              // after the payment method is selected.
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error.getErrorMessage());
            }
          }).bind("login_with_amazon_widget2");
          
          paymentRendered = true;
      }
	  
      window.onAmazonPaymentsReady = function() {
        renderAddressWidget();
        document.getElementById("state").innerHTML = "state parameter: " + state;
      };
	  
  </script>
	
	
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
