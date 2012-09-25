<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_GET["d"])){
        $d = viewCompletedTotalAmountsByDate($_GET["d"]);
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
        <span class="report-title">Daily Sales</span>
        <span class="report-date">Date: <?php echo date("F d, Y", strtotime($_GET["d"])) ?></span>
    </div>
    <div class="report-content">
        <table>
            <thead>
                <tr>
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
                    $sales = viewCompletedTransactionItemsByDate($_GET["d"]); 
                    while($s = mysql_fetch_assoc($sales)){
               ?>
                <tr>
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
                    <td colspan="3" class="right"></td>
                    <td class="total"><?php echo $d["total_quantity"]; ?></td>
                    <td class="right total"><?php echo $d["total_tax"]; ?></td>
                    <td class="right total"><?php echo $d["total_discount"]; ?></td>
                    <td class="right total"><?php echo $d["total_sales_amount"]; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
