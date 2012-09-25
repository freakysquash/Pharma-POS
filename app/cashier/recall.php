<?php
    include("../../library/config.php");
    include("../../template/header.php");
    authenticate();
    
    if(isset($_GET["t"])){
        $transNo = $_GET["t"];
        $_SESSION["transNo"] = $transNo;
    }
    else{
        header("Location: /");
    }
    if(isset($_SESSION["f5ebd1bab16ff5845411f18788c2ca1e"])){
        $lockStatus = $_SESSION["f5ebd1bab16ff5845411f18788c2ca1e"];
    }
    else{
        $lockStatus = "e0311f36cb8fa0942c7011fe0879d8f4";
        $_SESSION["f5ebd1bab16ff5845411f18788c2ca1e"] = $lockStatus;
    }
?>

<script>
    $(document).ready(function () {
    
    function changeUrl(module){
        var object = {module: module};
        history.pushState(object, "", "?module=" + module)
    }
    
    changeUrl("cashier");
    
    var transaction = parseInt($("#transactionNo").text());
    $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + transaction, function(){}).hide().slideDown(1000).delay(100);
    $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
        $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
        $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
        $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
    });
    $.getJSON("/app/cashier/due.php?t=" + transaction, function (data) {
        $("input[name=subTotal]").val(data.subtotal)
    });
    $.getJSON("/app/cashier/taxAmount.php?t=" + transaction, function (data) {
        $("#summaryTax").text(data.totalTax)
    });
    $.getJSON("/app/cashier/discount.php?t=" + transaction, function (data) {
        var totalDiscount = data.totalDiscount;
        $("input[name=totalDiscount]").val(totalDiscount);
        $("#summaryDiscount").text(totalDiscount)
    })
    $.getJSON("/app/cashier/lastItemEntry.php?t=" + transaction, function (data) {
        $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/app/manage/items/image.php?s=" + data.sku);
    })
    $.getJSON("/app/cashier/getCustomer.php?t=" + transaction, function (data) {
        $("#activeCustomer").attr("data-code", data.code);
        $("#activeCustomer").text(data.customer);
    })
    
    $(".itemExistenceCheck").hide();
    $("#itemEntryForm").unbind("submit").submit(function (e) {
        e.preventDefault();
        var transaction = parseInt($("#transactionNo").text());
        var sku = $("input[name=sku]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/entry.php",
            data: {transaction: transaction,sku: sku},
            success: function (data) {
                $("input[name=sku]").val("");
                $.getJSON("/app/cashier/itemDataCheck.php?s=" + sku, function (data) {
                    if(data.error == "1"){
                         $.uinotify({
                            "text": "Unknown item. Please verify the code.",
                            "duration": 3000
                        });
                    }
                });
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + transaction, function(){}).hide().slideDown(1000).delay(100);
                $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + transaction, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + transaction, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $.getJSON("/app/cashier/discount.php?t=" + transaction, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
                $.getJSON("/app/cashier/lastItemEntry.php?t=" + transaction, function (data) {
                    $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/app/manage/items/image.php?s=" + data.sku);
                })
                $.ajax({
                    type: "POST",
                    url: "/app/inventory/inventoryCountCheck.php",
                    data: {sku: sku},
                    dataType: "json",
                    success: function(data){
                        if(data[2] <= "0"){
                            $.uinotify({
                                "text": "Item selected is out of stock.",
                                "duration": 3000
                            });
                        }
                    }
                })
                
                $("#itemImage").attr("src", "http://<?php echo ROOT; ?>/app/manage/items/image.php?s=" + sku);
            }
        })
    });
    
    $(".back").click(function(){
        parent.history.back();
        return false;
    });
    
    $("input[name=enterItem]").removeClass("ui-button ui-corner-all ui-widget ui-state-default");
    
    $("#quantity").bind("change keyup input paste", function () {
        var price = $("input[name=price]").val();
        var quantity = $("input[name=quantity]").val();
        var tax = $("input[name=toggleEntryTax]").attr("data-tax");
        var amount = parseFloat(price * quantity + tax).toFixed(2);
        $("input[name=amount]").val(amount)
    });
    $("#cash").bind("keyup", function () {
        var cash = $("input[name=cash]").val();
        var tender = parseFloat(cash).toFixed(2);
        $("input[name=totalTendered]").val(tender);
        var totalAmount = parseFloat($("#tenderTotalAmount").val()).toFixed(2);
        var balance = (tender - totalAmount).toFixed(2);
        $("input[name=balance]").val(balance)
        if(balance < 0){
            $("input[name=tenderSale]").val("Insufficient Tender Amount");
            $("input[name=tenderSale]").attr("disabled", "disabled");
        }
        if(balance >= 0){
            $("input[name=tenderSale]").removeAttr("disabled");
            $("input[name=tenderSale]").val("Tender");
        }
    });
    $("#tenderSale").click(function () {
        $("#tenderSaleDialog").dialog("open");
        $("input[name=tenderSale]").attr("disabled", "disabled");
        $("input[name=tenderSale]").val("Tendering Disabled");
        var t = parseInt($("#transactionNo").text());
        $("input[name=balance]").val("-" + $("input[name=hiddenTotalAmount]").val());
        $.getJSON("/app/cashier/discount.php?t=" + t, function (data) {
            var totalDiscount = data.totalDiscount;
            $("input[name=totalDiscount]").val(totalDiscount);
        })
    });
    $("#tenderSaleDialog").dialog({
        title: "Tender Sale",
        autoOpen: false,
        minHeight: 120,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable: false
    });
    
    $("#printReceiptDialog").dialog({
        autoOpen: false,
        title: "Print Receipt",
        modal:true,
        width:400,
        draggable:false,
        buttons: {
            "Print Transaction Receipt": function () {
                $("#receiptDialog").printElement();
                $("#printReceiptDialog").dialog("close");
                setTimeout(function () {
                    window.location.href = "/"
                }, 1E3)
            },
            "Cancel": function(){
                $(this).dialog("close");
                setTimeout(function () {
                    window.location.href = "/"
                }, 1E3)
            }
        }
    })
    
    $("#tenderSaleForm").unbind("submit").submit(function (e) {
        e.preventDefault();
        var transaction = parseInt($("#transactionNo").text());
        var subTotal = $("input[name=subTotal]").val();
        var discountAmount = parseFloat($("input[name=totalDiscount]").val()).toFixed(2);
        var totalAmount = $("input[name=hiddenTotalAmount]").val();
        var totalTendered = $("input[name=totalTendered]").val();
        var balance = $("input[name=balance]").val();
        var type = "Normal";
        $.ajax({
            type: "POST",
            url: "/app/cashier/tender.php",
            data: {
                transaction: transaction,
                subTotal: subTotal,
                discountAmount: discountAmount,
                totalAmount: totalAmount,
                totalTendered: totalTendered,
                balance: balance,
                type: type
            },
            success: function () {
                $("#tenderSaleDialog").dialog("close");
                $("#receiptDialog").load("/app/cashier/receipt.php?t=" + transaction);
                $("#printReceiptDialog").dialog("open")
            }
        })
    });
    $("#cancelTransaction").unbind("click").click(function(e) {
        e.preventDefault();
        $(this).attr("disabled", "disabled");
        var transaction = parseInt($("#transactionNo").text());
        var subTotal = $("input[name=subTotal]").val();
        var discountAmount = parseFloat($("input[name=totalDiscount]").val()).toFixed(2);
        var totalAmount = $("input[name=hiddenTotalAmount]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/cancel.php",
            data: {
                transaction: transaction,
                subTotal: subTotal,
                discountAmount: discountAmount,
                totalAmount: totalAmount
            },
            success: function () {
                $.uinotify({
                    "text": "Transaction # " + transaction + " cancelled",
                    "duration": 3E3
                });
                setTimeout(function () {
                    window.location.href = "/"
                }, 3500)
            }
        })
    });
    $("#closeStore").click(function () {
        $("#closeStoreDialog").dialog("open");
        $("#closeStore").text("Open Store")
    });
    $("#closeStoreDialog").dialog({
        title: "Close Store",
        autoOpen: false,
        minHeight: 120,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable: false
    });
    $("#holdTransaction").unbind("click").click(function(e) {
        e.preventDefault();
        $(this).attr("disabled", "disabled");
        var transaction = parseInt($("#transactionNo").text());
        var subTotal = $("input[name=subTotal]").val();
        var taxAmount = $("#summaryTax").text();
        var discountAmount = parseFloat($("input[name=totalDiscount]").val()).toFixed(2);
        var totalAmount = $("input[name=hiddenTotalAmount]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/hold.php",
            data: {
                transaction: transaction,
                subTotal: subTotal,
                taxAmount: taxAmount,
                discountAmount: discountAmount,
                totalAmount: totalAmount
            },
            success: function () {
                $.uinotify({
                    "text": "Transaction # " + transaction + " on hold",
                    "duration": 3E3
                });
                setTimeout(function () {
                    window.location.href = "/"
                }, 3500)
            }
        })
    });
    
    
    
    $("#findItem").click(function () {
        $(".dataTables_filter input:text").addClass("findItemFilter");
        $("#findItemDialog").dialog("open");
        $("#itemsTable").dataTable({
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bRetrieve":true
        });
    });
    $("#findItemDialog").dialog({
        title: "Find Item",
        autoOpen: false,
        height: 500,
        width: 600,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable: true
    });
    $(".addItemToEntry").unbind("click").click(function(){
        var sku = $(this).attr("data-sku");
        $("input[name=sku]").val(sku);
        $("#itemEntryForm").trigger("submit");
        $("#findItemDialog").dialog("close");
    })
    
    /* SET CUSTOMERS */
    $("#customersTable").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
        
    $("#activeCustomer, #setCustomer").click(function () {
        $("#setCustomerDialog").dialog("open")
    });
    
    $("#setCustomerDialog").dialog({
        title: "Customers",
        autoOpen: false,
        minHeight: 400,
        width:500,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable: false
    });
    
    $(".setThisCustomer").click(function(){
        var transaction = parseInt($("#transactionNo").text());
        var customer = $(this).attr("data-customer");
        $.ajax({
            type: "POST",
            url: "/app/cashier/setCustomer.php",
            data: {transaction: transaction, customer:customer},
            success: function(){
                $.getJSON("/app/cashier/getCustomer.php?t=" + transaction, function (data) {
                    $("#activeCustomer").attr("data-code", data.code);
                    $("#activeCustomer").text(data.customer);
                })
            }
        })
        $("#setCustomerDialog").dialog("close");
    })
    
    /* ------------------------------------------------------ */
    
    /* RETURN TRANSACTION */
    
    $("#returnTransactionDialog").dialog({
        title: "Return Transaction",
        autoOpen: false,
        minHeight: 120,
        width: 600,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        draggable: false,
        buttons: {
            "Return Items": function(){
               
            }
        }
    });
    
    $("#returnTransaction").click(function () {
        $("#returnTransactionDialog").dialog("open");
    });
    
    $("#returnTransactionForm").submit(function(e){
        e.preventDefault();
        var t = $("input[name=returnTransactionNo]").val();
        $("#returnItems").load("/app/cashier/returnItems.php?t=" + t, function(){
            var x = jQuery(this).position().right + jQuery(this).outerWidth();
            var y = jQuery(this).position().top - jQuery(document).scrollTop();
            $("#returnTransactionDialog").dialog({ position: [x,y] });
            $("#returnItems").css({ "height":"250px" });
        });
    })

    /* ----------------------------------------------------- */
    
    $("#receiptDialog").dialog({
        title: "Sales Receipt",
        autoOpen: false,
        minHeight: 120,
        width: 400,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: {
            "Print Receipt": function () {
                $("#receiptDialog").printElement();
                $("#receiptDialog").dialog("close");
                setTimeout(function () {
                    window.location.href = "/"
                }, 1E3)
            },
            "Cancel": function(){
                $(this).dialog("close");
                setTimeout(function () {
                    window.location.href = "/"
                }, 1E3)
            }
        },
        draggable: false
    });
    $("#addDiscount").click(function (e) {
        e.preventDefault();
        $("#addDiscountDialog").dialog("open")
    });
    $("#addDiscountDialog").dialog({
        title: "Discounts",
        autoOpen: false,
        minHeight: 120,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: false,
        draggable: false
    });

    $("#addDiscountForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var transaction = parseInt($("#transactionNo").text());
        var discountCode = $("input[name=discount]").attr("data-code");
        var discountRate = $("input[name=discount]").val();
        $.ajax({
            type: "POST",
            url: "/app/cashier/applyTransactionDiscount.php",
            data: { transaction: transaction, discountCode: discountCode, discountRate: discountRate },
            success: function(){
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + transaction, function(){}).hide().slideDown(1000).delay(100);
                $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=tenderTotalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=totalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=balance]").val(($("input[name=cash]").val() - data.total).toFixed(2));
                });
                $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + transaction, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + transaction, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $.getJSON("/app/cashier/discount.php?t=" + transaction, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
            }
        })
        $("#addDiscountDialog").dialog("close")
    });
    
    $("#removeDiscounts").click(function(e) {
        e.preventDefault();
        $('.discountSelect input[type="checkbox"]').removeAttr("checked");
        var transaction = parseInt($("#transactionNo").text());
        $.ajax({
            type: "POST",
            url: "/app/cashier/removeTransactionDiscount.php",
            data: {transaction:transaction},
            success: function(){
                $("#addDiscountDialog").dialog("close");
                $(".cashier-entered-items").load("/app/cashier/entry.php?t=" + transaction, function(){}).hide().slideDown(1000).delay(100);
                $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=tenderTotalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=totalAmount]").val(parseFloat(data.total).toFixed(2));
                    $("input[name=balance]").val(($("input[name=cash]").val() - data.total).toFixed(2));
                });
                $.getJSON("/app/cashier/total.php?t=" + transaction, function (data) {
                    $("#totalAmount").text("P " + parseFloat(data.total).toFixed(2));
                    $("#tenderTotalAmount").val(parseFloat(data.total).toFixed(2));
                    $("input[name=hiddenTotalAmount]").val(parseFloat(data.total).toFixed(2))
                });
                $.getJSON("/app/cashier/due.php?t=" + transaction, function (data) {
                    $("input[name=subTotal]").val(data.subtotal)
                });
                $.getJSON("/app/cashier/taxAmount.php?t=" + transaction, function (data) {
                    $("#summaryTax").text(data.totalTax)
                });
                $.getJSON("/app/cashier/discount.php?t=" + transaction, function (data) {
                    var totalDiscount = data.totalDiscount;
                    $("input[name=totalDiscount]").val(totalDiscount);
                    $("#summaryDiscount").text(totalDiscount)
                })
            }
        })
    });
    
    $(function () {
        var max = 1;
        var checkboxes = $('.discountSelect input[type="checkbox"]');
        checkboxes.change(function () {
            var current = checkboxes.filter(":checked").length;
            checkboxes.filter(":not(:checked)").prop("disabled", current >= max)
        })
    });
    
    $(".product-buttons-tab").tabs();
    
    
    function playAudio() {
        var audio = $("#hoversound")[0];
        audio.play()
    }
    $("#showNumPress").hide();
    
    if ($("input[name=sku]").blur()) {
        $(".numKey").click(function(e){
            e.preventDefault();
            var keyVal = $(this).attr("id");
            $("input[name=sku]").val($("input[name=sku]").val() + keyVal);
             playAudio();
            $("#numPress").text(keyVal);
            $("input[name=sku]").trigger("change");
        })
        $("#clr").click(function(e){
            e.preventDefault();
            $("input[name=sku]").val("");
            $("input[name=sku]").trigger("change")
        })
    }
    

    $(function () {
        var buttons = $("a.trans-button");
        var audio = $("#hoversound")[0];
        buttons.hover(function () {
            audio.play()
        })
    });

    $("input[name=sku], input[name=cash], input[name=quantity], input[name=findSku], input[name=findDescription], input[name=transactionNo], .findItemFilter").focus(function () {
        $("input[name=focusnotifier]").val("1")
    }).blur(function () {
        $("input[name=focusnotifier]").val("0")
    });
    
    $('#checkAll').bind("mousedown change", function() {
        if(!$(this).prop("checked")) {
            $('.toggleEntryTax').each(function(){
                $(this).prop("checked", true);
                $(this).trigger("change");
            })
        }
        else {
            $('.toggleEntryTax').each(function(){
                $(this).prop("checked", false);
                $(this).trigger("change");
            })
        }
    });

    $('.toggleEntryTax').each(function(){
        $(this).mousedown(function() {
            if($(this).prop("checked", false)) {
                $('#checkAll').prop("checked", false);
            }
            else {
                var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
                var numTotal = $('input:checkbox:not(#checkAll)').length;
                if(numTotal == numChecked) {
                    $('#checkAll').prop("checked", true);
                }
            }
        });
    })
    
    $("#lockScreenDialog").dialog({
        title: "Screen Locked!",
        autoOpen: false,
        draggable:false,
        resizable:false,
        closeOnEscape: false,
        modal: true,
        buttons: false,
        width:350,
        open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
    })
    
    $("#lockScreen").click(function(){
        var ad67b39abe54ce1186efd7dcee02e478 = "dd2fb68f525ccebcdb426da3353fae4b";
        $.ajax({
            type: "POST",
            url: "/app/users/lock.php",
            data: {ad67b39abe54ce1186efd7dcee02e478:ad67b39abe54ce1186efd7dcee02e478},
            success: function(){
                
            }
        })
        $("#lockScreenDialog").dialog("open");
    })
    
    $("#lockScreenForm").submit(function(e){
        e.preventDefault();
        var f30552a9730deb17649759e307e336c6 = $("input[name=f30552a9730deb17649759e307e336c6]").val(); //username
        var d40d04ce5922305fc7a1f3208fb37f82 = $().crypt({method:"sha1", source:$("input[name=d40d04ce5922305fc7a1f3208fb37f82]").val()}) //password
        var c8f717eee724806566e3ec2a90e07779 = $().crypt({method:"sha1", source:$("#c8f717eee724806566e3ec2a90e07779").text()});
        $.ajax({
            type: "POST",
            url: "/app/users/unlock.php",
            dataType: "json",
            data: {f30552a9730deb17649759e307e336c6:f30552a9730deb17649759e307e336c6, d40d04ce5922305fc7a1f3208fb37f82:d40d04ce5922305fc7a1f3208fb37f82},
            success:function(data){
                if(data.bc268ffea25b473196a0833c90a0085f == c8f717eee724806566e3ec2a90e07779){
                    $("#lockScreenDialog").dialog("close");
                    $("input[name=f30552a9730deb17649759e307e336c6]").val("");
                    $("input[name=d40d04ce5922305fc7a1f3208fb37f82]").val("");
                }
                else{
                    $("input[name=f30552a9730deb17649759e307e336c6]").val("Invalid Username/Password");
                    $("input[name=d40d04ce5922305fc7a1f3208fb37f82]").val("xxxxxxxxxxxxxxxxxxxxxxxxxx");
                }
            }
        })
    })
    
    if($("input[name=dd9ac57b5bdcdd04b763c7d0675269bf]").val() == "dd2fb68f525ccebcdb426da3353fae4b"){
        $("#lockScreenDialog").dialog("open");
        $("input[name=f30552a9730deb17649759e307e336c6]").val($("#c8f717eee724806566e3ec2a90e07779").text());
    }
    
    /* CLOCK */
    function getTime()
    {
        var d = new Date;
        var hours = d.getHours();
        var mins = d.getMinutes();
        var sec = d.getSeconds();
    if(hours > 12)
    {
        var hour = (hours - 12);
        var ampm = "PM";
    }
    else
    {
        var hour = hours;
        var ampm = "AM";
    }
    return (hour < 10 ? '0' : '') + hour + ":" + (mins < 10 ? '0' : '') + mins + ":" + (sec < 10 ? '0' : '') + sec + " " + ampm;
    }

    setInterval(function(){ // Time will be updated
        $("#clock").html(getTime())
    }, 500);
    /* ------------------------------------------------- */

    return false
});

