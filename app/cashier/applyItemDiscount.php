<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["entryTransNo"])){
        $transNo = mres($_POST["entryTransNo"]);
        $entry = mres($_POST["entry"]);
        $discountCode = mres($_POST["discountCode"]);
        $discountRate = mres($_POST["discountRate"]);
        
        $discountAmount = sprintf("%.2f", getTransactionHeaderAmount($entry) * $discountRate);
        
        $totalAmount = sprintf("%.2f", getTransactionHeaderSubtotal($entry) - $discountAmount);
        
        if(getTransactionHeaderDiscount($entry) <= 0){
            applyItemDiscount($transNo, $entry, $discountCode, $discountAmount, $totalAmount);
        }
    }
?>