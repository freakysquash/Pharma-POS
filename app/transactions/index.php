<script>
    $(document).ready(function(){
        $("#completedTrans").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        $("#onHoldTrans").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        $("#cancelledTrans").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        $("#returnedTrans").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        $(".viewTransaction").live("click", function(){
            var t = $(this).text();
            $("#receiptDialog").load("/app/cashier/receipt.php?t=" + t + "&d=1");
            $("#viewTransactionDialog").load("/app/transactions/view.php?t=" + t);
            $("#viewTransactionDialog").dialog("open");
        })
        
        $("#viewTransactionDialog").dialog({
                title: 'Transaction Details',
                autoOpen: false,
                height: 600,
                width: 960,
                modal: true,
                closeOnEscape: true,
                resizable: false,
                buttons: {
                    "Print Receipt Duplicate": function(){
                        $(this).dialog("close");
                        $("#receiptDialog").printElement();
                    }
                },
                draggable:false
        });

        $("#receiptDialog").dialog({
                title: 'Sales Receipt',
                autoOpen: false,
                minHeight: 120,
                width: 350,
                modal: true,
                closeOnEscape: true,
                resizable: false,
                buttons: false,
                draggable:false
        });
        
        $("#transTables").tabs();
        
        function changeUrl(transactions){
            var object = {transactions: transactions};
            history.pushState(object, "", "?module=transactions&type=" + transactions)
        }
        
        $("#transTables a").each(function(){
            $(this).click(function(){
                var transactions = $(this).attr("data-transactions");
                changeUrl(transactions);
            })
        })
        
        var transactions = $(document).getUrlParam("type");
        switch(transactions){
            case "completed":
                $("#transTables").tabs({ selected:0 });
                break;
            case "onhold":
                $("#transTables").tabs({ selected:1 });
                break;
           case "cancelled":
                $("#transTables").tabs({ selected:2 });
                break;
           case "returned":
                $("#transTables").tabs({ selected:3 });
                break;
        }
        
    })
</script>

<div class="window">
<div class="window-title">
    <span>Transactions</span>
</div>
<div id="transTables">
    <ul>
        <li><a href="#completed" data-transactions="completed">Completed</a></li>
        <li><a href="#onhold" data-transactions="onhold">On Hold</a></li>
        <li><a href="#cancelled" data-transactions="cancelled">Cancelled</a></li>
        <li><a href="#returned" data-transactions="returned">Returned</a></li>
    </ul>
    <div id="completed" class="x-table">
        <table id="completedTrans">
            <thead>
                <tr>
                    <td>Transaction No</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Time</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $transNoData = getTransactions();
                    while($transNo = mysql_fetch_assoc($transNoData)){
                        $transNo = $transNo["transaction_no"];
                        $transData = getCompletedTransactionData($transNo);
                        while($trans = mysql_fetch_assoc($transData)){
                ?>
                        <tr>
                            <td><a href="#" class="viewTransaction"><?php echo $transNo; ?></a></td>
                            <td><?php echo $trans["total_amount"]; ?></td>
                            <td><?php echo $trans["system_date"]; ?></td>
                            <td><?php echo $trans["system_time"]; ?></td>
                        </tr>
                <?php
                                }
                            }
                ?>
            </tbody>
        </table>
    </div>
    <div id="onhold" class="x-table">
        <table id="onHoldTrans">
            <thead>
                <tr>
                    <td>Transaction No</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Time</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $transNoData = getTransactions();
                    while($transNo = mysql_fetch_assoc($transNoData)){
                        $transNo = $transNo["transaction_no"];
                        $transData = getOnHoldTransactionData($transNo);
                        while($trans = mysql_fetch_assoc($transData)){
                ?>
                        <tr>
                            <td><a href="#" class="viewTransaction"><?php echo $transNo; ?></a></td>
                            <td><?php echo $trans["total_amount"]; ?></td>
                            <td><?php echo $trans["system_date"]; ?></td>
                            <td><?php echo $trans["system_time"]; ?></td>
                            <td><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/?module=cashier&t=<?php echo $transNo; ?>" class="button">Recall</a></td>
                        </tr>
                <?php
                                }
                            }
                ?>
            </tbody>
        </table>
    </div>
    <div id="cancelled" class="x-table">
        <table id="cancelledTrans">
            <thead>
                <tr>
                    <td>Transaction No</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Time</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $transData = getCancelledTransactions($transNo);
                    while($trans = mysql_fetch_assoc($transData)){
                ?>
                        <tr>
                            <td><a href="#" class="viewTransaction"><?php echo $trans["transaction_no"]; ?></a></td>
                            <td><?php echo $trans["total_amount"]; ?></td>
                            <td><?php echo $trans["system_date"]; ?></td>
                            <td><?php echo $trans["system_time"]; ?></td>
                        </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div id="returned" class="x-table">
        <table id="returnedTrans">
            <thead>
                <tr>
                    <td>Transaction No</td>
                    <td>Amount</td>
                    <td>Date</td>
                    <td>Time</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $transNoData = getTransactions();
                    while($transNo = mysql_fetch_assoc($transNoData)){
                        $transNo = $transNo["transaction_no"];
                        $transData = getReturnedTransactionData($transNo);
                        while($trans = mysql_fetch_assoc($transData)){
                ?>
                        <tr>
                            <td><a href="#" class="viewTransaction"><?php echo $transNo; ?></a></td>
                            <td><?php echo $trans["total_amount"]; ?></td>
                            <td><?php echo $trans["system_date"]; ?></td>
                            <td><?php echo $trans["system_time"]; ?></td>
                        </tr>
                <?php
                                }
                            }
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<div id="dialogs">
    <div id="viewTransactionDialog">
        
    </div>
    <div id="receiptDialog" class="ui-dialog-form">
         
    </div>
</div>