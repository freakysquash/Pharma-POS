<?php
    include("../../library/config.php");
    authenticate();
    $purchaseNo = getAvailablePONo();
?>

<script>
    $("#returnEntriesDialog").dialog({
        title: "Return Entry",
        autoOpen: false,
        draggable:false,
        resizable:false,
        modal:true,
        closeOnEscape:true,
        height:254,
        width:300
    })
    
    $(".returnEntry").click(function(){
        var trans = $(this).attr("data-trans");
        var id = $(this).attr("data-id");
        var product = $(this).attr("data-product");
        var quantity = $(this).attr("data-quantity");
        $("input[name=returnTrans]").val(trans);
        $("input[name=returnId]").val(id);
        $("#returnProductName").text(product);
        $("#returnQuantity").text(quantity);
        $("#returnEntriesDialog").dialog("open");
    })
    
    $("#returnItemForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var t = parseInt($("#transactionNo").text());
        var id = $("input[name=returnId]").val();
        var quantity = $("input[name=quantityForReturn]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/return.php",
            data: { t:t, id:id, quantity:quantity },
            success: function(){
                $("#returnEntriesDialog").dialog("close");
                $("a.returnEntry[data-id=" + id + "]").text("");
            }
        })
    })
    
    
</script>

<table id="returnTransactionItems">
    <thead>
        <tr>
            <td>Description</td>
            <td>Quantity</td>
            <td>&nbsp;</td>
        </tr>
    </thead>
    <tbody>
<?php
    $transData = viewUnreturnedTransactionItems($_GET["t"]);    
    while($t = mysql_fetch_assoc($transData)){
?>
        <tr>
            <td><?php echo $t["description"]; ?></td>
            <td class="qty"><?php echo $t["quantity"]; ?></td>
            <td><button class="returnEntry" data-trans="<?php echo $t["transaction_no"]; ?>" data-id="<?php echo $t["id"]; ?>" data-quantity="<?php echo $t["quantity"]; ?>" data-product="<?php echo $t["description"]; ?>">Return</button></td>
            <td><input type="hidden" id="purchaseNo" name="purchaseNo" value="<?php echo $purchaseNo; ?>"/></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>

<div id="dialogs">
    <div id="returnEntriesDialog" class="ui-dialog-form">
        <span id="returnProductName"></span><br/><br/>
        <span>Quantity Purchased: </span><span id="returnQuantity"></span><br/><hr/>
        <form id="returnItemForm">
            <input type="hidden" name="returnId"/>
            <div>
                <label>Quantity to return:</label>
                <input type="text" name="quantityForReturn"/>
            </div>
            <div>
                <input type="submit" value="Return"/>
            </div>
        </form>
    </div>
</div>