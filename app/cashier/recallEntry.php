<?php
    include("../../library/config.php");
    $entryData = getOnHoldHeaderByTransNoEntry($_GET["t"]);
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
            var amount = parseFloat(total_a + total_t).toFixed(2);
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
        height:250,
        width:250
    })
    
    $(".entryItemsControl").click(function(e){
        $(".editItemEntry").attr("data-id", $(this).attr("data-id"));
        $(".removeItemEntry").attr("data-id", $(this).attr("data-id"));
        $(".applyDiscount").attr("data-id", $(this).attr("data-id"));
        $("ui-dialog-title").text($(this).parent().siblings("input[name=enteredDescription]").val());
        $("#entryItemsControlDialog").dialog({ position: [e.pageX, e.pageY] }).dialog("open");
    })
    
    $(".removeItemEntry").click(function(e){
        e.preventDefault();
        $("#idHolder").val($(this).attr("data-id"));
        $("#transNoHolder").val($(this).attr("data-trans"));
        var itemEntryId = $("#idHolder").val();
        var entryTransNo = $("#transNoHolder").val();
        $.ajax({
            type: "POST",
            url: "/cashier/sales/remove.php",
            data: { itemEntryId: itemEntryId },
            success: function(){
                $("#idHolder").val("");
                $("#entryTransNo").val("");
                $("#entryItemsControlDialog").dialog("close");
                $(".cashier-entered-items").load("/cashier/sales/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(1000).delay(100).fadeIn(400);
                $.getJSON("/cashier/sales/total.php?t=" + entryTransNo, function(data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                })
                $.getJSON("/cashier/sales/due.php?t=" + entryTransNo, function(data) {
                    $("input[name=subTotal]").val(data.subtotal);
                })
                $.getJSON("/cashier/sales/taxAmount.php?t=" + entryTransNo, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $("#receiptDialog").load("/cashier/sales/receipt.php?t=" + entryTransNo);
            }
        })
    })
    
    $(".editItemEntry").click(function(e){
        e.preventDefault();
        $("#idHolder").val($(this).attr("data-id"));
        $("#transNoHolder").val($(this).attr("data-trans"));
        var dataId = $(this).attr("data-id");
        var itemEntryId = $("#idHolder").val();
        var quantity = $(".entryItemsControl[data-id=" + dataId + "]").siblings("input[name=enteredQuantity]").val();
        var unitPrice = parseFloat($(".entryItemsControl[data-id=" + dataId + "]").siblings("input[name=flatAmount]").val()).toFixed(2);
        var entryTransNo = $("#transNoHolder").val();
        $.ajax({
            type: "POST",
            url: "/cashier/sales/update.php",
            data: { itemEntryId: itemEntryId, quantity: quantity, unitPrice: unitPrice },
            success: function(){
                $("#idHolder").val("");
                $("#entryTransNo").val("");
                $("#entryItemsControlDialog").dialog("close");
                $(".cashier-entered-items").load("/cashier/sales/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(2000).delay(100).fadeIn(400);
                $.getJSON("/cashier/sales/total.php?t=" + entryTransNo, function(data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                })
                $.getJSON("/cashier/sales/due.php?t=" + entryTransNo, function(data) {
                    $("input[name=subTotal]").val(data.subtotal);
                })
                $.getJSON("/cashier/sales/taxAmount.php?t=" + entryTransNo, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $("#receiptDialog").load("/cashier/sales/receipt.php?t=" + entryTransNo);
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
        var a = parseFloat(q * up).toFixed(3);
        var ta = 0;
        var fa = 0;
        var type = "";
        $(this).bind("mousedown change", function(){
            if($(this).prop("checked")){
                $(this).attr("data-tax", "0.00");
                ta = $(this).attr("data-tax");
                fa = parseFloat(a).toFixed(2);
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
                url: "/cashier/sales/update.php",
                data: { itemEntryId: itemEntryId, fa:fa, ta:ta, type: type },
                success: function(){
                    $("#idHolder").val("");
                    $("#entryTransNo").val("");
                    $(".cashier-entered-items").load("/cashier/sales/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(100).delay(100).fadeIn(400);
                    $.getJSON("/cashier/sales/total.php?t=" + entryTransNo, function(data) {
                        $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    })
                    $.getJSON("/cashier/sales/due.php?t=" + entryTransNo, function(data) {
                        $("input[name=subTotal]").val(data.subtotal);
                    })
                    $.getJSON("/cashier/sales/taxAmount.php?t=" + entryTransNo, function (data) {
                        $("#summaryTax").text(data.totalTax)
                    });
                    $("#receiptDialog").load("/cashier/sales/receipt.php?t=" + entryTransNo);
                }
            })
        })
    })
    
    $("#editItemEntryDialog").dialog({
        title: "Edit Item Entry",
        autoOpen: false,
        minHeight: 120,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable:false
    });
    
    $(".editItemEntry").click(function(e){
        e.preventDefault();
        $("#editItemEntryDialog").dialog("open");
    })
    
    $(".applyDiscount").click(function(e){
        e.preventDefault();
        $(this).attr("disabled", "disabled");
        var entryTransNo = $(this).attr("data-trans");
        var entry = $(this).attr("data-id");
        var discountCode = $("input[name=discounts]").attr("data-discount");
        var discountRate = $("input[name=discounts]").attr("data-rate");
        $.ajax({
            type: "POST",
            url: "/cashier/sales/applyItemDiscount.php",
            data: {entryTransNo:entryTransNo, entry:entry, discountCode:discountCode, discountRate:discountRate},
            success: function(){
                $(".cashier-entered-items").load("/cashier/sales/entry.php?t=" + entryTransNo, function(){}).hide().slideDown(100).delay(100).fadeIn(400);
                $.getJSON("/cashier/sales/total.php?t=" + entryTransNo, function(data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                })
                $.getJSON("/cashier/sales/due.php?t=" + entryTransNo, function(data) {
                    $("input[name=subTotal]").val(data.subtotal);
                })
                $.getJSON("/cashier/sales/taxAmount.php?t=" + entryTransNo, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $("#receiptDialog").load("/cashier/sales/receipt.php?t=" + entryTransNo);
                $.getJSON("/cashier/sales/discount.php?t=" + entryTransNo, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
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
            $(this).parent().css({ "background":"#fcf0f0" });
            $(this).parent().find("input").css({ "background":"#fcf0f0" });
        }
    })
    
    //$(".enteredItemsRow:odd").css("background-color","#e1e1e1");
    //$(".enteredItemsRow:odd").find("input").css("background-color","#e1e1e1");
    //$(".enteredItemsRow:even").css("background-color","#ffffff"); 

</script>

<style>
    
    .enteredItemsRow {
        border-bottom:1px solid #ccc;
        margin:0;
        width:480px;
    }
    
    .enteredDescription {
        border:none;
        margin:2px 0 2px 0;
        width:190px;
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
        margin: 2px 0 2px 20px;
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
</style>

<?php
    while($entry = mysql_fetch_assoc($entryData)){
?>
<div class="enteredItemsRow grid_12">
    <input type="hidden" name="itemEntryId" value="<?php echo $entry["id"]; ?>"/>
    <input type="hidden" name="entryTransNo" value="<?php echo $entry["transaction_no"]; ?>"/>
    <input type="hidden" name="stockChecker" data-stock="<?php echo getStockOnHandBySku($entry["sku"]) ?>" data-reorder="<?php echo getStockReorderMinBySku($entry["sku"]); ?>"/>
    <input type="text" class="enteredDescription" name="enteredDescription" readonly="readonly" value="<?php echo $entry["description"]; ?>"/>
    <input type="text" class="enteredQuantity" name="enteredQuantity" value="<?php echo $entry["quantity"]; ?>"/>
    <input type="checkbox" class="toggleEntryTax" name="toggleEntryTax" data-tax="<?php echo $entry["tax_amount"]; ?>" id="<?php echo $entry["id"]; ?>"/>
    <input type="text" class="enteredAmount" name="enteredAmount" value="<?php echo sprintf("%.2f", $entry["total_amount"]); ?>"/>
    <input type="hidden" name="flatAmount" value="<?php echo $entry["price"]; ?>"/>
    <button class="entryItemsControl" data-id="<?php echo $entry["id"]; ?>">Menu</button>
</div>
<?php
    }
?>
<div id="dialogs" class="ui-dialog-form">
    <div id="entryItemsControlDialog">
        <button class="editItemEntry" data-id="">Edit</button>
        <button class="removeItemEntry" data-id="">Remove</button>
        <br/>
        <br/>
        <p>Discounts:</p>
        <br/>
        <?php
            $discountData = getDiscounts();
            while($d = mysql_fetch_assoc($discountData)){
        ?>
        <label><input type="radio" name="discounts" data-discount ="<?php echo $d["code"]; ?>" data-rate="<?php echo $d["rate"]; ?>"/><?php echo $d["type"]; ?></label><br/>
        <?php
            }
        ?>
        <br/>
        <button class="applyDiscount" data-id="">Apply Discount</button>
    </div>
</div>