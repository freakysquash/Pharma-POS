<?php
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    authenticate();
?>

<script type="text/javascript">
    $(document).ready(function(){
        
        function changeUrl(page){
            var object = {page: page};
            history.pushState(object, "", "?module=manage&page=" + page)
        }
        
        $("#inventoryList").click(function(){
           $(".manage-workspace").load("/app/inventory/index.php");
        })
        
        $("#inventoryCount").click(function(){
           $(".manage-workspace").load("/app/inventory/count.php");
        })
        
        $("#purchases").click(function(){
           $(".manage-workspace").load("/app/inventory/purchase/index.php");
           changeUrl("purchase");
        })
        
        $("#itemList").click(function(){
           $(".manage-workspace").load("/app/manage/items/index.php");
           changeUrl("itemList");
        })
        
        $("#addItem").click(function(){
           $(".manage-workspace").load("/app/manage/items/add.php");
           changeUrl("addItem");
        })
        
        $("#addItem").click(function(){
           $(".manage-workspace").load("/app/manage/items/add.php");
        })
        
        $("#departments").click(function(){
           $(".manage-workspace").load("/app/manage/items/departments/index.php");
           changeUrl("departments");
        })
        
        $("#categories").click(function(){
           $(".manage-workspace").load("/app/manage/items/categories/index.php");
           changeUrl("categories");
        })
        
        $("#manufacturers").click(function(){
           $(".manage-workspace").load("/app/manage/items/manufacturers/index.php");
        })
        
        $("#brands").click(function(){
           $(".manage-workspace").load("/app/manage/items/brands/index.php");
           changeUrl("brands");
        })
        
        $("#addType").click(function(){
           $(".manage-workspace").load("/app/manage/items/types/add.php");
           changeUrl("addType")
        })
        
        $("#addPackaging").click(function(){
           $(".manage-workspace").load("/app/manage/items/packagings/add.php");
           changeUlr("addPackaging");
        })
        
        $("#addMeasurement").click(function(){
           $(".manage-workspace").load("/app/manage/items/measurements/add.php");
           changeurl("addMeasurements");
        })
        
        $("#addSupplier").click(function(){
           $(".manage-workspace").load("/app/manage/suppliers/add.php");
           changeUrl("addSupplier");
        })
        
        $("#addSalesTax").click(function(){
           $(".manage-workspace").load("/app/manage/items/taxes/add.php");
           changeUrl("addSalesTax")
        })
        
        $("#addDiscount").click(function(){
           $(".manage-workspace").load("/app/manage/items/discounts/add.php");
           changeUrl("addDiscount");
        })
        
        $("#importItems").click(function(){
           $(".manage-workspace").load("/app/manage/items/import.php");
           changeUrl("import");
        })
        
        $("#company").click(function(){
           $(".manage-workspace").load("/app/manage/company/index.php");
           changeUrl("company");
        })
        
        $("#store").click(function(){
           $(".manage-workspace").load("/app/manage/stores/index.php");
           changeUrl("store");
        })
        
       $("#users").click(function(){
           $(".manage-workspace").load("/app/users/index.php");
           changeUrl("users");
        })
        
        var page =  $(document).getUrlParam("page");
        switch(page){
            case "itemList":
                $(".manage-workspace").load("/app/manage/items/index.php");
                break;
            case "addItem":
                $(".manage-workspace").load("/app/manage/items/add.php");
                break;
            case "departments":
                $(".manage-workspace").load("/app/manage/items/departments/index.php");
                break;
            case "categories":
                $(".manage-workspace").load("/app/manage/items/categories/index.php");
                break;
            case "manufacturers":
                $(".manage-workspace").load("/app/manage/items/manufacturers/index.php");
                break;
            case "brands":
                $(".manage-workspace").load("/app/manage/items/brands/index.php");
                break;
            case "addType":
                $(".manage-workspace").load("/app/manage/items/types/add.php");
                break;
           case "addPackaging":
                $(".manage-workspace").load("/app/manage/items/packagings/add.php");
                break;
            case "addMeasurement":
                $(".manage-workspace").load("/app/manage/items/measurements/add.php");
                break;
            case "addSupplier":
                $(".manage-workspace").load("/app/manage/suppliers/add.php");
                break;
            case "addSalesTax":
                $(".manage-workspace").load("/app/manage/items/taxes/add.php");
                break;
            case "addDiscount":
                $(".manage-workspace").load("/app/manage/items/discounts/add.php");
                break;
            case "import":
                $(".manage-workspace").load("/app/manage/items/import.php");
                break;
            case "inventoryCount":
                $(".manage-workspace").load("/app/inventory/count.php");
                break;
            case "inventory":
                $(".manage-workspace").load("/app/inventory/index.php");
                break;
            case "company":
                $(".manage-workspace").load("/app/manage/company/index.php");
                break;
            case "store":
                $(".manage-workspace").load("/app/manage/stores/index.php");
                break;
            case "users":
                $(".manage-workspace").load("/app/users/index.php");
                break;
            default:
                 $(".manage-workspace").load("/app/inventory/index.php");
                break;
        }
        
        var windowHeight = $(".content-block").height() - 35;
        $(".navigation").css({ "height" : windowHeight + "px"});
        
        $(".nav").accordion({
            icons: false,
            fillSpace:true
        });
        
        if($(document).getUrlParam("page") == "inventory"){
            $(".nav").accordion({
                active: 1
            })
        }
        
        $( ".accordion-submenu" ).button({
            icons: {
                primary: "ui-icon-carat-1-e"
            }
        })
    })
