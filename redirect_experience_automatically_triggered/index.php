<html>
  <head>
    <title>Redirect Experience - Automatically start authentication via script</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/base-min.css">
    <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
      
    <style type="text/css">
      #login_with_amazon_button img {
        cursor:pointer;
      }
        
      body {
        padding: 25px;
      }
    </style>
  </head>
<body>
  <h1>Redirect experience</h1>
  <p>
    This button is hosted on this page directly and not created using the Amazon Pay Widgets.js. <br />
    Clicking it will lead you to a new page (redirect.php), which will automatically trigger the redirect experience to ask for buyer authentication.<br />
    The integration <b>does not</b> follow the best practices given in the official AmazonPay integration guide. This is a workaround if you cannot host the Widgets.js file on your cart page.<br /><br z7>
    <b>Please be aware</b>: The <i>state</i> parameter is fixed in this sample. For security reasons this should not be the case, please check the Request Frogery section of the guide for this.
  </p>
	<div id="login_with_amazon_button">
	  <img src="https://d23yuld0pofhhw.cloudfront.net/de/live/amazonpay/gold/large/button.png" />
	</div>
	
	  <script>
	    document.getElementById("login_with_amazon_button").addEventListener("click", startUserAuthentication);
	  
	    function startUserAuthentication(event){
	      document.location = "redirect.php";
	    }
	  
	  
    </script>
  </body>
</html>
