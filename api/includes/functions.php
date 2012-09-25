<?php
    
    function currentUrl() {
        $page = 'http';
       if (!empty($_SERVER['HTTPS'])) {$page .= "s";}  
            $page .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $page .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $page .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $page;
    }
    
    function auth($key, $secret){
        $query = mysql_query("SELECT code FROM tenants WHERE code = '$key' AND secret_key = '$secret'") or die(mysql_error());
        if(mysql_num_rows($query) == 1){
            $auth = true;
        }
        else{
            $auth = false;
        }
        return $auth;
    }

?>