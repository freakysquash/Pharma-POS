<?php
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
?>

<script>
    $(document).ready(function(){
        
        function changeUrl(page){
            var object = {page: page};
            history.pushState(object, "", "?module=purchase&page=" + page)
        }
        
        $("#listPurchaseOrders").click(function(){
           changeUrl("list");
           $(".purchase-content").load("/app/inventory/purchase/list.php");
           $(".purchase-toolbar ul li a").each(function(){
               $(this).removeClass("selected");
           })
           $(this).addClass("selected");
        })
        
        $("#createPurchaseOrder").click(function(){
            changeUrl("create");
           $(".purchase-content").load("/app/inventory/purchase/create.php");
           $(".purchase-toolbar ul li a").each(function(){
               $(this).removeClass("selected");
           })
           $(this).addClass("selected");
        })
        
        var page = $(document).getUrlParam("page");
        switch(page){
            case "list":
                $(".purchase-content").load("/app/inventory/purchase/list.php");
                break;
           case "create":
                $(".purchase-content").load("/app/inventory/purchase/create.php");
                break;
           default:
               $(".purchase-content").load("/app/inventory/purchase/list.php");
               break;
        }
        
    })
</script>

<div class="window">
<div class="window-title">
    <span>Purchase</span>
</div>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="listPurchaseOrders">Purchase Orders</a></li>
        <li><a href="#" id="createPurchaseOrder">Create</a></li>
    </ul>
</div>
<div class="purchase-content">

</div>
</div>

