<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"> 
	<head>
        <title>POS<?php if(isset($_GET["module"])){ echo " | " . ucwords($_GET["module"]); } ?></title>
        <link rel="shortcut icon" href="http://<?php echo ROOT; ?>/template/images/favicon.ico"/>  
        <link type="text/css" rel="stylesheet" href="http://<?php echo ROOT; ?>/template/styles/reset.css"/>
        <link type="text/css" rel="stylesheet" href="http://<?php echo ROOT; ?>/template/styles/aristo.css"/>
        <link type="text/css" rel="stylesheet" href="http://<?php echo ROOT; ?>/template/styles/960gs24col.css"/>
        <link type="text/css" rel="stylesheet" href="http://<?php echo ROOT; ?>/template/styles/jquery.dataTables_themeroller.css"/>
        <link type="text/css" rel="stylesheet" href="http://<?php echo ROOT; ?>/template/styles/style.css"/>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery-ui-1.8.20.custom.min.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.browser.min.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.getUrlParam.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.printElement.min.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/uinotify.jquery.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery.crypt.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/jquery-barcode-2.0.2.js"></script>
        <script type="text/javascript" src="http://<?php echo ROOT; ?>/template/scripts/custom-ui.js"></script>
        <noscript><meta http-equiv="refresh" content="0; url=http://<?php echo ROOT; ?>/no-js.php"></noscript>
        <script>
            $(document).ready(function(){
                
                $('#browserCheckDialog').dialog({
                   title: "Unsupported browser",
                   autoOpen:false,
                   modal:true,
                   closeOnEscape:false,
                   resizable:false,
                   draggable:false,
                   width:500,
                   height:400,
                   open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
               })
               
                if($.browser.name == "chrome" && parseInt($.browser.version) < 16){
                    $('#browserCheckDialog').dialog("open");
                }
                if($.browser.name == "msie" && parseInt($.browser.version) < 9){
                    $('#browserCheckDialog').dialog("open");
                }
                if($.browser.name == "firefox" && parseInt($.browser.version) < 10){
                    $('#browserCheckDialog').dialog("open");
                }
                if($.browser.name == "safari" && parseInt($.browser.version) < 5){
                    $('#browserCheckDialog').dialog("open");
                }
                if($.browser.name == "opera" && parseInt($.browser.version) < 11){
                    $('#browserCheckDialog').dialog("open");
                }
                if($.browser.name != "chrome" && $.browser.name != "firefox" && $.browser.name != "msie" && $.browser.name != "safari" && $.browser.name != "opera") {
                    $('#browserCheckDialog').dialog("open");
                }
                
                var module = $(document).getUrlParam("module");
                var page = $(document).getUrlParam("page");
                if(module == "manage" && page == "inventory"){
                    $(".main-menu").removeClass("current");
                    $("#inventoryMenu").addClass("current");
                }
                else if(module == "manage"){
                    $(".main-menu").removeClass("current");
                    $("#managerMenu").addClass("current");
                }
                switch(module){
                    case "cashier":
                        $(".main-menu").removeClass("current");
                        $("#cashierMenu").addClass("current");
                        break;
                    case "transactions":
                        $(".main-menu").removeClass("current");
                        $("#transactionsMenu").addClass("current");
                        break;
                    case "customers":
                        $(".main-menu").removeClass("current");
                        $("#customersMenu").addClass("current");
                        break;
                    case "reports":
                        $(".main-menu").removeClass("current");
                        $("#reportsMenu").addClass("current");
                        break;
                    default:
                        
                }

            })
        </script>
    </head>
    <body>    
        <div id="container">
            <div id="header_wrapper">
                <div id="header">
                    <div class="header_top">
                        <div class="logo">Point of Sale</div> <!-- Point of Sale logo -->
                        <!-- navigation start -->
                        <div class="nav">
                            <?php
                                if(isset($_SESSION["userId"])){
                            ?>
                                <ul>
                                    <li class="current main-menu" id="cashierMenu"><a href="/?module=cashier">Cashier</a></li>
                                    <li class="current main-menu" id="transactionsMenu"><a href="/?module=transactions">Transactions</a></li>
                                    <li class="current main-menu" id="inventoryMenu"><a href="/?module=manage&page=inventory">Inventory</a></li>
                                    <li class="current main-menu" id="customersMenu"><a href="/?module=customers">Customers</a></li>
                                    <li class="current main-menu" id="reportsMenu"><a href="/?module=reports">Reports</a></li>
                                    <li class="current main-menu" id="managerMenu"><a href="/?module=manage">Manager</a></li>
                                </ul>
                             <?php
                                }
                            ?>
                        </div>
                        <!-- navigation end -->
                </div>
                <div class="header_bottom">
                    <h1 class="title"><?php if(isset($_SESSION["userId"])){ echo getCompanyName(); } ?></h1>
                    <div class="user_action ">
                        <?php
                            if(isset($_SESSION["userId"])){
                        ?>
                        <ul>
                            <li><a href="#" id="c8f717eee724806566e3ec2a90e07779"><?php echo $_SESSION["username"]; ?></a></li> <!-- User -->
                            <li><a href="http://<?php echo ROOT; ?>/app/users/logout.php" id="logout">Logout</a></li> <!-- Logout -->
                        </ul>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                    <!-- navigation end -->
            </div>
        </div>
        <!-- body content start -->
        <div class="content_wrapper">
            <div class="content">