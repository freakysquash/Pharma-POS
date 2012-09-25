<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["transaction"])){
        $transNo = mres($_POST["transaction"]);
        $transHeaders = getTransactionHeaders($transNo);
        while($t = mysql_fetch_assoc($transHeaders)){
            $entry = $t["id"];
            $unDiscountedAmount = sprintf("%.2f", $t["total_amount"] + $t["discount_amount"]);
            $discountAmount = 0;
            $discountCode = "0000";
            removeTransactionHeaderDiscount($transNo, $entry, $unDiscountedAmount, $discountCode, $discountAmount);
        }
    }
?>