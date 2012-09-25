<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["purchaseNo"])){
        $entryId = mres($_POST["entryId"]);
        $purchaseNo = mres($_POST["purchaseNo"]);
        $supplier = mres($_POST["supplier"]);
        $sku = mres($_POST["sku"]);
        $quantity = mres($_POST["quantity"]);
        $deliveryStatus = mres($_POST["receivedQuantity"]);
        $receivedQuantity = mres($_POST["receivedQuantity"]);
        $unitPrice = mres($_POST["unitPrice"]);
        $amount = sprintf("%.2f", $quantity * $unitPrice);
        $vatAmount = sprintf("%.2f", $amount - ($amount / 1.12));
        $vatable = sprintf("%.2f", $amount - $vatAmount);
        $remainingBalance = sprintf("%.2f", $amount - $amount);
        $remaining = $receivedQuantity;
        $discrepancy = $quantity - $receivedQuantity;
        $receivedBy = $_SESSION["userId"];
        $dateReceived = date("Y-m-d h:i:s");
        $deliveryNo = mres($_POST["deliveryNo"]);
        $type = $_POST["type"];
        if($type == "Pending"){
            updateItemIn($entryId, $purchaseNo, $sku, $deliveryNo, $discrepancy, $remaining);
        }
        else{
            $preDeliveryStatus = getPOEntryDeliveryStatus($purchaseNo, $entryId);
            $remaining = $preDeliveryStatus + $remaining;
            updateItemIn($entryId, $purchaseNo, $sku, $deliveryNo, $discrepancy, $remaining);
        }
        $status = null;
        if(getPOEntriesDeliveryStatus($purchaseNo) != getTotalPurchaseQuantity($purchaseNo)){
            $status = "Incomplete";
        }
        else{
            $status = "Completed";
        }
        if(empty($_POST["salesInvoiceNo"])){
            $salesInvoice = null;
        }
        else{
            $salesInvoice = mres($_POST["salesInvoiceNo"]);
        }
        if($discrepancy == 0){
            $receiveStatus = "Completed";
        }
        else{
            $receiveStatus = "Incomplete";
        }
        $batchNo = assignBatchNo($purchaseNo, $deliveryNo, $salesInvoice);
        $itemRecordNo = assignItemRecordNo($batchNo);
        $expiration = mres($_POST["expiration"]);
        $expense = mres($_POST["expense"]);
        $docDate = date("Y-m-d");
        $docTime = date("h:i:s");
        $updatedStockCount = getStockOnHandBySku($sku) + $receivedQuantity;
        receiveDelivery($purchaseNo, $supplier, $sku, $quantity, $amount, $unitPrice, $vatable, $vatAmount, $remaining, $discrepancy, $receivedBy, $receiveStatus, $dateReceived, $deliveryNo, $salesInvoice, $batchNo, $itemRecordNo, $expiration, $expense, $docDate, $docTime);
        updateOrderStatus($purchaseNo, $status, $remainingBalance, $dateReceived, $deliveryStatus);
        
        updateInventoryUponDelivery($sku, $updatedStockCount, $dateReceived);
    }
?>

