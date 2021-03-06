<?php
require_once 'config.php';
?>
<html>

<head>
<meta name="viewport" content="width-device-width,initial-scale=1.0, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type='text/javascript'>
	window.onAmazonLoginReady = function() {
		amazon.Login.setRegion(amazon.Login.Region.Europe);
		amazon.Login.setClientId("<?php echo $config['client_id']; ?>");
		amazon.Login.setUseCookie(true);
	};

</script>
<style type="text/css">
      	.warning {
			color: red;
		}

		/* container emulating the button design, gradient, border, ... */
		.PwAResponsiveContainer, .PwAResponsiveContainerNoBG, .PwAResponsiveContainerLightGrey, .PwAResponsiveContainerDarkGrey {
			width: 80%;
			min-width: 290px;
			height:60px;
			margin-left: auto;
			margin-right:auto;
			background: linear-gradient(#FAE69B, #EFC332);
			border-radius: 3px; 
			border: #AA8426;  
			border-style: solid;   
			border-width: 1px; 
			cursor: pointer
		}
		
		.PwAResponsiveContainerNoBG {
			background: transparent;
			border-style: none;  
		}
		
		.PwAResponsiveContainerLightGrey {
			background: linear-gradient(#FEFEFE, #E2E2E2);
		}
		
		.PwAResponsiveContainerDarkGrey {
			background: transparent;
		}
		
		
		/* alignment of the actual image */
		.PwAResponsive {
			margin-left: auto;
			margin-right:auto;
			padding-top: 5px;
			padding-bottom: 5px;
			display: block !important; 
			height: 85%;
			width: 90%;
		}
		
		/* hide the button we create, essential: in the JS code below, the click action gets assigned to the div */
		#AmazonPayButton img {
			display: none;
		}
		
        #walletWidgetDivRegular {min-width: 300px; max-width:600px; min-height: 228px; max-height: 400px;}

        /* Smartphone and small window */
        #walletWidgetDivRegular {width: 100%; height: 228px;}

        /* Desktop and tablet */
        @media only screen and (min-width: 768px) {
            #walletWidgetDivRegular {width: 400px; height: 228px;}
            .PwAResponsiveContainer, .PwAResponsiveContainerNoBG, .PwAResponsiveContainerLightGrey, .PwAResponsiveContainerDarkGrey {
            	height:90px;
            }
        }
        
        @media only screen and (max-width: 400px) {
            #walletWidgetDivRegular {width: 400px; height: 228px;}
            .PwAResponsiveContainer, .PwAResponsiveContainerNoBG, .PwAResponsiveContainerLightGrey, .PwAResponsiveContainerDarkGrey {
            	height:40px;
            }
        }
</style>

</head>
<body>
	<h1>Demo to provide a responsive Pay with Amazon button</h1>
	<p class="warning">Please do not use this in live integrations, this is for demo purposes only.</p>

	<div id="AmazonPayButton" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-de.svg"></img>
	</div>
	
	<div id="AmazonPayButton_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-de.svg"></img>
	</div>
	
	<br />
	<div id="AmazonPayButton2" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-en.svg"></img>
	</div>
	
	<div id="AmazonPayButton2_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-en.svg"></img>
	</div>
	
	<br />
	<div id="AmazonPayButton3" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-es.svg"></img>
	</div>
	
	<div id="AmazonPayButton3_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-es.svg"></img>
	</div>
	
	<br />
	<div id="AmazonPayButton4" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-fr.svg"></img>
	</div>
	
	<div id="AmazonPayButton4_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-fr.svg"></img>
	</div>
	
	<br />
	<div id="AmazonPayButton5" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-it.svg"></img>
	</div>
	
	<div id="AmazonPayButton5_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-it.svg"></img>
	</div>
	
	<br />
	<div id="AmazonPayButton6" class="PwAResponsiveContainer">
		<img class="PwAResponsive" src="img/black/pwa-logo-nl.svg"></img>
	</div>
	
	<div id="AmazonPayButton6_s" class="PwAResponsiveContainerLightGrey">
		<img class="PwAResponsive" src="img/styled/pwa-logo-nl.svg"></img>
	</div>
	
	
	
	
	
	
	
	
	
	<div id="walletWidgetDivRegular"></div>


	<p><a href="" id="Logout">Logout</a></p>

	<script type="text/javascript">
		function getURLParameter(name, source) {
			/*<![CDATA[*/
				var expr = (new RegExp('[?|&|#]' + name + '=' +'([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,'%20');
				return decodeURIComponent(expr)|| 'wrong';
			/*]]>*/
		}
	  
		function doIfLoggedIn(loggedInCallback, notLoggedInCallback){
		 options = { scope: "profile payments:widget", popup: true, interactive: 'never' };
		    amazon.Login.authorize(options, function(response) 
		    {
		     if ( response.error ) {
		         notLoggedInCallback();
		         return;
		     }
		     loggedInCallback();
		    });
		}

		var access_token = getURLParameter("access_token", location.hash);
		if (typeof access_token === 'string' && access_token.match(/^Atza/)) {
			document.cookie = "amazon_Login_accessToken=" + access_token + ";secure";
		}
		
		window.onAmazonPaymentsReady = function(){
			doIfLoggedIn(renderWidgets, showButton);
		}
		
	    function showButton(){
			var authRequest;
			var authorization = function() {
				loginOptions = {
					scope : "profile payments:widget",
					popup : false
				};
				authRequest = amazon.Login.authorize(loginOptions, "https://amazon-login-and-pay-tests-github-danielneu.c9users.io/responsive_button/index.php");
			};
			
			/* This is setting the click event to the div emulating our button */
	        document.getElementById("AmazonPayButton").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton_s").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton2").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton2_s").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton3").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton3_s").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton4").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton4_s").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton5").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton5_s").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton6").addEventListener("click", authorization);
	        document.getElementById("AmazonPayButton6_s").addEventListener("click", authorization);
    		
			OffAmazonPayments.Button("AmazonPayButton", "<?php echo $config['merchant_id']; ?>",
			{
				type : "PwA",
				color: "Gold",
				size : "medium",
				authorization : authorization,
				onError : function(error) {
					alert("The following error occurred: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
				}
			});
		
		}
		
		function renderWidgets() {
		
			new OffAmazonPayments.Widgets.Wallet({
				sellerId: "<?php echo $config['merchant_id']; ?>",
				scope: "profile payments:widget",
				onOrderReferenceCreate : function(orderReference) {
					orderReference.getAmazonOrderReferenceId();
				},
				design: {
					designMode: 'responsive'
				},
				onPaymentSelect: function(orderReference) {
					// Display your custom complete purchase button
				},
				onError: function(error) {
					widgetError('wallet',error);
				}
				}).bind("walletWidgetDivRegular");
		}
      
		function widgetError (whichWidget, error) {
			alert("The following error occurred while rendering the " + whichWidget + " widget: " + error.getErrorMessage());
		}
      
		document.getElementById('Logout').onclick = function() {
			amazon.Login.logout();
			console.log("logging out");
			document.cookie = "amazon_Login_accessToken=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
			window.location.reload();
		};
    </script>

	<script async="async" type='text/javascript' src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>

</body>
</html>