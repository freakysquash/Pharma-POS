<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["t"])){
        $transNo = mres($_POST["t"]);
        $userId = $_SESSION["userId"];
        $store = $_SESSION["store"];
        $register = $_SESSION["register"];
        $subtotal = getTransactionSubtotal($transNo);
        $tax = getTransactionTotalTax($transNo);
        $discount = getTransactionTotalDiscount($transNo);
        $total = getTransactionTotalAmount($transNo);
        
        $systemDate = date("Y-m-d");
        $systemTime = date("h:i:s");
    }
?>
