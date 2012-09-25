<?php
    include("../../library/config.php");
    
    $itemData = checkSku($_GET["s"]);
    $result = mysql_num_rows($itemData);
    $error = 0;
    if($result == 0){
       $error = 1;
    }
    echo "{";
    echo '"error":', json_encode($error), "\n";
    echo "}";

?>