</script>

<div class="window">
<div class="window-title">
    <span>Manager</span>
</div>
<div class="window-content">
    <div class="grid_6 alpha navigation">
        <div class="nav">
            <h3>Products</h3>
            <ul>
                <li><a href="#" id="itemList" class="accordion-submenu">Item List</a></li>
                <li><a href="#" id="addItem" class="accordion-submenu">Add Item</a></li>
                <li><a href="#" id="departments" class="accordion-submenu">Departments</a></li>
                <li><a href="#" id="categories" class="accordion-submenu">Categories</a></li>
                <li><a href="#" id="manufacturers" class="accordion-submenu">Manufacturers</a></li>
                <li><a href="#" id="brands" class="accordion-submenu">Brands</a></li>
                <li><a href="#" id="addType" class="accordion-submenu">Types</a></li>
                <li><a href="#" id="addPackaging" class="accordion-submenu">Packaging</a></li>
                <li><a href="#" id="addMeasurement" class="accordion-submenu">Measurement</a></li>
                <li><a href="#" id="addSupplier" class="accordion-submenu">Supplier</a></li>
                <li><a href="#" id="addSalesTax" class="accordion-submenu">Sales Tax</a></li>
                <li><a href="#" id="addDiscount" class="accordion-submenu">Discounts</a></li>
                <li><a href="#" id="importItems" class="accordion-submenu">Import Item List</a></li>
            </ul>
            <h3>Inventory</h3>
                <ul>
                    <li><a href="#" id="inventoryList" class="accordion-submenu">Inventory List</a></li>
                    <li><a href="#" id="inventoryCount" class="accordion-submenu">Update Inventory</a></li
                    <li><a href="/?module=purchase" class="accordion-submenu">Purchase Orders</a></li>
                </ul>
            <h3>Configuration</h3>
                <ul>
                    <li><a href="#" id="company" class="accordion-submenu">Company</a></li>
                    <li><a href="#" id="store" class="accordion-submenu">Store Setup</a></li>
                    <li><a href="#" id="users" class="accordion-submenu">User Accounts</a></li>
                </ul>
            <h3>Reports</h3>
            <ul>
                <li><a href="http://<?php echo ROOT; ?>/?module=reports" id="reports" class="accordion-submenu">Generate Reports</a></li>
            </ul>
        </div>
    </div>
    <div class="grid_18 omega manage-workspace">

    </div>
</div>
</div>
