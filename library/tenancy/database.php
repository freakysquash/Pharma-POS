<?php

    $dbhost = "localhost";
    $dbusername = "root";
    $dbpassword = "root";
    $dbname = "my_pharma_pos_master";

    mysql_connect($dbhost, $dbusername, $dbpassword) or die(mysql_error());

    mysql_select_db($dbname);

//    $host = "5.175.195.164";
//    $user = "jacobfernandez";
//    $password = "z7FZGSjC";
//    $db  = "my_pharma_pos_master";
//    
//    mysql_connect($host, $user, $password) or die(mysql_error());
//    mysql_select_db($db);
?>
