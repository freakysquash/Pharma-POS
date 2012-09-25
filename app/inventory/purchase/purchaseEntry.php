<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["po"])){
        $purchaseNo = mres($_POST["po"]);
        $sku = mres($_POST["sku"]);
        $quantity = mres($_POST["quantity"]);
        $price = mres($_POST["unitPrice"]);
        $totalAmount = sprintf("%.2f", $quantity * $price);
        $waiting = $quantity;
        $deliveryStatus = 0;
        $systemDate = date("Y-m-d");
        $systemTime= date("h:i:s");
        newPurchaseOrder($purchaseNo, $sku, $quantity, $price, $totalAmount, $waiting, $deliveryStatus, $systemDate, $systemTime);
    }
?>
<style>
    
    .purchaseEntryRow input[type="text"] {
        display:inline-block;
    }
    
    #poEntryQuantity {
        width:28px;
        border:1px solid #ccc;
    }
    
    #poEntryDescription {
        width:248px;
        border:none;
        margin:0 0 0 30px;
    }
    
    #poEntryUnitPrice {
        width:68px;
        border:none;
    }
    
    #poEntryAmount {
        width:50px;
        border:none;
    }
    
</style>

<?php
    $poData = getPurchaseOrderByPONo($_GET["po"]);
    while($po = mysql_fetch_assoc($poData)){
?>
<div class="purchaseEntryRow">
    <input type="text" id="poEntryQuantity" name="poEntryQuantity" readonly="readonly" value="<?php echo $po["quantity"]; ?>"/>
    <input type="text" id="poEntryDescription" name="poEntryDescription" value="<?php echo getDescriptionBySku($po["sku"]); ?>"/>
    <input type="text" id="poEntryUnitPrice" name="unitPrice" value="<?php echo $po["unit_price"]; ?>"/>
    <input type="text" id="poEntryAmount" name="poEntryAmount" value="<?php echo $po["total_amount"]; ?>"
</div>
<?php
    } 
?>

