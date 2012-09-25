<script type="text/javascript">
    $("#manufacturer").bind('change click keyup', function() {
        var c = $("#manufacturer").val() + $("#brand").val() + $("#type").val();
        var d = $("#brand option:selected").text() + $("#type option:selected").text() + $("#quantity").val() + $("#unit").val()
        $("#prompt").load("../manage/items/checkItemCodeDesc.php?c=" + c + "&d=" + d);
    })
    
    $("#brand").bind('change click keyup', function() {
        $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + " " + $("#unit").val());
        var c = $("#manufacturer").val() + $("#brand").val() + $("#type").val();
        var d = $("#brand option:selected").text() + $("#type option:selected").text() + $("#quantity").val() + $("#unit").val()
        $("#prompt").load("../manage/items/checkItemCodeDesc.php?c=" + c + "&d=" + d);
    })
    
    $("#type").bind('change click keyup', function() {
        $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + " " + $("#unit").val());
        var c = $("#manufacturer").val() + $("#brand").val() + $("#type").val();
        var d = $("#brand option:selected").text() + $("#type option:selected").text() + $("#quantity").val() + $("#unit").val()
        $("#prompt").load("../manage/items/checkItemCodeDesc.php?c=" + c + "&d=" + d);
    })
    
    $("#quantity").keyup(function() {
        $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + " " + $("#unit").val());
        var c = $("#manufacturer").val() + $("#brand").val() + $("#type").val();
        var d = $("#brand option:selected").text() + $("#type option:selected").text() + $("#quantity").val() + $("#unit").val()
        $("#prompt").load("../manage/items/checkItemCodeDesc.php?c=" + c + "&d=" + d);
    })
    
    $("#unit").bind('change click keyup', function() {
        $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + " " + $("#unit").val());
        var c = $("#manufacturer").val() + $("#brand").val() + $("#type").val();
        var d = $("#brand option:selected").text() + $("#type option:selected").text() + $("#quantity").val() + $("#unit").val()
        $("#prompt").load("../manage/items/checkItemCodeDesc.php?c=" + c + "&d=" + d);
    })
</script>

<form method="post" action="">
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
    <div id="brands">
        <label for="brand">Brand:</label>
        <select id="brand" name="brand">
            <?php
                $brandData = getBrands();
                while($brand = mysql_fetch_assoc($brandData)){
            ?>
            <option value="<?php echo $brand["code"]; ?>"><?php echo $brand["brand_name"]; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
    <div id="types">
        <label for="type">Type:</label>
        <select id="type" name="type">
            <?php
                $typeData = getTypes();
                while($type = mysql_fetch_assoc($typeData)){
            ?>
            <option value="<?php echo $type["code"]; ?>"><?php echo $type["type_name"]; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
    <div>
        <label for="department">Department:</label>
        <select id="department" name="department">
            <?php
                $departmentData = getDepartments();
                while($dep = mysql_fetch_assoc($departmentData)){
            ?>
            <option value="<?php echo $dep["code"]; ?>"><?php echo $dep["department_name"]; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
    <div>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <?php
                $categoryData = getCategories();
                while($cat = mysql_fetch_assoc($categoryData)){
            ?>
            <option value="<?php echo $cat["code"]; ?>"><?php echo $cat["category_name"]; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
    <div class="uom">
        <label>UOM:</label>
        <input type="text" id="quantity" name="quantity" value="<?php if(isset($_POST["quantity"])){ echo $_POST["quantity"]; } ?>"/>
        <select id="unit" name="unit">
            <?php
                $measurementData = getMeasurements();
                while($mea = mysql_fetch_assoc($measurementData)){
            ?>
            <option value="<?php echo $mea["measurement_name"]; ?>"><?php echo $mea["measurement_name"]; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
    <div id="prompt">
        
    </div>
    <div>
        <label for="desc1">Description 1:</label>
        <textarea id="desc1" name="desc1"></textarea> 
    </div>
    <div>
        <label for="desc2">Description 2:</label>
        <textarea id="desc2" name="desc2"></textarea>  
    </div>
    <div>
        <label for="price">Price:</label>
        <input type="text" id="price" name="price"/> 
    </div>
    <div>
        <input class="chiclet-blue" type="submit" name="addItem" value="Add Item"/> 
    </div>
</form>