$(document).keypress(function(e){var key=e.which;if($("input[name=focusnotifier]").val()=="0")switch(key){case 48:$("#0").trigger("click");$("#0").css("background","#444");setTimeout(function(){$("#0").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))",
"background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 49:$("#1").trigger("click");$("#1").css("background","#444");setTimeout(function(){$("#1").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))",
"background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 50:$("#2").trigger("click");$("#2").css("background","#444");setTimeout(function(){$("#2").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))",
"background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 51:$("#3").trigger("click");$("#3").css("background","#444");setTimeout(function(){$("#3").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))",
"background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 52:$("#4").trigger("click");$("#4").css("background","#444");setTimeout(function(){$("#4").css("background","#333")},100);break;case 53:$("#5").trigger("click");$("#5").css("background","#444");setTimeout(function(){$("#5").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)",
"background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 54:$("#6").trigger("click");$("#6").css("background","#444");setTimeout(function(){$("#6").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)","background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)",
"background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 55:$("#7").trigger("click");$("#7").css("background","#444");setTimeout(function(){$("#7").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)",
"background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 56:$("#8").trigger("click");$("#8").css("background","#444");setTimeout(function(){$("#8").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)",
"background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 57:$("#9").trigger("click");$("#9").css("background","#444");setTimeout(function(){$("#9").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)",
"background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 249:$("#00").trigger("click");$("#00").css("background","#444");setTimeout(function(){$("#00").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)",
"background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;case 13:$("#accept").trigger("click");$("#accept").css("background","#444");setTimeout(function(){$("#accept").css({"background-image":"linear-gradient(to bottom, #383838 0%, #2F2F2F 100%)",
"background-image":"-ms-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-moz-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-o-linear-gradient(top, #383838 0%, #2F2F2F 100%)","background-image":"-webkit-gradient(linear, left top, left bottom, color-stop(0, #383838), color-stop(1, #2F2F2F))","background-image":"-webkit-linear-gradient(top, #383838 0%, #2F2F2F 100%)"})},100);break;default:}});

