<?php
    include("config.php");
    if(isset($_POST["client"])){
        $client = mysql_real_escape_string($_POST["client"]);
        $code = sha1(strtolower($client));
        $secret = substr(sha1(md5($code)), 0, 8);
        $address = mysql_real_escape_string($_POST["address"]);
        newTenant($code, $secret, $client, $address);
        createTenantDb($code, $secret, $client, $address);
    }

?>

<form method="post" action="">
    <div>
        <label>Client name:</label>
        <input type="text" name="client"/>
    </div>
    <div>
        <label>Address:</label>
        <input type="text" name="address"/>
    </div>
    <div>
       <input type="submit" value="Submit"/>
    </div>
</form>