<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["quantity"])){
        $id = $_POST["itemEntryId"];
        $sku = getSkuByCashierEntryId($id);
        $unitPrice = getUnitPriceBySku($sku);
        $taxRate = "1." . intval(getTaxRateBySku($sku));
        
        $quantity = $_POST["quantity"];
        $amount = sprintf("%.2f", $quantity * $unitPrice);
        $taxAmount = sprintf("%.2f", $amount - ($amount / $taxRate));
        $subtotal = sprintf("%.2f",$quantity * $unitPrice);
        if(getStockOnHandBySku($sku) >= $quantity){
            updateEntryQuantity($id, $quantity, $subtotal, $amount, $taxAmount);
        }       
            
        /*
        $freeBatch = batchLookup($e["sku"]);
        $b = mysql_fetch_assoc($freeBatch);
        $batchNo = $b["batch_no"];
        $itemRecordNo = $b["item_record_no"];
        $remaining = $b["remaining"];
        $reserve = ($remaining + getBatchReservedQuantity($e["transaction_no"], $e["sku"], $batchNo, $itemRecordNo)) - $_POST["quantity"];
        reserveFromBatch($batchNo, $itemRecordNo, $reserve);
        mapBatch($e["transaction_no"], $e["sku"], $batchNo, $itemRecordNo, $quantity);
         * /
        /* -------------------------------------------------------------- */
    }
    
//    if(isset($_POST["fa"])){
//        $id = $_POST["itemEntryId"];
//        $amount = $_POST["fa"];
//        if($_POST["type"] == "xtax"){
//            $finalAmount = sprintf("%.2f", $totalAmount);
//        }
//        if($_POST["type"] == "ytax"){
//            $finalAmount = sprintf("%.2f", $_POST["fa"] - getTransactionHeaderDiscount($id));
//        }
//        $tax = $_POST["ta"];
//        updateEntryTax($id, $finalAmount, $tax);
//    }
?>
