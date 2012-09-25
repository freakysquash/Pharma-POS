<?php
    include("../../../library/config.php");
    authenticate();
?>

<script>
    $("#itemListTable").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
    
    $("#viewItemDialog").dialog({
        title: "View Item",
        autoOpen:false,
        modal:true,
        resizable:false,
        draggable:false,
        closeOnEscape:true,
        width:500,
        height:300
    })
    
    $(".viewItem").click(function(){
        var sku = $(this).attr("data-sku");
        $("#viewItemDialog").load("/app/manage/items/view.php?s=" + sku);
        $("#viewItemDialog").dialog("open");
    })
    
    $("#changeImageDialog").dialog({
        title: "Change Item Image",
        autoOpen:false,
        draggable:false,
        resizable:false,
        closeOnEscape:true,
        modal:true,
        height:180,
        width:370
    })
             
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="http://<?php echo ROOT; ?>/?module=manage&page=addItem" id="addItem">Add Item</a></li>
    </ul>
</div>

<table id="itemListTable">
    <thead>
        <tr>
            <td>Description</td>
            <td>Generic</td>
            <td>Price</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $items = getItems();
            while($i = mysql_fetch_assoc($items)){
        ?>
        <tr>
            <td><a href="#" data-sku="<?php echo $i["sku"]; ?>" class="viewItem"><?php echo $i["description_1"]; ?></a></td>
            <td><?php echo $i["generic_name"]; ?></td>
            <td style="text-align:right;"><?php echo $i["price"]; ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<div id="dialogs">
    <div id="viewItemDialog">
        No item selected.
    </div>
    <div id="changeImageDialog" class="ui-dialog-form">
        <form method="post" action="http://<?php echo ROOT; ?>/app/manage/items/changeImage.php" enctype="multipart/form-data">
            <input type="hidden" name="imageSku"/>
            <div>
                <input type="file" name="image"/>
            </div>
            <div>
                <input type="submit" value="Change Photo"/>
            </div>
        </form>
    </div>
</div>
