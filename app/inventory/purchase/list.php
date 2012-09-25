<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
?>

<script>
    $(document).ready(function(){
        $("#purchase-table").dataTable({
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "aaSorting": [[ 0, "desc" ]]
        });
        
        $("#receivePODialog").dialog({
            autoOpen:false,
            height:600,
            width:800,
            modal:true,
            draggable:false,
            resizable:false,
            closeOnEscape:true,
            buttons: {
                "Close":function(){
                    $(this).dialog("close");
                }
            }
        })

        $(".receivePO").click(function(e){
            e.preventDefault();
            var po = $(this).attr("data-po");
            $("#ui-dialog-title-receivePODialog").text("Receive Purchase Order # " + po);
            $("#receivePODialog").load("/app/inventory/purchase/purchaseOrderEntries.php?po=" + po);
            $("#receivePODialog").dialog("open");
        })
        
    })
</script>

<div class="x-table">
<table id="purchase-table">
    <thead>
        <tr>
            <td>Purchase no</td>
            <td>Supplier</td>
            <td>Amount</td>
            <td>Status</td>
            <td>Purchase Date</td>
            <td>&nbsp;</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $purchaseData = getPurchaseOrders();
            while($p = mysql_fetch_assoc($purchaseData)){
        ?>
        <tr>
            <td><?php echo $p["purchase_no"]; ?></td>
            <td><?php echo getSupplierNameByCode($p["supplier_code"]); ?></td>
            <td><?php echo $p["total_amount"]; ?></td>
            <td><?php echo $p["status"]; ?></td>
            <td><?php echo $p["system_date"] . " " . $p["system_time"]; ?></td>
            <td>
                <?php
                    if($p["status"] == "Completed"){
                        
                    }
                    else{
                ?>
                        <button class="x-button receivePO" id="" data-po="<?php echo $p["purchase_no"]; ?>">Receive</button>
                <?php
                    }
                ?>
            </td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</div>

<div id="dialogs ui-dialog-form">
    <div id="receivePODialog">
        
    </div>
</div>

