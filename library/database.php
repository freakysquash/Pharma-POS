<?php
    $host = "localhost";
    $user = "root";
    $pass = "root";
    $db = $_SESSION["db"];

    mysql_connect($host, $user, $pass) or die(mysql_error());

    mysql_select_db($db);

?>
