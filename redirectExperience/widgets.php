<?php
require_once 'config.php';
?>
<html>
  <head>
    <title>Redirect - widgets</title>
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
    </style>
      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/base-min.css">
      <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
      
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

  <div id="state"></div>
	<div id="login_with_amazon_widget1"></div>
	<div id="login_with_amazon_widget2"></div>
	
	
	<div class="pure-button" id="logout">Logout</div>
	<div class="pure-button" id="check">Check login state</div>
	<div class="pure-button" id="back">Back to button page</div>
	
	<script>
	  document.getElementById('logout').onclick = function() {
		  amazon.Login.logout();
		  document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
		  window.location = 'index.php';
	  };
	  
	  document.getElementById('back').onclick = function() {
		  window.location = 'index.php';
	  };
	  
	  document.getElementById('check').onclick = function() {
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
	  };
	  
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
        document.getElementById("state").innerHTML = "state: " + state;
      };
	  
  </script>
	
	
	<script async='async' src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

  </body>
</html>
