<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"> 
	<head>
        <title>my Pharma POS<?php if(isset($_GET["module"])){ echo " | " . ucwords($_GET["module"]); } ?></title>
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
                switch(module){
                    case "cashier":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#cashierMenu").addClass("main-menu-active");
                        break;
                    case "transactions":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#transactionsMenu").addClass("main-menu-active");
                        break;
                    case "inventory":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#inventoryMenu").addClass("main-menu-active");
                        break;
                    case "customers":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#customersMenu").addClass("main-menu-active");
                        break;
                    case "reports":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#reportsMenu").addClass("main-menu-active");
                        break;
                    case "manage":
                        $(".main-menu").removeClass("main-menu-active");
                        $("#managerMenu").addClass("main-menu-active");
                        break;
                    default:
                        
                }
                
            })
        </script>
    </head>
    <body>
        <div class="header-wrapper">
            <div class="header container_24">
                <div class="grid_6 alpha logo">
                                <a href="/"><h3>my pharma pos</h3></a>
                </div>
                <div class="grid_4 user-nav push_14 omega">
                    <?php
                        if(isset($_SESSION["userId"])){
                    ?>
                    <ul>
                        <li><a href="#" id="c8f717eee724806566e3ec2a90e07779"><?php echo $_SESSION["username"]; ?></a></li>
                        <li><span style="color:#f0f0f0;"> | </span></li>
                        <li><a href="http://<?php echo ROOT; ?>/app/users/logout.php">Logout</a></li>
                    </ul>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="top-wrapper">
                <div class="top container_24">
                    <?php
                        if(isset($_SESSION["userId"])){
                    ?>
                    <ul>
                        <li><a href="/?module=cashier" id="cashierMenu" class="main-menu">Cashier</a></li>
                        <li><a href="/?module=transactions" id="transactionsMenu" class="main-menu">Transactions</a></li>
                        <li><a href="/?module=manage&page=inventory" id="inventoryMenu" class="main-menu">Inventory</a></li>
                         <li><a href="/?module=customers" id="customersMenu" class="main-menu">Customers</a></li>
                        <li><a href="/?module=reports" id="reportsMenu" class="main-menu">Reports</a></li>
                        <li class="last-main-menu"><a href="/?module=manage" id="managerMenu" class="main-menu">Manager</a></li>
                    </ul>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="content-block container_24">