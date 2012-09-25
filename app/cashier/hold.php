<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["transaction"])){
        $transNo = $_POST["transaction"];
        $userId = $_SESSION["userId"];
        $subTotal = $_POST["subTotal"];
        $taxAmount = $_POST["taxAmount"];
        $discountAmount = $_POST["discountAmount"];
        $totalAmount = $_POST["totalAmount"];
        $systemDate = date("Y-m-d");
        $systemTime = date("h:i:s");
        $status = "OnHold";
        holdTransaction($_POST["transaction"]);
        processTransaction($transNo, $_SESSION["store"], $_SESSION["register"], $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime, $status);
        unset($_SESSION["transNo"]);
    }
?>
