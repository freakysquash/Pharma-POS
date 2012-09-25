<?php
    include("../../library/config.php");
   
    $tax = getTransactionTotalTax($_GET["t"]);
    if($tax == null){
        $tax = "0.00";
    }
    echo "{";
    echo '"totalTax":', json_encode($tax), "\n";
    echo "}";
?>