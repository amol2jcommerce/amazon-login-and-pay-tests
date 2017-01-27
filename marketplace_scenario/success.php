<html>
  <head>
    <title>Marketplace scenario</title>
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
  </head>
<body>

	<div id="profile"></div>
	<br />
	
	Here is your address book:
	
	<div id="login_with_amazon_widget1"></div>
	<div id="logout">Logout</div>
	
<div id="state"></div>
	
	<script>
	  document.getElementById('logout').onclick = function() {
  		amazon.Login.logout();
  		document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
  		window.location = 'index.php';
	  };
	 
	  
    window.onAmazonLoginReady = function(){
        amazon.Login.setClientId("amzn1.application-oa2-client.3af7115907cf43f28c3fa8590cc3ef0b");	
      };
	  
      window.onAmazonPaymentsReady = function() {
        
        amazon.Login.retrieveProfile("<?php  echo $_GET['access_token']; ?>", function(response){
          console.log(response);
          if(response.success && response.profile != undefined){
            let profile = response.profile;
            document.getElementById('profile').innerHTML = "<h1>Welcome " + profile.Name + "</h1><br/><p>Your user id is: " + profile.CustomerId + " </p>";
          }
        });
      
      new OffAmazonPayments.Widgets.AddressBook({
            sellerId: 'A3IWQXFKXS2WSY',
            onOrderReferenceCreate: function(orderReference) {
              orderReferenceId = orderReference.getAmazonOrderReferenceId();
              console.log(orderReferenceId);
            },
            onAddressSelect: function(orderReference) {
            
            },
            design: {
              designMode: 'responsive'
            },
            onError: function(error) {
              console.log(error.getErrorMessage());
            }
          }).bind("login_with_amazon_widget1");
               
      };
</script>
	<script src='https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'></script>
	<script src="https://eu.account.amazon.com/lwa/js/sdk/login1.beta.js"></script>

  </body>
</html>
