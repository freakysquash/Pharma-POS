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
    
     $("input[name=toggleEntryTax]").each(function(){
         if($(this).attr("data-tax") == "0.00"){
             $(this).prop("checked", false);
         }
         else{
             $(this).prop("checked", true);
         }
     })
     
     $("input[name=enteredQuantity]").each(function(){
        $(this).bind("change keyup", function(){
            var q = $(this).val();
            var f = $(this).siblings("input[name=flatAmount]").val();
            var total_a = q * f;
            var t = $(this).siblings(":checkbox").attr("data-tax");
            var total_t = t * q;
            var amount = parseFloat(total_a).toFixed(2);
            $(this).siblings("input[name=enteredAmount]").val(amount);
        });
    });

    
    $("input[name=itemEntryId]").each(function(){
        $(this).attr("id", $(this).val());
    });

    $("input[name=entryTransNo]").each(function(){
        $(this).attr("id", $(this).val());
        $(".removeItemEntry").attr("data-trans", $(this).val());
        $(".editItemEntry").attr("data-trans", $(this).val());
        $(".toggleEntryTax").attr("data-trans", $(this).val());
        $(".applyDiscount").attr("data-trans", $(this).val());
    });
    
    $("#entryItemsControlDialog").dialog({
        autoOpen:false,
        draggable:false,
        resizable:false,
        modal:true,
        minHeight:120,
        width:300
    })
    
    $(".entryItemsControl").click(function(e){
        $(".editItemEntry").attr("data-id", $(this).attr("data-id"));
        $(".removeItemEntry").attr("data-id", $(this).attr("data-id"));
        $(".applyDiscount").attr("data-id", $(this).attr("data-id"));
        $("ui-dialog-title").text($(this).parent().siblings(".enteredDescription").text());
        
        $.getJSON("/app/cashier/checkEntryDiscount.php?i=" + $(this).attr("data-id"), function(data){
            if(data.entryDiscount > 0){
                $(".applyDiscount").attr("disabled", "disabled");
            }
            else{
                $(".applyDiscount").removeAttr("disabled");
            }
        })
        
        $("#entryItemsControlDialog").dialog({ position: [e.pageX, e.pageY] }).dialog("open");
    })
    
    $(".removeItemEntry").unbind("click").click(function(e){
        e.preventDefault();
        $("#idHolder").val($(this).attr("data-id"));
        $("#transNoHolder").val($(this).attr("data-trans"));
        var itemEntryId = $("#idHolder").val();
        var entryTransNo = $("#transNoHolder").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/remove.php",
            data: { itemEntryId: itemEntryId },
            success: function(){
                $("#idHolder").val("");
                $("#entryTransNo").val("");
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
                    $("#summaryTax").text(data.totalTax)
                });
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
                $.getJSON("/app/cashier/lastItemEntry.php?t=" + entryTransNo, function (data) {
                    $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/manage/items/image.php?s=" + data.sku);
                })
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
            }
        })
    })
    
    $(".editItemEntry").unbind("click").click(function(){
        $(".editItemEntry").attr("disabled", "disabled");
        $("#idHolder").val($(this).attr("data-id"));
        $("#transNoHolder").val($(this).attr("data-trans"));
        var dataId = $(this).attr("data-id");
        var itemEntryId = $("#idHolder").val();
        var quantity = $(".entryItemsControl[data-id=" + dataId + "]").siblings("input[name=enteredQuantity]").val();
        var unitPrice = parseFloat($(".entryItemsControl[data-id=" + dataId + "]").siblings("input[name=flatAmount]").val()).toFixed(2);
        var entryTransNo = $("#transNoHolder").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/update.php",
            data: { itemEntryId: itemEntryId, quantity: quantity, unitPrice: unitPrice },
            success: function(){
                $(".editItemEntry").removeAttr("disabled");
                $("#idHolder").val("");
                $("#entryTransNo").val("");
                $("#entryItemsControlDialog").dialog("close");
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(2000).delay(100).fadeIn(400);
                $.getJSON("/app/cashier/total.php?t=" + entryTransNo, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + entryTransNo, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + entryTransNo, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                });
                $.getJSON("/app/cashier/lastItemEntry.php?t=" + entryTransNo, function(data) {
                    $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/app/manage/items/image.php?s=" + data.sku);
                });
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
            }
        })
    })
    
    $(".toggleEntryTax").each(function(){
        $("#idHolder").val($(this).attr("id"));
        $("#transNoHolder").val($(this).attr("data-trans"));
        var itemEntryId = $("#idHolder").val();
        var entryTransNo = $("#transNoHolder").val();
        var q = $(this).siblings("input[name=enteredQuantity]").val();
        var up = $(this).siblings("input[name=flatAmount]").val();
        var a = parseFloat(q * up).toFixed(2);
        var ta = 0;
        var fa = 0;
        var type = "";
        $(this).bind("mousedown change", function(){
            if($(this).prop("checked")){
                $(this).attr("data-tax", "0.00");
                ta = $(this).attr("data-tax");
                fa = a;
                type = "xtax";
            }
            else{
                $(this).attr("data-tax", a * 0.12);
                ta = $(this).attr("data-tax");
                fa = parseFloat(a + ta).toFixed(2);
                type = "ytax";
            }
            $.ajax({
                type: "POST",
                url: "/app/cashier/update.php",
                data: { itemEntryId: itemEntryId, fa:fa, ta:ta, type: type },
                success: function(){
                    $("#idHolder").val("");
                    $("#entryTransNo").val("");
                    $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(100).delay(100).fadeIn(400);
                    $.getJSON("/app/cashier/total.php?t=" + entryTransNo, function(data) {
                        $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    })
                    $.getJSON("/app/cashier/due.php?t=" + entryTransNo, function(data) {
                        $("input[name=subTotal]").val(data.subtotal);
                    })
                    $.getJSON("/app/cashier/taxAmount.php?t=" + entryTransNo, function (data) {
                        $("#summaryTax").text(data.totalTax)
                    });
                    $.getJSON("/app/cashier/lastItemEntry.php?t=" + entryTransNo, function (data) {
                        $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/app/manage/items/image.php?s=" + data.sku);
                    })
                    $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
                }
            })
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
                    $("#summaryTax").text(data.totalTax)
                });
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + entryTransNo);
                $.getJSON("/app/cashier/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
                $("input[name=discounts]").prop("checked", false);
                $("#entryItemsControlDialog").dialog("close");
            }
        })
    })
    
    
    
    $("input[name=enteredQuantity]").focus(function(){
        $("input[name=focusnotifier]").val("1")
    }).blur(function () {
        $("input[name=focusnotifier]").val("0")
    });
    
    
    $("input[name=stockChecker]").each(function(){
        var s = parseInt($(this).attr("data-stock"));
        var r = parseInt($(this).attr("data-reorder"));
        if(s == r || r > s){
            $(this).parent().css({ "background":"#fc4e4e" });
            $(this).parent().find("input").css({ "background":"#fc4e4e", "color":"#fff" });
        }
    })
    
    //$(".enteredItemsRow:odd").css("background-color","#e1e1e1");
    //$(".enteredItemsRow:odd").find("input").css("background-color","#e1e1e1");
    //$(".enteredItemsRow:even").css("background-color","#ffffff"); 

