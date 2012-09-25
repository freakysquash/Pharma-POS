<?php
    include("../../library/config.php");
    
    if(isset($_POST["itemEntryId"])){
        removeItemEntry($_POST["itemEntryId"]);
        $item = getTransactionHeaderById($_POST["itemEntryId"]);
        $i = mysql_fetch_assoc($item);
        $transNo = $i["transaction_no"];
        $sku = $i["sku"];
        $batch = getBatchAssigned($transNo, $sku);
        $b = mysql_fetch_assoc($batch);
        $batchNo = $b["batch_no"];
        $itemRecordNo = $b["item_record_no"];
        removeBatchReserves($transNo, $sku, $batchNo, $itemRecordNo);
        $quantity = getDeliveryEntryRemaining($batchNo, $itemRecordNo) + $b["quantity"];
        updateDeliveryEntryRemaining($batchNo, $itemRecordNo, $quantity);
    }
?>
