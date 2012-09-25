<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["purchaseNo"])){
        $purchaseNo = mres($_POST["purchaseNo"]);
        $supplier = mres($_POST["supplier"]);
        $attentionTo = mres($_POST["attention"]);
        $preparedBy = $_SESSION["userId"];
        $status = "Pending";
        $totalAmount = getPOTotalAmount($purchaseNo);
        $systemDate = date("Y-m-d");
        $systemTime= date("h:i:s");
        filePurchaseOrder($purchaseNo, $supplier, $attentionTo, $preparedBy, $status, $totalAmount, $systemDate, $systemTime);
    }
?>