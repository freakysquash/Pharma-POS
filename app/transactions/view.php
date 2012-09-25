<?php
    include("../../library/config.php");
?>

<script>
    $("#transactionEntries").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>
    
Transaction no: <span id="viewTransNo"><?php echo $_GET["t"]; ?></span><br/>
Items<br/><br/>

<table id="transactionEntries">
    <thead>
        <tr>
            <th>Description</th>
            <th>Qty</th>
            <th>Tax</th>
            <th>Discount</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $entries = viewTransactionItems($_GET["t"]);
        while($e = mysql_fetch_assoc($entries)){
    ?>
        <tr>
            <td><?php echo $e["description"]; ?></td>
            <td><?php echo $e["quantity"]; ?></td>
            <td><?php echo $e["tax_amount"]; ?></td>
            <td><?php echo $e["discount_amount"]; ?></td>
            <td><?php echo $e["total_amount"]; ?></td>
        </tr>
    <?php
        }
    ?>
        
    </tbody>
</table>
        
        