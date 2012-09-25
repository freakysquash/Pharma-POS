<?php
    $host = "5.175.157.92:3306";
    $user = "mypharmapos";
    $password = "mypharmapos";
    $db  = "my_pharma_pos_master";
    
    mysql_connect($host, $user, $password) or die(mysql_error());
    mysql_select_db($db);
    
    $query = mysql_query("SELECT username FROM user_accounts") or die(mysql_error());
    while($i = mysql_fetch_assoc($query)){
        echo $i["username"] . "<br/>";
    }
    
?>
