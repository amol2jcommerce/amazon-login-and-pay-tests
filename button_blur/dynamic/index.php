<?php
include("config.php");
?>
<html>

<head>
    <title>Blurring the button until loaded</title>
    <script src="https://code.jquery.com/jquery-2.2.3.js"></script>
    <script type="text/javascript" src="options.js"></script>
    <link rel="stylesheet" href="../button_blur.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
</head>

<body>
    
    
    <form action="index.php" method="post">
        <table class="options">
            <tr>
                <th class="option">Option</th>
                <th class="separator"></th>
                <th class="value">Value</th>
            </tr>
            <tr>
                <td class="option">Environment</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="environment" name="environment"></select>
                </td>
            </tr>
             <tr>
                <td class="option">Region</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="region" name="region"></select>
                </td>
            </tr>
            <tr>
                <td class="option">Type</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="type" name="type"></select>
                </td>
            </tr>
            <tr>
                <td class="option">Color</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="color" name="color"></select>
                </td>
            </tr>
            <tr>
                <td class="option">Size</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="size" name="size"></select>
                </td>
            </tr>
            <tr>
                <td class="option">Language</td>
                <td class="separator">:</td>
                <td class="value">
                    <select id="language" name="language"></select>
                </td>
            </tr>
            <tr>
                <td class="option">Artificial delay (ms)</td>
                <td class="separator">:</td>
                <td class="value">
                    <input type="text" id="delay" name="delay" value="<?php if(isset($_POST['delay'])){ echo $_POST['delay']; } else { echo "3000";}?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="separator"><input type="submit" value="Submit"/></td>
            </tr>
        </table>
    </form>
    <br />

  
    <p> This is loading the button directly from our servers. No image is staticly included.
    The blur effect is added dynamically via css filters.
        <br />
        <b>For demonstration purposes, an artificial delay of 3 seconds is used. </b>
        <br /> The link to the image is <i>not</i> hard-coded, it is calculated based on the selected values above.
        <br />
        <br />
        Clicks to the blurred image are caught and directed to the real button as soon as possible. Unfortunately this ends in a blocked popup at the moment.
   
    
    <div id="amazon_pay_button">
        <img id="amazon_pay_button_placeholder" class="blur" src=""/>
    </div>
    
    <br />
    
    <script>
    var color = "Gold";
    var size = "medium";
    var type = "PwA";
    var language = "de_DE";
    var environment= "sandbox";
    var region = "<?php echo $config['region']; ?>".toLowerCase();
    
    var imgSrc = "https://d23yuld0pofhhw.cloudfront.net/$region$/$environment$/amazonpay/$color$/$size$/button.png";
    
    <?php 
    if(isset($_POST["color"])){ echo "color = \"".$_POST["color"]."\";";} 
    if(isset($_POST["size"])){ echo "size = \"".$_POST["size"]."\";";} 
    if(isset($_POST["type"])){ echo "type = \"".$_POST["type"]."\";";} 
    if(isset($_POST["language"])){ echo "language = \"".$_POST["language"]."\";";} 
    if(isset($_POST["environment"])){ echo "environment = \"".$_POST["environment"]."\";";} 
    if(isset($_POST["region"])){ echo "region = \"".$_POST["region"]."\";";}
    
    ?>
        $.each(colors, function(key, value) {
            var selected = color == value;
                if(selected){
                    imgSrc = imgSrc.replace("$color$", value.toLowerCase());
                    $("#color").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#color").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
        $.each(sizes, function(key, value) {
            var selected = size == value;
                if(selected){
                    imgSrc = imgSrc.replace("$size$", value.toLowerCase());
                    $("#size").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#size").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
        $.each(types, function(key, value) {
            var selected = type == value;
                if(selected){
                    imgSrc = imgSrc.replace("$type$", value);
                    $("#type").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#type").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
         $.each(languages, function(key, value) {
            var selected = language == value;
                if(selected){
                    imgSrc = imgSrc.replace("$language$", value);
                    $("#language").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#language").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
        $.each(environments, function(key, value) {
            var selected = environment == value;
                if(selected){
                    var replacement = value;
                    imgSrc = imgSrc.replace("$environment$", replacement);
                    $("#environment").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#environment").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
        $.each(regions, function(key, value) {
            var selected = region == value;
                if(selected){
                    imgSrc = imgSrc.replace("$region$", value);
                    $("#region").append('<option value=' + value + ' selected="selected">' + key + '</option>');
                } else {
                    $("#region").append('<option value=' + value + '>' + key + '</option>');
                }
            }
        );
        
        $("#amazon_pay_button_placeholder").attr("src", imgSrc);
        
    </script>
  
    <script>
        var clickCatched = false;
        $("#amazon_pay_button_placeholder").click(function() {
                clickCatched = true;
            }

        );
        window.onAmazonLoginReady = function() {
            amazon.Login.setClientId("<?php echo $config['client_id']; ?>");
        }

        ;
        window.onAmazonPaymentsReady = function() {
            // render the button delayed
            setTimeout(renderButton, $("#delay").val());
        }

        function renderButton() {
            var authRequest;
            var loginOptions = {
                scope: "profile payments:widget",
                popup: "true"
            };
            OffAmazonPayments.Button("amazon_pay_button", "<?php echo $config['merchant_id']; ?>", {
                type: type,
                color: color,
                size: size,
                language: language.replace("_","-"),
                authorization: function() {
                    authRequest = amazon.Login.authorize(loginOptions, customerAuthorized);
                },
                onError: function(error) {
                    console.log(error);
                }
            });
            
            $("#amazon_pay_button img").removeClass("blur").addClass("clickable");

            if (clickCatched) {
                // this ends in a popup blocker
                $("#amazon_pay_button").children("img").click();
                clickCatched = false;
            }
            
        }
        
        
        var widgetsjsUrl = widgetsjsUrls[region][environment];
        $("body").append("<script async='async' src='" + widgetsjsUrl + "'><\/script>");

        function customerAuthorized(result) {
            console.log(result);
        }
    </script>
    

</body>

</html>