</script>
<div class="cashier container_24">
    <div class="cashier-left">
        <div class="cashier-first-column grid_12 alpha">
            <div class="entered-items">
                <div class="cashier-header"> 
                    <ul>
                        <li><span id="cashier-header-description">Description</span></li>
                        <li><span id="cashier-header-qty">Qty</span></li>
<!--                        <li><span id="cashier-header-tax">Tax&nbsp;<input type="checkbox" id="checkAll"/></span></li>-->
                        <li><span id="cashier-header-amount">Amount</span></li>
                        <li><span id="cashier-header-action">&nbsp;</span></li>
                    </ul>
                </div>
                <input type="hidden" name="hiddenTotalAmount"/>
                <div class="clear"></div>
                <div class="cashier-entered-items">

                </div>
                <input type="hidden" name="dd9ac57b5bdcdd04b763c7d0675269bf" value="<?php echo $lockStatus;  ?>"/>
                <input type="hidden" id="idHolder" value=""/>
                <input type="hidden" id="transNoHolder" value=""/>
            </div>
            <div class="clear"></div>
            <div class="transaction-operations grid_12 alpha">
                <ul>
                    <li><button class="first trans-button" id="findItem"></button></li>
                    <li><button class="trans-button" id="returnTransaction"></button></li>
                    <li><button class="trans-button" id="cancelTransaction"></button></li>
                    <li><button class="trans-button" id="holdTransaction"></button></li>
                </ul>
                <div class="sounds">
                    <audio id="hoversound">
                        <source src="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/template/sounds/hover.mp3" preload="auto"></source>
                        <source src="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/template/sounds/hover.ogg" preload="auto"></source>
                    </audio>
                </div>
            </div>
        </div>
    </div>
    <div class="cashier-right">
        <div class="cashier-second-column grid_6">
            <div class="item-detail">
                <div id="placeholder">
                    <img src="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/template/images/no-image-available-cashier.png" id="itemImage"/>
                </div>
                <div class="custom-form">

                        <div>
                            <form method="post" action="" id="itemEntryForm">
                                <input type="text" id="sku" name="sku" placeholder="enter item code"/>
                            </form>
                        </div>
                </div>
            </div>

            <div class="numpad">
                <input type="hidden" name="focusnotifier" value="0"/>
                <table>
                    <tr>
                        <td colspan="3"><input type="submit" name="enterItem" id="accept" form="itemEntryForm" value="ACCEPT"/></a></td>
                    </tr>
                    <tr>
                        <td><a href="#" class="numKey" id="7">7</a></td>
                        <td><a href="#" class="numKey" id="8">8</a></td>
                        <td><a href="#" class="numKey" id="9">9</a></td>
                    </tr>
                    <tr>
                        <td><a href="#" class="numKey" id="4">4</a></td>
                        <td><a href="#" class="numKey" id="5">5</a></td>
                        <td><a href="#" class="numKey" id="6">6</a></td>
                    </tr>
                    <tr>
                        <td><a href="#" class="numKey" id="1">1</a></td>
                        <td><a href="#" class="numKey" id="2">2</a></td>
                        <td><a href="#" class="numKey" id="3">3</a></td>
                    </tr>
                    <tr>
                        <td><a href="#" class="numKey" id="0">0</a></td>
                        <td><a href="#" class="numKey" id="00">00</a></td>
                        <td><a href="#" id="clr">C</a></td>
                    </tr>
                </table>
                <div id="showNumPress">
                    <span id="numPress"></span>
                </div>
            </div>
        </div>

        <div class="cashier-third-column grid_6 omega">
            <div class="transaction-detail">
                <div class="date-time-block">
                    <span><?php echo date("F d, Y"); ?></span>
                    <span id="clock">24:00:00</span>
                </div>
                <div class="transaction-number-block">
                    <span class="trans-number" id="transactionNo"><?php echo $transNo; ?></span>
                    <span>Transaction no</span>
                </div>
                <div class="customer-transaction-block">
                    <span id="activeCustomer">Customer not set</span>
                    <span>Customer</span>
                </div>
                <div class="transaction-tax-disc-block">
                    <span class="trans-inline-title">Tax</span><span id="summaryTax" class="trans-inline-value">0.00</span><br/>
                    <span class="trans-inline-title">Discount</span><span id="summaryDiscount" class="trans-inline-value">0.00</span>
                </div>
                <div class="transaction-total-amount-block">
                    <span id="totalAmount">P 0.00</span>
                </div>
                <div class="tenderSaleButtonHolder">
                    <a href="#" class="tenderSaleButton trans-button" id="tenderSale"></a>
                </div>
            </div>
            <div class="store-operations">
                <ul>
                    <li><a href="#" id="setCustomer"></a></li>
                    <li><a href="#" id="openCloseStore"></a></li>
                    <li><a href="#" id="openCloseDrawer"></a></li>
                    <li><a href="#" id="payout"></a></li>
                    <li><a href="#" id="lockScreen"></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="dialogs">
    <div id="findItemDialog" class="ui-dialog-form">
        <table id="itemsTable">
            <thead>
                <tr>
                    <td>Code</td>
                    <td>Description</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $itemData = getItems();
                    while($item = mysql_fetch_assoc($itemData)){
                ?>
                    <tr>
                        <td><?php echo $item["sku"]; ?></td>
                        <td><?php echo $item["description_1"]; ?></td>
                        <td><button class="button addItemToEntry" data-sku="<?php echo $item["sku"]; ?>">Add to Items</button></td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
    
    <div id="setCustomerDialog" class="ui-dialog-form">
        <table id="customersTable">
            <thead>
                <tr>
                    <td>Name</td>
                    <td>Address</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $customers = getCustomers();
                    while($c = mysql_fetch_assoc($customers)){
                ?>
                <tr>
                    <td><?php echo $c["customer_name"]; ?></td>
                    <td><?php echo $c["address"]; ?></td>
                    <td><a href="#" class="setThisCustomer" data-customer="<?php echo $c["code"]; ?>">Set</a></td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div id="returnTransactionDialog" class="ui-dialog-form">
        <form id="returnTransactionForm">
            <div>
                <label for="returnTransactionNo">Transaction no:</label>
                <input type="text" id="returnTransactionNo" name="returnTransactionNo"/>
            </div>
            <div>
                <input type="submit" value="Find Transaction"/>
            </div>
            <div id="returnItems">
                <p>Enter Transaction # to view items</p>
            </div>
        </form>
    </div>
    
    <div id="tenderSaleDialog" class="ui-dialog-form">
        <form method="post" action="" id="tenderSaleForm">
            <div>
                <label for="cash">Cash:</label>
                <input type="text" id="cash" name="cash" value="0"/>
            </div>
            <!--<div>
                <label for="check">Check</label>
                <input type="text" id="check" name="check" value="0"/>
            </div>-->
            <div class="submit">
                <button id="addDiscount">Discounts</button>
            </div>
            <div id="amountInfo">
                <table>
                    <tr>
                        <td><label for="subTotal">Sub Total:</label></td>
                        <td><input type="text" id="subTotal" name="subTotal" readonly="readonly" class="no-border-textbox"/></td>
                    </tr>
                    <tr>
                        <td><label for="totalDiscount">Total Discount:</label></td>
                        <td><input type="text" id="totalDiscount" name="totalDiscount" readonly="readonly" class="no-border-textbox"/></td>
                    </tr>
                    <tr>
                        <td><label for="totalAmount">Total Amount:</label></td>
                        <td><input type="text" id="tenderTotalAmount" name="tenderTotalAmount" readonly="readonly" class="no-border-textbox"/></td>
                    </tr>
                    <tr>
                        <td><label for="totalTendered">Tendered Amount:</label></td>
                        <td><input type="text" id="totalTendered" name="totalTendered" readonly="readonly" class="no-border-textbox" value="0.00"/></td>
                    </tr>
                    <tr>
                        <td><label for="balance">Balance:</label></td>
                        <td><input type="text" id="balance" name="balance" readonly="readonly" class="no-border-textbox"/></td>
                    </tr>
                </table>
            </div>
            <div class="submit">
                <input type="submit" name="tenderSale"  value="Tender"/>
            </div>
        </form>
    </div>
    
    <div id="receiptDialog" class="ui-dialog-form">
         <div id="transactionBarcode"></div>
    </div>
    
    <div id="addDiscountDialog" class="ui-dialog-form">
        <form method="post" action="" id="addDiscountForm">
            <?php
                $discountData = getDiscounts();
                while($disc = mysql_fetch_assoc($discountData)){
            ?>
                <div class="discountSelect">
                    <input type="checkbox" class="discountTypes" name="discount" data-code="<?php echo $disc["code"]; ?>" value="<?php echo $disc["rate"]; ?>"/><?php echo $disc["type"]; ?><br/>
                </div>
            <?php
                }
            ?>
            <div class="submit">
                <?php if(getTransactionTotalDiscount($transNo) <= 0){ ?>
                <input type="submit" name="applyDiscount" value="Apply"/>
                <?php } ?>
            </div>
            <div class="submit">
                <button id="removeDiscounts" <?php if(getTransactionTotalDiscount($transNo) <= 0){ ?> disabled="disabled"  <?php } ?>>Remove</button>
            </div>
        </form>
    </div>
    
    <div id="printReceiptDialog">
        <h3>Transaction Tendered!</h3>
    </div>
    
    <div id="lockScreenDialog">
        <form id="lockScreenForm">
            <span>Enter login details to unlock the screen</span>
            <div>
                <label>Username</label>
                <input type="text" required="required" name="f30552a9730deb17649759e307e336c6"/>
            </div>
            <div>
                <label>Password</label>
                <input type="password" required="required" name="d40d04ce5922305fc7a1f3208fb37f82"/>
            </div>
            <div id="submit">
                <input type="submit" name="loginAccount" value="Unlock"/>
                <a href="http://<?php echo ROOT; ?>/users/logout.php">Logout</a>
            </div>
        </form>
    </div>
    
</div>


