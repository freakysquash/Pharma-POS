<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["transaction"])){
        $transNo = mres($_POST["transaction"]);
        $userId = $_SESSION["userId"];
        $subTotal = $_POST["subTotal"];
        $taxAmount = getTransactionTotalTax($transNo);
        $discountAmount = $_POST["discountAmount"];
        $totalAmount = $_POST["totalAmount"];
        $systemDate = date("Y-m-d");
        $systemTime= date("h:i:s");
        processCancelledTransaction($transNo, $_SESSION["store"], $_SESSION["register"], $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime);
        cancelSalesEntry($transNo);
        cancelAllItems($transNo);
        
        $transaction = getTransactionHeaders($transNo);
        while($t = mysql_fetch_assoc($transaction)){
            $sku = $t["sku"];
            $batch = getBatchAssigned($transNo, $sku);
            $b = mysql_fetch_assoc($batch);
            $batchNo = $b["batch_no"];
            $itemRecordNo = $b["item_record_no"];
            $quantity = getDeliveryEntryRemaining($batchNo, $itemRecordNo) + $b["quantity"];
            updateDeliveryEntryRemaining($batchNo, $itemRecordNo, $quantity);
        }
        
        cancelBatchReserves($transNo);
        
        unset($_SESSION["transNo"]);
    }
?>
