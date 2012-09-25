<?php
    include("../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
?>

<script>
    $("#inventory-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>

<table id="inventory-table">
    <thead>
        <tr>
            <td>SKU</td>
            <td>Description</td>
            <td>On Hand</td>
            <td>Minimum</td>
            <td>Status</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $inventoryData = getInventoryCount();
            while($i = mysql_fetch_assoc($inventoryData)){
        ?>
        <tr>
            <td><?php echo $i["sku"]; ?></td>
            <td><?php echo $i["description_1"]; ?></td>
            <td><?php echo $i["stock_count"]; ?></td>
            <td><?php echo $i["reorder_min_count"]; ?></td>
            <td><?php if($i["stock_count"] <= $i["reorder_min_count"]){ echo "Critical"; } else{ echo "On Stock"; } ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>


