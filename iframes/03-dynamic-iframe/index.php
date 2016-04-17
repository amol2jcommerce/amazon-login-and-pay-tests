<html>
  <head>
  <title>Standard with iframe</title>
  </head>
<body>
<?php
    if(!empty($_SERVER['HTTPS']) || $_SERVER['REQUEST_SCHEME'] == "https"){
        include "button.php";
    } else {
?>
    <iframe width="90%" height="800px" id="lpa_button" src="button.php">
<?php
    }
?>
    </iframe> 
  </body>
</html>