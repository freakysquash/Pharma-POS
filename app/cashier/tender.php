<?php
    include("../../library/config.php");
    
    if(isset($_POST["transaction"])){
        $transNo = mres($_POST["transaction"]);
        $userId = $_SESSION["userId"];
        $subTotal = $_POST["subTotal"];
        $taxAmount = getTransactionTotalTax($transNo);
        $discountAmount = $_POST["discountAmount"];
        $totalAmount = $_POST["totalAmount"];
        $totalTendered = $_POST["totalTendered"];
        $balance = $_POST["balance"];
        $systemDate = date("Y-m-d");
        $systemTime= date("h:i:s");
        $status = "Completed";
        if($_POST["type"] == "Normal"){
            processTransaction($transNo, $_SESSION["store"], $_SESSION["register"], $userId, $subTotal, $taxAmount, $discountAmount, $totalAmount, $systemDate, $systemTime, $status);
        }
        else{
            processHoldTransaction($transNo, $status);
        }
        transactionPayment($transNo, $_SESSION["store"], $_SESSION["register"], $userId, $subTotal, $discountAmount, $totalAmount, $totalTendered, $balance, $systemDate, $systemTime);
        updatePaidTransaction($transNo);
        $completedItemData = getCompletedItemTransaction($transNo);
        while($comp = mysql_fetch_assoc($completedItemData)){
            $itemData = getItemDetailsBySku($comp["sku"]);
            $item = mysql_fetch_assoc($itemData);
            $updatedCount = getCurrentItemCount($item["sku"]) - ($comp["quantity"] * getQuantityFromPackagingCode(getPackagingCodeFromSku($comp["sku"])));
            $lastUpdate = date("Y-m-d h:i:s");
            updateItemCount($item["sku"], $updatedCount, $lastUpdate);
            completeBatchMapping($transNo, $item["sku"]);
        }
        
        $transHeaders = getTransactionHeaders($transNo);
        while($t = mysql_fetch_assoc($transHeaders)){
        /* NEW BATCHING SCHEME */
            $sku = $t["sku"];
            $batch = batchLookup($sku);
            $rows = mysql_num_rows($batch);
            $b = mysql_fetch_assoc($batch);
            
            $updatedQty = $t["quantity"];
            $x = 1;
//            $y = 1;
//            
//            while($y <= $rows){
//                $i = 1;
//                $ctr = 0;
//                $count = 0;
//                
//                $y++;
//            }
            
            while($x <= $updatedQty){
                $qty = $b["remaining"];
                $qty1 = $qty - 1;
                
                $batchNo = $b["batch_no"];
                $itemRecordNo = $b["item_record_no"];
                
                if($rows <> 0){
                    $updateDeliveries = mysql_query("UPDATE deliveries SET remaining = '$qty1' WHERE sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
                }
                
                $getBatch = mysql_query("SELECT * FROM batch_mapping WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
                $batchRows = mysql_num_rows($getBatch);
                $br = mysql_fetch_assoc($getBatch);
                
                if($batchRows <> 0){
                    $batchQty = $br["quantity"] + 1;
                    $updateBatch = mysql_query("UPDATE batch_mapping SET quantity = '$batchQty' WHERE transaction_no = '$transNo' AND sku = '$sku' AND batch_no = '$batchNo' AND item_record_no = '$itemRecordNo'") or die(mysql_error());
                }
                else{
                    $batchQty = 1;
                    $status = "out";
                    $newBatch = mysql_query("INSERT INTO batch_mapping (transaction_no, sku, batch_no, item_record_no, quantity, status) VALUES ('$transNo', '$sku', '$batchNo', '$itemRecordNo', '$batchQty', '$status')") or die(mysql_error());
                }
                
                $x++;
            }
            /* -------------------------------------------------------------- */
            }
        
        unset($_SESSION["transNo"]);
    }
?>