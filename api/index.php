<?php

    include("includes/config.php");
    
    if(isset($_GET["key"]) && isset($_GET["secret"]) && isset($_GET["r"])){
        $key = mysql_real_escape_string(stripcslashes($_GET["key"]));
        $secret = mysql_real_escape_string(stripcslashes($_GET["secret"]));
        $auth = false;
        $auth = auth($key, $secret);
        if($auth){
            
            $request = mysql_real_escape_string(stripcslashes($_GET["r"]));
            if(!file_exists("json/" . $request . ".php")){
                echo "Error (file not exists)";
            }
            else{
                $uri = currentUrl();
                $paramString = str_replace(ROOT . "?", "", $uri);
                $array = explode("&", $paramString);
                $params = null;
                foreach($array as $index => $elem) {
                if ($index > 2) {
                    $params .= "&" . $elem;
                }
            }
                header("Location: json/" . $request . ".php?" . $params);
            }
        }
        else{
            echo "authentication failed";
        }
    }

?>
