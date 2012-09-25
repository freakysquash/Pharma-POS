<?php

    include("config.php");
    
    $db = mysql_query("SELECT DATABASE()") or die(mysql_error());
    echo mysql_result($db, 0);

?>
