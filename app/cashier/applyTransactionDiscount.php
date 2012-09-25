<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["discountCode"])){
        $transNo = mres($_POST["transaction"]);
        $discountCode = mres($_POST["discountCode"]);
        $discountRate = mres($_POST["discountRate"]);
        $transHeaders = getTransactionHeaders($transNo);
        while($t = mysql_fetch_assoc($transHeaders)){
            $entry = $t["id"];
            $amount = $t["total_amount"];
            $discountAmount = sprintf("%.2f", $amount * $discountRate);
            $discountedAmount = sprintf("%.2f", $amount - $discountAmount);
            applyTransactionHeaderDiscount($entry, $discountedAmount, $discountCode, $discountAmount);
        }
    }
?>
