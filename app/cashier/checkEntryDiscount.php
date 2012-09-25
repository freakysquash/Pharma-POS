<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_GET["i"])){
        $discount = getTransactionHeaderDiscount($_GET["i"]);
        
        echo "{";
        echo '"entryDiscount":', json_encode($discount), "\n";
        echo "}";
    }
?>