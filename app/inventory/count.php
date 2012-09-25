<?php
    include("../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
?>

<script>

    $("#item-inventory-summary").hide();
    $("#sku").change(function(){
        var sku = $("#sku").val();
        $("#itemName").text("");
        $("#itemStockCount").text("");
        $("#itemReorderMin").text("");
        $("#itemReorderLevel").text("");
        $.ajax({
            type: "POST",
            url: "/app/inventory/inventoryCountCheck.php",
            data: {sku: sku},
            dataType: "json",
            success: function(data){
                $("#itemName").text($("#sku option:selected").text());
                $("#itemStockCount").text(data[2]);
                $("input[name=newStockCount]").val(data[2]);
                $("#itemReorderMin").text(data[3]);
                $("input[name=newReorderMin]").val(data[3]);
                $("#itemReorderLevel").text(data[4]);
                $("input[name=newReorderLevel]").val(data[4]);
                if(data[2] > data[3]){
                    $("#itemStatus").text("On Stock");
                }
                else{
                    $("#itemStatus").text("Critical Level");
                }
                $("#item-inventory-summary").show();
            }
        })
    })
    
    $("#updateItemInventory").unbind("submit").submit(function(e){
        e.preventDefault();
        var sku = $("#sku").val();
        var stockCount = $("input[name=newStockCount]").val();
        var reorderMin = $("input[name=newReorderMin]").val();
        var reorderLevel = $("input[name=newReorderLevel]").val();
        $.ajax({
            type: "POST",
            url: "/app/inventory/update.php",
            data: {sku: sku, stockCount: stockCount, reorderMin: reorderMin, reorderLevel: reorderLevel},
            success: function(){
                $.uinotify({
                        "text": "Inventory Updated.",
                        "duration": 3000
                });
                $("input[name=newStockCount]").val("");
                $("input[name=newReorderMin]").val("");
                $("input[name=newReorderLevel]").val("");
                $.ajax({
                    type: "POST",
                    url: "/app/inventory/inventoryCountCheck.php",
                    data: {sku: sku},
                    dataType: "json",
                    success: function(data){
                        $("#itemName").text($("#sku option:selected").text());
                        $("#itemStockCount").text(data[2]);
                        $("input[name=newStockCount]").val(data[2]);
                        $("#itemReorderMin").text(data[3]);
                        $("input[name=newReorderMin]").val(data[3]);
                        $("#itemReorderLevel").text(data[4]);
                        $("input[name=newReorderLevel]").val(data[4]);
                        if(data[2] > data[3]){
                            $("#itemStatus").text("On Stock");
                        }
                        else{
                            $("#itemStatus").text("Critical Level");
                        }
                        $("#item-inventory-summary").show();
                    }
                })
            }
        })
    })
</script>

<div class="custom-form">
    <form method="post" action="">
        <div class="grid_10">
            <label for="sku">Select Item:</label>
            <select id="sku">
                <option value=""></option>
            <?php
                $itemData = getSkus();
                while($s = mysql_fetch_assoc($itemData)){
            ?>
                <option value="<?php echo $s["sku"]; ?>"><?php echo getDescriptionBySku($s["sku"]); ?></option>
            <?php
                }
            ?>
            </select>
        </div>
    </form>
</div>

<div class="clear"></div>
<div id="item-inventory-summary">
    <div class="grid_5 alpha">Item:</div><div class="grid_10 omega"><span id="itemName"></span></div>
    <div class="clear"></div>
    <div class="grid_5 alpha">Current Stock Count:</div><div class="grid_10 omega"><span id="itemStockCount"></span></div>
    <div class="clear"></div>
    <div class="grid_5 alpha">Minimum Count:</div><div class="grid_10 omega"><span id="itemReorderMin"></span></div>
    <div class="clear"></div>
    <div class="grid_5 alpha">Reorder level:</div><div class="grid_10 omega"><span id="itemReorderLevel"></span></div>
    <div class="clear"></div>
    <div class="grid_5 alpha">Status:</div><div class="grid_10 omega"><span id="itemStatus"></span></div>
    <div class="clear"></div>
    <br/>
    <br/>
    <div class="custom-form">
        <form id="updateItemInventory">
            <div class="grid_8 alpha">
                <label for="newStockCount">Starting Stock Count:</label>
                <input type="text" id="newStockCount" name="newStockCount"/>
            </div>
            <div class="clear"></div>
            <div class="grid_8 alpha">
                <label for="newReorderMin">Minimum Count:</label>
                <input type="text" id="newReorderMin" name="newReorderMin"/>
            </div>
            <div class="clear"></div>
            <div class="grid_8 alpha">
                <label for="newReorderLevel">Reorder Level:</label>
                <input type="text" id="newReorderLevel" name="newReorderLevel"/>
            </div>
            <div class="clear"></div>
            <div class="grid_8 alpha">
                <input type="submit" value="Update Inventory Count"/>
            </div>
        </form>
    </div>
</div>
