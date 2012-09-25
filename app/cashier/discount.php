<?php
    include("../../library/config.php");
    
    if(getTransactionTotalDiscount($_GET["t"]) == null){
        $discount = "0.00";
    }
    else{
        $discount = getTransactionTotalDiscount($_GET["t"]);
    }
    
    echo "{";
    echo '"totalDiscount":', json_encode($discount), "\n";
    echo "}";
?>