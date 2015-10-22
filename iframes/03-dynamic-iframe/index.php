<html>
  <head>
  <title>Standard with iframe</title>
  </head>
<body>
<?php
    if(!empty($_SERVER['HTTPS'])){
        include "button.php";
    }else {
?>
    <iframe width="90%" height="800px" id="lpa_button" src="https://com-search.de/amz/samples/amazon-login-and-pay-tests/iframes/03-dynamic-iframe/button.php">
<?php
    }
?>
    </iframe> 
  </body>
</html>