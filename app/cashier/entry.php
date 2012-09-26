<?php
    include("../../library/config.php");
    
    if(isset($_POST["transaction"])){
        $transNo = $_POST["transaction"];
        $userId = $_SESSION["userId"];
        $sku = $_POST["sku"];
        $itemData = getItemDetailsBySku($sku);
        $result = mysql_num_rows($itemData);
        $error = 0;
        if($result == 0){
            $error = 1;
        }
        if(getStockOnHandBySku($sku) <= 0){
            $error = 2;
        }
        $item = mysql_fetch_assoc($itemData);
        $description = $item["description_1"];
        $quantity = 1;
        $price = $item["price"];
        $taxCode = getTaxCodeBySku($sku);
        $subtotal = sprintf("%.2f", $quantity * $price);
        $taxRate = "1." . str_replace(".", "", getTaxRate($taxCode));
        $taxAmount = sprintf("%.2f", $subtotal - ($subtotal / $taxRate));
        $discountAmount = 0.00;
        $discountCode = "0000";
        $totalAmount = ($quantity * $price) - $discountAmount;
        $subtotal = $totalAmount;
        $systemDate = date("Y-m-d");
        $systemTime= date("h:i:s");
        if($error == 0){
            //if(checkTransactionNoExistence($transNo) != 1){
            //    newTransaction($transNo);
            //}
            
            /* BATCH MAPPING */
//            $freeBatch = batchLookup($sku);
//            $b = mysql_fetch_assoc($freeBatch);
//            
//            $batchNo = $b["batch_no"];
//            $itemRecordNo = $b["item_record_no"];
//            $remaining = $b["remaining"];
//            $reserve = $remaining - $quantity;
//            reserveFromBatch($batchNo, $itemRecordNo, $reserve);
//            mapBatch($transNo, $sku, $batchNo, $itemRecordNo, $quantity);
            /* -------------------------------------------------------------- */
            
            newTransactionHeader($transNo, $_SESSION["store"], $_SESSION["register"], $userId, $sku, $description, $quantity, $price, $subtotal, $totalAmount, $discountCode, $discountAmount, $taxCode, $taxAmount, $systemDate, $systemTime);
        }
    }
?>

