<?php
    mysql_connect("120.28.67.53:3306", "remote", "remoteconn") or die(mysql_error());
    
    mysql_select_db("point_of_sales");
    
    $query = mysql_query("SELECT * FROM brands") or die(mysql_error());
    while($b = mysql_fetch_assoc($query)){
        echo $b["name"] . "<br/>";
    }
    
?>