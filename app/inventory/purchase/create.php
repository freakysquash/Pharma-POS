<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    $purchaseNo = getAvailablePONo();
?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#supplier").change(function(){
            $("#item").find("option").remove().end();
            $.getJSON("/app/inventory/purchase/supplierItemList.php?s=" + $("#supplier").val(), function(data){
                $.each(data, function(i,item)
                {
                    $("#item").append("<option value='" + item.sku + "'>" + item.description_1 + "</option>")
                });
            })
            $("#itemAutoComplete").autocomplete({
                source: "/manage/suppliers/itemList.php?s=" + $("#supplier").val(),
                width:300,
                select: function(event, ui) {
                    $("input[name=item]").val(ui.item.value);
                    $("#selectedItem").text(ui.item.value);
                    $("#supplier").attr("disabled", true);
                    $.getJSON("/manage/items/checkItemSku.php?d=" + ui.item.value, function(data){
                        $("#itemHolder").val(data.sku);
                    })
                    $.getJSON("/app/inventory/purchase/checkReorderLevel.php?s=" + $("#itemHolder").val(), function(data){
                        $("input[name=poEntryQuantity]").val(data.reorder);
                        $("input[name=poEntryQuantity]").attr("min", data.reorder);
                    })
                    /*$("input[name=poEntryUnitPrice]").val("");*/
                    $("input[name=poEntryQuantity]").val("");
                    $("#selectItem").dialog("open");
                }
            })
        })
        
        $("#item").change(function(){
            $("#supplier").attr("disabled", true);
            $("#selectedItem").text($("#item option:selected").text());
            $.getJSON("/app/inventory/purchase/checkReorderLevel.php?s=" + $("#item").val(), function(data){
                $("input[name=poEntryQuantity]").val(data.reorder);
                $("input[name=poEntryQuantity]").attr("min", data.reorder);
            })
            /*$("input[name=poEntryUnitPrice]").val("");*/
            $("#itemHolder").val($("#item").val());
            $("input[name=poEntryQuantity]").val("");
            $("#selectItem").dialog("open");
        })
        
        $(".purchaseEntries").load("/app/inventory/purchase/purchaseEntry.php?po=" + $("#purchaseNo").val())
        
        $("#selectItem").dialog({
            title: "Item Purchase Order",
            autoOpen:false,
            modal:true,
            minHeight:200,
            width:300,
            resizable:false,
            buttons: {
                "Enter": function(){
                    var po = $("#purchaseNo").val();
                    var sku = $("#itemHolder").val();
                    var quantity = $("input[name=poEntryQuantity]").val();
                    var unitPrice = $("input[name=poEntryUnitPrice]").val();
                    $.ajax({
                        type: "POST",
                        url: "/app/inventory/purchase/purchaseEntry.php",
                        data: { po: po, sku: sku, quantity: quantity, unitPrice: unitPrice },
                        success: function(){
                            $("input[name=poEntryQuantity]").val("");
                            $("input[name=poEntryUnitPrice]").val("");
                            $("input[name=item]").val("");
                        }
                    })
                    $(this).dialog("close");
                    $(".purchaseEntries").load("/app/inventory/purchase/purchaseEntry.php?po=" + po);
                },
                "Cancel": function(){
                    $(this).dialog("close");
                }
            }
        });
        
        $(".datepicker").bind("mouseenter click", function(){
            $('.datepicker').datepicker({
                dateFormat: "yy-mm-dd"
            });
        })
        
        $("#clear").click(function(e){
            e.preventDefault();
            var purchaseNo = $("#purchaseNo").val();
            $.ajax({
                type: "POST",
                url: "/app/inventory/purchase/removePurchaseEntries.php",
                data: {purchaseNo: purchaseNo},
                success: function(){
                    $(".purchaseEntries").load("/app/inventory/purchase/purchaseEntry.php?po=" + purchaseNo);
                }
            })
            $("#supplier").removeAttr("disabled");
            $("#supplierCode").val("");
            $("#attention").val("");
        })
        
        $("#createPurchaseOrderForm").unbind("submit").submit(function(e){
            e.preventDefault();
            $("input[name=filePO]").attr("disabled", "disabled");
            var purchaseNo = $("input[name=purchaseNo]").val();
            var supplier = $("#supplier").val();
            var attention = $("input[name=attention]").val();
            $.ajax({
                type: "POST",
                url: "/app/inventory/purchase/file.php",
                data: { purchaseNo: purchaseNo, supplier: supplier, attention: attention },
                success: function(){
                    $.uinotify({
                        'text'		: 'Purchase Order Created',
                        'duration'	: 3000
                    });
                    setTimeout(function() {
                        $("#listPurchaseOrders").trigger("click");
                    }, 3000);
                }
            })
        })
        
        $("#itemAutoComplete").bind("mousedown click", function(){
            $(this).val("");
        })
        
    });    
