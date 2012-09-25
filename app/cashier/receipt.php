<?php
    include("../../library/config.php");
    authenticate();
    list($usersUsername, $usersFirstname, $usersLastname, $usersEmailAddress, $usersContactNo, $usersAddress1, $usersAddress2, $usersCity, $usersProvince, $usersCountry, $usersPostalCode) = getUserDataById($_SESSION["userId"]);
    if(isset($_GET["t"])){
        $entryData = getHeaderByTransNoReceipt($_GET["t"]);
        $paymentData = getTransactionPaymentDataByTransNo($_GET["t"]);
        $pay = mysql_fetch_assoc($paymentData);
    }
    $companyData = getCompanyDetails();
    $c = mysql_fetch_assoc($companyData);
    ?>

<script>
    $(function(){
        var t = $("input[name=transaction]").val();
        $("#transactionBarcode").barcode(t, "code128", {barWidth:2, barHeight:15, showHRI: false});
    })
</script>
<style>
    .receipt {
        margin: -20px 0 0 -20px;
        font-family: Helvetica, sans-serif;
    }
    
    .receipt table thead {
        font-size:10px;
    } 
    
    .receipt table tbody {
        font-size:10px;
    }
    
    .receipt table tfoot {
        font-size:10px;
        text-align:right;
    }
    
    .storeDetail {
        text-align:center;
    }
    
    .storeName {
        font-size:16px;
        font-weight: bold;
    }
    
    #amountHeader {
        text-align:right
    }
    
    #qtyHeader {
        text-align:left;
    }
    
    .amount {
        text-align:right;
    }
    
    .note {
        text-align:center;
        font-size:10px;
    }
    
    .receipt tfoot span {
        width:150px;
        margin:0 10px 0 0;
        float:left;
    }
    
    #reprint {
        font-weight: bold;
        font-size: 12px;
        display:block;
        width:100%;
        text-align: center;
        border-bottom: 1px solid #999;
        padding:0 0 10px 0;
    }
    
</style>

<input type="hidden" name="transaction" value="<?php echo $_GET["t"] ?>"/>

<div class="receipt">
    <table>
        <thead>
            <tr>
                <td colspan="3" class="storeDetail storeName"><?php echo $c["company_name"]; ?></td>
            </tr>
            <tr>
                <td colspan="3" class="storeDetail"><?php echo $c["address_1"]; ?></td>
            </tr>
            <tr>
                <td colspan="3" class="storeDetail"><?php echo $c["address_2"]; ?></td>
            </tr>
            <tr>
                <td colspan="3" class="storeDetail">Accreditation No: <?php echo $c["accreditation"]; ?></td>
            </tr>
            <tr>
                <td colspan="3" class="storeDetail">TIN: <?php echo $c["tin"]; ?></td>
            </tr>
            <tr>
                <td colspan="3" class="storeDetail">S/N: <?php echo date("y-md") . mt_rand(10, 99); ?></td>
            </tr>
            <tr>
                <td colspan="3"><hr/>Transaction #: <?php echo $_GET["t"]; ?></td>
            </tr>
            <tr>
                <td colspan="3">Date: <?php echo $pay["system_date"] . " " . $pay["system_time"]; ?></td>
            </tr>
            <tr>
                <td colspan="3">Cashier:  <?php echo substr($usersFirstname, 0, 1) . ". " . $usersLastname; ?><hr/></td>
            </tr>
            <?php
                if(isset($_GET["d"])){
            ?>
            <tr>
                <td colspan="3"><span id="reprint">Reprint Only</span></td>
            </tr>
            <?php
                }
            ?>
            <tr id="header">
                <td id="qtyHeader">Qty</td>
                <td>Description</td>
                <td id="amountHeader">Amount</td>
            </tr>
        </thead>
        <tbody>
            <?php
                while($entry = mysql_fetch_assoc($entryData)){
            ?>
            <tr>
                <td><?php echo $entry["quantity"]; ?></td>
                <td><?php echo getShortItemDescriptionBySku($entry["sku"]); ?></td>
                <td class="amount"><?php echo $entry["total_amount"]?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <span>Sub Total:</span>
                    <?php echo sprintf("%.2f", getTransactionSubtotal($_GET["t"])); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>Vatable Sales:</span>
                    <?php echo sprintf("%.2f", getTransactionSubtotal($_GET["t"]) - getTransactionTotalTax($_GET["t"])); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>VAT 12%:</span>
                    <?php echo getTransactionTotalTax($_GET["t"]); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>Discount:</span>
                    <?php echo getTransactionTotalDiscount($_GET["t"]); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>Items:</span>
                    <?php echo getTransactionCompletedEntries($_GET["t"]); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                     <hr/>
                    <span>Total Amount:</span>
                    <b><?php echo sprintf("%.2f", getTransactionTotalAmount($_GET["t"])); ?></b>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr/> 
                    <span>Cash:</span>
                    <?php echo getTransactionPayment($_GET["t"]); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span>Change:</span>
                    <?php echo sprintf("%.2f", getTransactionPayment($_GET["t"]) - getTransactionTotalAmount($_GET["t"])); ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="note"><br/><br/><br/>** THIS SERVES AS YOUR OFFICIAL RECEIPT **</td>
            </tr>
            <tr>
                <td colspan="3"><div id="transactionBarcode"></div></td>
            </tr>
        </tfoot>
    </table>
</div>