</script>

<style>
    
    .enteredItemsRow {
        border-bottom:1px solid #ccc;
        margin:0 0 0 1px !important;
        width:480px;
    }
    
    .enteredDescription {
        border:none;
        margin:4px 2px 2px 0;
        width:210px;
        padding:0 0 0 5px;
    }
    
    .enteredQuantity {
        border:1px solid #ccc;
        padding:3px;
        width:24px;
        margin:2px 0 2px 10px;
    }
    
    .toggleEntryTax {
        margin:10px 0 0 30px;
    }
    
    .enteredAmount {
        border:none;
        text-align:center;
        width:70px;
        margin: 2px 0 2px 50px;
    }
    
    .entryItemsControl {
        height:30px;
        margin: 2px 0 2px 15px;
        background-image: -ms-linear-gradient(top, #F0F0F0 0%, #E3DEDE 100%);
        background-image: -moz-linear-gradient(top, #F0F0F0 0%, #E3DEDE 100%);
        background-image: -o-linear-gradient(top, #F0F0F0 0%, #E3DEDE 100%);
        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #F0F0F0), color-stop(1, #E3DEDE));
        background-image: -webkit-linear-gradient(top, #F0F0F0 0%, #E3DEDE 100%);
        background-image: linear-gradient(to bottom, #F0F0F0 0%, #E3DEDE 100%);
        border:1px solid #ccc;
    }
    
    .entryItemImage {
        height:30px;
        width:30px;
    }
</style>

<?php
    if(isset($_GET["t"])){
        $entryData = getHeaderByTransNoEntry($_GET["t"]);
        while($entry = mysql_fetch_assoc($entryData)){
?>
<div class="enteredItemsRow grid_16">
    <input type="hidden" name="itemEntryId" value="<?php echo $entry["id"]; ?>"/>
    <input type="hidden" name="entryTransNo" value="<?php echo $entry["transaction_no"]; ?>"/>
    <input type="hidden" name="stockChecker" data-stock="<?php echo getStockOnHandBySku($entry["sku"]) ?>" data-reorder="<?php echo getStockReorderMinBySku($entry["sku"]); ?>"/>
    <img src="http://<?php echo ROOT; ?>/app/manage/items/image.php?s=<?php echo $entry["sku"]; ?>" class="entryItemImage"/>
    <span class="enteredDescription"><a href="#"><?php echo $entry["description"]; ?></span></span>
    <input type="text" class="enteredQuantity" name="enteredQuantity" readonly="readonly" value="<?php echo $entry["quantity"]; ?>"/>
    <input type="text" class="enteredAmount" name="enteredAmount" value="<?php echo sprintf("%.2f", $entry["total_amount"]); ?>"/>
    <input type="hidden" name="flatAmount" value="<?php echo $entry["price"]; ?>"/>
    <button class="entryItemsControl" data-id="<?php echo $entry["id"]; ?>">Menu</button>
</div>
<?php
        }
    }
?>
<div id="dialogs">
    <div id="entryItemsControlDialog" class="ui-dialog-form">
        <button class="editItemEntry" data-id="">Update Qty</button>
        <button class="removeItemEntry" data-id="">Remove</button>
    </div>
</div>


