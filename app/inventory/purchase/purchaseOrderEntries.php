<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    $("input[name=toggleComplete]").each(function(){
         $(this).bind("mousedown change", function(){
             if(!$(this).is(":checked")){
                $(this).siblings("input[name=poEntryReceive]").val($(this).attr("data-quantity"));
            }
         })
     })
     
     $(".receiveButton").click(function(e){
         e.preventDefault();
         var purchaseNo = $(this).attr("data-po");
         var entryId = $(this).attr("data-id");
         var sku = $(this).siblings("input[name=poEntrySku]").val();
         var supplier = $(this).siblings("input[name=poEntrySupplier]").val();
         var quantity = $(this).siblings("input[name=poEntryQuantity]").val();
         var receivedQuantity = $(this).siblings("input[name=poEntryReceive]").val();
         var unitPrice = $(this).siblings("input[name=poEntryUnitPrice]").val();
         var deliveryNo = $("input[name=poEntryDeliveryNo]").val();
         var salesInvoiceNo = $("input[name=poEntrySalesInvoiceNo]").val();
         var expiration = $("input[name=poEntryExpiration]").val();
         var expense = $("input[name=poEntryExpense]").val();
         var type = $("input[name=poEntryStatus]").val();;
         $.ajax({
             type: "POST",
             url: "/app/inventory/purchase/receive.php",
             data: {purchaseNo: purchaseNo, entryId: entryId, sku: sku, supplier: supplier, quantity: quantity, receivedQuantity: receivedQuantity, unitPrice: unitPrice, deliveryNo: deliveryNo, salesInvoiceNo: salesInvoiceNo, expiration: expiration, expense: expense, type: type},
             success: function(){
                 $(".purchase-content").load("/app/inventory/purchase/list.php");
             }
         })
         $(this).attr("disabled", true);
         $(this).text("Received");
     })
    
    $(".datepicker").each(function(){
        $(this).datepicker({dateFormat: "yy-mm-dd"});
        $(this).css({ "width":"100px" })
    })
     
</script>

<style>
    .poEntryRow {
        margin: 0 0 5px 0;
    }
    
    #poEntryHeader span {
        display:inline-block;
        margin: 0 0 10px 0;
        text-align:center;
    }
    
    .poEntryRow input[type="text"] {
        display:inline-block;
        text-align:center;
    }

    #poEntryCompleteHeader {
        width:70px;
    }
    
    .complete {
        display:inline-block;
        margin:0 30px 0 25px;
        background:#ccc;
    }
    
    #poEntryQuantity {
        width:68px;
        border:none;
    }
    
    #poEntryQuantityHeader {
        width:70px;
    }
    
    #poEntryDescription {
        width:250px;
        border:none;
    }
    
    #poEntryDescriptionHeader {
        width:250px;
    }
    
    #poEntryExpiration {
        width:100px;
        border:1px solid #ccc;
    }
    
    #poEntryExpirationHeader {
        width:100px;
    }
    
    #poEntryReceive {
        width:70px;
        border:1px solid #ccc;
    }
    
    #poEntryReceiveHeader {
        width:70px;
    }
    
    #poEntryReceiveSubmit {
        width:100px;
    }
    
    .receiveButton {
        margin:0 10px;
    }
    
    #poEntryHeader div label {
        display:inline-block;
        width:170px;
    }
    
    #poEntryHeader div input[type="text"] {
        border:1px solid #ccc;
    }
    
</style>

<div id="poEntryHeader">
    <div>
        <label for="poEntryDeliveryNo">Delivery no:</label>
        <input type="text" id="poEntryDeliveryNo" name="poEntryDeliveryNo"/>
    </div>
    <div>
        <label for="poEntrySalesInvoiceNo">Sales Invoice no:</label>
        <input type="text" id="poEntrySalesInvoiceNo" name="poEntrySalesInvoiceNo"/>
    </div>
    <div>
        <label for="poEntryExpense">Shipping Expense:</label>
        <input type="text" id="poEntryExpense" name="poEntryExpense"/>
    </div>
     <span id="poEntryCompleteHeader">&nbsp;</span><span id="poEntryQuantityHeader">Qty</span><span id="poEntryDescriptionHeader">Item</span><span id="poEntryReceiveHeader">#</span><span id="poEntryExpirationHeader">Expiration</span><span id="poEntryReceiveSubmit">&nbsp;</span>
</div>
<?php
    if(isset($_GET["po"])){
        $poData = getPurchaseOrderEntries($_GET["po"]);
        while($po = mysql_fetch_assoc($poData)){
            if($po["delivery_status"] != $po["quantity"]){
?>
<div class="poEntryRow">
    <input type="checkbox" name="toggleComplete" class="complete" data-quantity="<?php echo $po["quantity"] - getPOEntryDeliveryStatus($_GET["po"], $po["id"]); ?>"/>
    <input type="hidden" name="poEntrySku" value="<?php echo $po["sku"]; ?>"/>
    <input type="hidden" name="poEntrySupplier" value="<?php echo $po["supplier_code"]; ?>"/>
    <input type="hidden" name="poEntryUnitPrice" value="<?php echo $po["unit_price"]; ?>"/>
    <?php
        if($po["waiting"] != $po["quantity"]){
    ?>
        <input type="hidden" name="poEntryStatus" value="Incomplete"/>
    <?php
        }
        else{
     ?>
         <input type="hidden" name="poEntryStatus" value="Pending"/>
    <?php
        }
    ?>
    <input type="text" name="poEntryQuantity" id="poEntryQuantity" readonly="readonly" value="<?php echo $po["quantity"] - getPOEntryDeliveryStatus($_GET["po"], $po["id"]); ?>"/>
    <input type="text" name="poEntryDescription" id="poEntryDescription" readonly="readonly" value="<?php echo getDescriptionBySku($po["sku"]); ?>"/>
    <input type="text" name="poEntryReceive" id="poEntryReceive" value=""/>
    <input type="text" name="poEntryExpiration" class="datepicker" id="poEntryExpiration<?php echo mt_rand(); ?>" value=""/>
    <button data-po="<?php echo $po["purchase_no"]; ?>" data-id="<?php echo $po["id"]; ?>" class="x-button receiveButton">Receive</button>
</div>

<?php
            }
        }
    }
?>
