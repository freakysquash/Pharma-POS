<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_GET["sku"])){
        $item = getTotalSalesPerItem($_GET["sku"]);
    }
?>

<style>
    .report-generated {
        font-family: "Times New Roman", sans-serif;
        width:930px;
        margin:0 auto;
        color:#141414;
    }
    
    .report-header {
        margin:0 0 10px 0;
        border-bottom:1px solid #ccc;
    }
    
    .report-header span {
        display:block;
        margin:0 0 8px 0;
    }
    
    .report-title {
        font-size:18px;
        font-weight:bold;
    }
    
    .report-desc {
       
    }
    
    .report-content table {
        border-collapse: collapse;
        width:99.5%;
    }
    
    .report-content table thead tr th {
        border:1px solid #141414;
        padding:6px;
    }
    
    .report-content table tbody tr td {
        padding:4px;
        text-align: center;
        border:1px solid #141414;
    }
    
    .report-content table tbody tr td.right {
        text-align: right;
        padding:0 5px 0 0;
    }
    
    .report-content table tbody tr td.total{
        font-weight: bold;
    }
</style>

<div class="report-generated">
    <div class="report-header">
        <span class="report-title">Item Sales</span>
        <span class="report-desc">Item: <?php echo getItemDescriptionBySku($_GET["sku"]); ?></span>
        <span class="report-date">Date: <?php echo date("F d, Y") ?></span>
    </div>
    <div class="report-content">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Transaction #</th>
                    <th>Quantity</th>
                    <th>Tax</th>
                    <th>Discount</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sales = getSalesPerItem($_GET["sku"]); 
                    while($s = mysql_fetch_assoc($sales)){
               ?>
                <tr>
                    <td><?php echo $s["system_date"]; ?></td>
                    <td><?php echo $s["system_time"]; ?></td>
                    <td><?php echo getCustomerNameByCode(getCustomerFromTransaction($s["transaction_no"])); ?></td>
                    <td><?php echo $s["transaction_no"]; ?></td>
                    <td><?php echo $s["quantity"]; ?></td>
                    <td class="right"><?php echo $s["tax_amount"]; ?></td>
                    <td class="right"><?php echo $s["discount_amount"]; ?></td>
                    <td class="right"><?php echo $s["total_amount"]; ?></td>
                </tr>
                <?php
                    }
                ?>
                <tr>
                    <td colspan="4" class="right">Total</td>
                    <td class="total"><?php echo $item["total_quantity"]; ?></td>
                    <td class="right total"><?php echo $item["total_tax"]; ?></td>
                    <td class="right total"><?php echo $item["total_discount"]; ?></td>
                    <td class="right total"><?php echo $item["total_sales_amount"]; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