</script>

<div class="po-form container_24">
    <form method="post" action="" id="createPurchaseOrderForm">
        <div class="purchaseOrderDetail">
            <div class="grid_8 alpha">
                <div grid_8 alpha>
                    <label>Purchase no:</label>
                    <input type="text" id="purchaseNo" name="purchaseNo" value="<?php echo $purchaseNo; ?>" readonly="readonly"/>
                </div>
                <div class="clear"></div>
                <div grid_8 alpha>
                    <label for="supplier">Supplier:</label>
                    <select id="supplier" name="supplier">
                        <option value=""></option>
                        <?php
                            $supplierData = getSuppliers();
                            while($sup = mysql_fetch_assoc($supplierData)){
                        ?>
                        <option value="<?php echo $sup["code"]; ?>"><?php echo $sup["supplier_name"]; ?></option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="clear"></div>
                <div grid_8 alpha>
                    <label>Date Issued:</label>
                    <input type="text" name="dateIssued" value="<?php echo date("Y-m-d"); ?>" readonly="readonly"/>
                </div>
            </div>
            <div class="grid_8">
                <label for="attention">Attention to:</label>
                <input type="text" id="attention" name="attention"/>
                <label for="deliveryDate">Delivery:</label>
                <input type="text" class="datepicker" id="deliveryDate" name="deliveryDate"/>
            </div>
            <div class="grid_8 omega">
                <input type="submit" name="filePO" value="Submit"/>
                <button id="clear">Clear</button>
            </div>
            <input type="hidden" id="itemHolder"/>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <label for="item">Item:</label>
            <input type="text" name="item" id="itemAutoComplete" placeholder="Search Item" style="width:220px;"/>
            <select id="item" multiple="multiple">
                <option value=""></option>
            </select>
        </div>
        <div class="entries grid_10 omega">
            <div class="clear"></div>
            <div class="purchaseEntryHeader grid_12 alpha">
                <ul>
                    <li><span id="poQuantity">Qty</span></li>
                    <li><span id="poDescription">Description</span></li>
                    <li><span id="poUnitPrice">Price</span></li>
                    <li><span id="poAmount">Amount</span></li>
                    <li><span id="poAction">&nbsp;</span></li>
                </ul>
            </div>
            <div class="clear"></div>
            <div class="purchaseEntries grid_12 alpha">
                
            </div>
        </div>
    </form>
</div>

<div id="dialogs" class="ui-dialog-form">
    <div id="selectItem">
        <span id="selectedItem"></span><br/><br/>
        <label>Quantity:</label>
        <input type="number" min="" max="9999" step="1" id="poEntryQuantity" style="border:1px solid #ccc;width:100px;margin:0 0 0 10px;" name="poEntryQuantity" value=""/>
    <br/><br/>
        <label>Unit Price:</label>
        <input type="text" id="poEntryUnitPrice" autofocus="autofocus"  name="poEntryUnitPrice" style="border:1px solid #ccc;width:100px;margin:0 0 0 0px;"/>
    </div>
</div>