<script>
    
    $("input[name=itemEntryId]").each(function(){
        $(this).attr("id", $(this).val());
    });

    $("input[name=entryTransNo]").each(function(){
        $(this).attr("id", $(this).val());
        $(".removeItemEntry").attr("data-trans", $(this).val());
        $(".applyDiscount").attr("data-trans", $(this).val());
    });
    
    $("#entryItemsControlDialog").dialog({
        title: "Edit Item Entry",
        autoOpen:false,
        draggable:false,
        resizable:false,
        modal:true,
        minHeight:120,
        width:300
    })
    
    $(".entryItemsControl").click(function(){
        $("#entryItemsControlDialog").dialog("open");
        $("input[name=itemEntryId]").val($(this).attr("data-id"));
        $("input[name=entryTransNo]").val($(this).attr("data-trans"));
    })
    
    $(".editItemEntry").unbind("click").click(function(e){
         e.preventDefault();
         var entryTransNo =  $("input[name=entryTransNo]").val();
         var itemEntryId = $("input[name=itemEntryId]").val();
         var quantity = $("input[name=enteredQuantity]").val();
         $.ajax({
             type: "POST",
             url: "/app/cashier/update.php",
             data: { entryTransNo:entryTransNo, itemEntryId: itemEntryId, quantity:quantity},
             success: function(){
                $("input[name=itemEntryId]").val("");
                $("input[name=itemEntryId]").val("");
                $("input[name=enteredQuantity]").val("");
                $("#entryItemsControlDialog").dialog("close");
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(1000).delay(100).fadeIn(400);
                $.getJSON("/app/cashier/total.php?t=" + entryTransNo, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + entryTransNo, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + entryTransNo, function (data) {
                     $("input[name=summaryTax]").val(data.totalTax);
                });
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("input[name=summaryDiscount]").val(totalDiscount)
                })
                $.getJSON("/app/cashier/lastItemEntry.php?t=" + entryTransNo, function (data) {
                    $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/manage/items/image.php?s=" + data.sku);
                })
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
             }
         })
    })
    $(".removeItemEntry").unbind("click").click(function(e){
        e.preventDefault();
        var entryTransNo =  $("input[name=entryTransNo]").val();
        var itemEntryId = $("input[name=itemEntryId]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/remove.php",
            data: { itemEntryId: itemEntryId },
            success: function(){
                $("input[name=itemEntryId]").val("")
                $("input[name=itemEntryId]").val("")
                $("#entryItemsControlDialog").dialog("close");
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(1000).delay(100).fadeIn(400);
                $.getJSON("/app/cashier/total.php?t=" + entryTransNo, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + entryTransNo, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + entryTransNo, function (data) {
                     $("input[name=summaryTax]").val(data.totalTax);
                });
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("input[name=summaryDiscount]").val(totalDiscount)
                })
                $.getJSON("/app/cashier/lastItemEntry.php?t=" + entryTransNo, function (data) {
                    $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/manage/items/image.php?s=" + data.sku);
                })
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
            }
        })
    })
    
    $("#editItemEntryDialog").dialog({
        title: "Edit Item Entry",
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable:false
    });
    
    $(".applyDiscount").unbind("click").click(function(e){
        e.preventDefault();
        var entryTransNo = $(this).attr("data-trans");
        var entry = $(this).attr("data-id");
        var discountCode = $("input[name=discounts]").attr("data-discount");
        var discountRate = $("input[name=discounts]").attr("data-rate");
        $.ajax({
            type: "POST",
            url: "/app/cashier/applyItemDiscount.php",
            data: {entryTransNo:entryTransNo, entry:entry, discountCode:discountCode, discountRate:discountRate},
            success: function(){
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(100).delay(100).fadeIn(400);
                $.getJSON("/app/cashier/total.php?t=" + entryTransNo, function(data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                })
                $.getJSON("/app/cashier/due.php?t=" + entryTransNo, function(data) {
                    $("input[name=subTotal]").val(data.subtotal);
                })
                $.getJSON("/app/cashier/taxAmount.php?t=" + entryTransNo, function (data) {
                     $("input[name=summaryTax]").val(data.totalTax);
                });
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("input[name=summaryDiscount]").val(totalDiscount)
                })
                $("input[name=discounts]").prop("checked", false);
                $("#entryItemsControlDialog").dialog("close");
            }
        })
    })
        
    $("input[name=stockChecker]").each(function(){
        var s = parseInt($(this).attr("data-stock"));
        var r = parseInt($(this).attr("data-reorder"));
        if(s == r || r > s){
            $(this).parent().css({ "background":"#fc4e4e" });
            $(this).parent().find("input").css({ "background":"#fc4e4e", "color":"#fff" });
        }
    })
    
</script>

<?php
    if(isset($_GET["t"])){
        $entryData = getHeaderByTransNoEntry($_GET["t"]);
        while($entry = mysql_fetch_assoc($entryData)){
?>

<div class="cid_row">
    <input type="hidden" name="stockChecker" data-stock="<?php echo getStockOnHandBySku($entry["sku"]) ?>" data-reorder="<?php echo getStockReorderMinBySku($entry["sku"]); ?>"/>
    <div class="display_item">
        <div class="item_frame">
                <img src="http://<?php echo ROOT; ?>/app/manage/items/image.php?s=<?php echo $entry["sku"]; ?>"/>
        </div>
        <h2 class="display_name_item"><a href="#" class="entryItemsControl" data-trans="<?php echo $_GET["t"]; ?>" data-id="<?php echo $entry["id"]; ?>"><?php echo $entry["description"]; ?></a></h2>
    </div>
    <div class="display_qty">
        <input type="text" id="qty_value" class="qty_value" readonly="readonly" value="<?php echo $entry["quantity"]; ?>" />
    </div>
    <div class="display_amount">
        <div class="amt_value"><span class="enteredAmount"><?php echo sprintf("%.2f", $entry["total_amount"]); ?></span></div>
    </div>
    <input type="hidden" name="flatAmount" value="<?php echo $entry["price"]; ?>"/>
</div>
<div class="sepa"></div>
<?php
        }
    }
?>
<div id="dialogs">
    <div id="entryItemsControlDialog" class="ui-dialog-form">
        <input type="hidden" name="entryTransNo"/>
        <input type="hidden" name="itemEntryId"/>
        <div>
            <label>Quantity:</label>
            <input type="text" name="enteredQuantity" value="<?php echo $entry["quantity"]; ?>"/>
            <br/><br/>
        </div>
        <div>
            <button class="editItemEntry" data-id="">Update Qty</button>
            <button class="removeItemEntry" data-id="">Remove</button>
        </div>
    </div>
</div>


