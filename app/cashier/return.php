<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["t"])){
        $transNo = $_POST["t"];
        $id = mres($_POST["id"]);
        $quantity = mres($_POST["quantity"]);
        $sku = getSkuByCashierEntryId($id);
        
        /********************************************************************/
        
        $entries = getTransactionHeaderById($id);
        $e = mysql_fetch_assoc($entries);
                
        if($quantity > $e["quantity"]){
            $quantity = $e["quantity"];
        }
        
        $store = $_SESSION["store"];
        $register = $_SESSION["register"];
        $userId = $_SESSION["userId"];
        $description = getDescriptionBySku($sku);
        $price = getUnitPriceBySku($sku);
        $subtotal = sprintf("%.2f", $quantity * $price);
        $subtotalAmount = sprintf("%.2f", $subtotal * (-1));
        $taxCode = $e["tax_code"];
        $tax = sprintf("%.2f", $quantity * ($e["tax_amount"] / $e["quantity"]));
        $taxAmount = $tax * (-1);
        $discountCode = $e["discount_code"];
        $discount = sprintf("%.2f", $quantity * ($e["discount_amount"] / $e["quantity"]));
        $discountAmount = $discount * (-1);
        $total = sprintf("%.2f", ($subtotal - $discount) * (-1));
        $systemDate = date("Y-m-d");
        $systemTime = date("h:i:s");
        newTransactionHeader($transNo, $store, $register, $userId, $sku, $description, $quantity, $price, $subtotalAmount, $total, $discountCode, $discountAmount, $taxCode, $taxAmount, $systemDate, $systemTime);
    }
?>