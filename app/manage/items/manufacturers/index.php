<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
?>

<script>
    $("#addManufacturerDialog").dialog({
        title: "Add Manufacturer",
        autoOpen:false,
        resizable:false,
        draggable:false,
        closeOnEscape:true,
        modal:true,
        height:300,
        width:400,
        buttons:false
    })

    $("#addManufacturer").click(function(){
        $("#addManufacturerDialog").dialog("open");
    })

    $("#addManufacturerForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var manufacturer = $("input[name=manufacturer]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/manufacturers/add.php",
            data: {manufacturer: manufacturer},
            success: function(){
                $.uinotify({
                    'text'		: 'New Manufacturer Added',
                    'duration'	: 3000
                });
                $("input[name=manufacturer]").val("");
                $("#addManufacturerDialog").dialog("close");
                $("#manufacturers").trigger("click");
            }
        })
    })
    
    $("#manufacturers-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="addManufacturer">Add</a></li>
    </ul>
</div>

<table id="manufacturers-table">
    <thead>
        <tr>
            <td>Manufacturers</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $manufacturers = getManufacturers();
            while($m = mysql_fetch_assoc($manufacturers)){
        ?>
        <tr>
            <td><?php echo $m["manufacturer_name"]; ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<div class="ui-dialog-form" id="addManufacturerDialog">
    <form method="post" action="" id="addManufacturerForm">
        <div class="grid_9 alpha">
            <div>
                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer"  required="required" name="manufacturer" value=""/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9 omega">
            <div class="submit">
                <input type="submit" value="Add Manufacturer"/>
            </div>
        </div>
    </form>
</div>