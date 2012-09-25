<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    
    $("#addBrandDialog").dialog({
        title: "Add Brand",
        autoOpen: false,
        draggable: false,
        resizable:false,
        closeOnEscape:true,
        modal:true,
        width:400,
        height:350,
        buttons: false
    })
    
    $("#addBrand").click(function(){
        $("#addBrandDialog").dialog("open");
    })
    
    $("#addBrandForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var manufacturer = $("#manufacturer").val();
        var brand = $("input[name=brand]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/brands/add.php",
            data: {manufacturer: manufacturer, brand: brand},
            success: function(){
                $.uinotify({
	                'text'		: 'New Brand Added',
	                'duration'	: 2000
                });
                $("input[name=brand]").val("");
                $("#addBrandDialog").dialog("close");
                $("#brands").trigger("click");
            }
        })
    })
    
    $("#brands-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="addBrand">Add</a></li>
    </ul>
</div>

<table id="brands-table">
    <thead>
        <tr>
            <td>Manufacturer</td>
            <td>Brand</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $brands = getBrands();
            while($b = mysql_fetch_assoc($brands)){
        ?>
        <tr>
            <td><?php echo getManufacturerNameByCode($b["manufacturer_code"]); ?></td>
            <td><?php echo $b["brand_name"]; ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<div id="addBrandDialog" class="ui-dialog-form">
    <form method="post" action="" id="addBrandForm">
        <div class="grid_8 alpha">
            <div>
                <label for="manufacturer">Manufacturer:</label>
                <select id="manufacturer" name="manufacturer">
                    <?php
                        $manufacturerData = getManufacturers();
                        while($mftr = mysql_fetch_assoc($manufacturerData)){
                    ?>
                    <option value="<?php echo $mftr["code"]; ?>"><?php echo $mftr["manufacturer_name"]; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <div>
                <label for="brand">Brand:</label>
                <input type="text" id="brand" required="required" name="brand" value=""/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9">
            <div class="submit">
                <input type="submit" name="addBrand" value="Add Brand"/>
            </div>
        </div>
    </form>
</div>
