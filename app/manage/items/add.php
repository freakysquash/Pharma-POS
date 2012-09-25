<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    /* ADD ITEM */
    if(isset($_POST["price"])){
        $errors = null;
        $mbCode = getManufacturerBrandCode($_POST["manufacturer"], $_POST["brand"]);
        $measurementCode = getAvailableMeasurementCode($_POST["manufacturer"], $_POST["brand"], $_POST["type"], $_POST["measurement"]);
        $itemCode = $mbCode . $_POST["type"] . $measurementCode;
        $sku =  getAvailableSku();
        $packaging = $_POST["packaging"];
        $departmentCode = mres($_POST["department"]);
        $categoryCode = mres($_POST["category"]);
        $desc1 = mres($_POST["desc1"]);
        $desc2 = mres($_POST["desc2"]);
        $genericName = mres($_POST["genericName"]);
        $price = mres($_POST["price"]);
        $tax = mres($_POST["tax"]);
        $supplier = mres($_POST["supplier"]);
        if(empty($errors)){
            newSku($sku);
            newMeasurementCode($measurementCode, $_POST["manufacturer"], $_POST["brand"], $_POST["type"], $_POST["measurement"]);
            addItem($itemCode, $sku, $packaging, $departmentCode, $categoryCode,  $desc1, $desc2, $genericName, $price);
            applyTax($sku, $tax);
            assignItemToSupplier($supplier, $sku);
        }
        else{
            
?>
           <div class='add-item-dialog'>
               <ul><?php echo $errors; ?></ul>
           </div>
        <?php
        
        }
    }
?>
<script>
    
        String.prototype.killWhiteSpace = function() {
            return this.replace(/\s/g, '%20');
        }
        
        function description2(){
            var b = $("#brand option:selected").text();
            var t = $("#type option:selected").text();
            var q = $("#quantity").val();
            var u = $("#unit").val();
            var description = (b + t + q + u).replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
            var trimmed = description.substring(1, 20).replace(/a/g, "").replace(/e/g, "").replace(/i/g, "").replace(/o/g, "").replace(/u/g, "").replace(/A/g, "").replace(/E/g, "").replace(/O/g, "").replace(/I/g, "").replace(/O/g, "").replace(/U/g, "").replace(/ /g, "");
            var up = description.substring(0, 1) + trimmed.toUpperCase();
            $("#desc2").val(up);
        }

        $("#manufacturer").bind('change', function() {
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text()).killWhiteSpace();
            $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
            $("#brand").find("option").remove().end();
            $.getJSON("/app/manage/items/brands/brandList.php?m=" + $("#manufacturer").val(), function(data){
                $.each(data, function(i,item)
                {
                    $("#brand").append("<option value='" + item.code + "'>" + item.brand_name + "</option>")
                });
            })
        })
        
        $("#brand").bind('click', function() {
            $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text());
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text()).killWhiteSpace();
            $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
            $("#type").find("option").remove().end();
            $.getJSON("/app/manage/items/types/typeList.php?b=" + $("#brand").val(), function(data){
                $.each(data, function(i,item)
                {
                    $("#type").append("<option value='" + item.code + "'>" + item.type_name + "</option>")
                });
            })
            description2();
        })
        
        $("#type").bind('change click keyup', function() {
           $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text());
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text()).killWhiteSpace();
             $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
             description2();
        })
        
        $("#department").bind("change", function(){
            $("#category").find("option").remove().end();
            $.getJSON("/app/manage/items/categories/categoryList.php?d=" + $("#department").val(), function(data){
                $.each(data, function(i,item)
                {
                    $("#category").append("<option value='" + item.code + "'>" + item.category_name + "</option>")
                });
            })
        })
        
        $("#quantity").keyup(function() {
             $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text());
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val()).killWhiteSpace();
            $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
            description2();
        })

        $("#unit").bind('change click keyup', function() {
            $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text());
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text()).killWhiteSpace();
             $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
             description2();
        })
        
        $("#packaging").bind('change click keyup', function() {
            $("#desc1").val($("#brand option:selected").text() + " " + $("#type option:selected").text()  + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text());
            var p = $("#packaging").val();
            var d = ($("#brand option:selected").text() + " " + $("#type option:selected").text() + " " + $("#quantity").val() + $("#unit").val() + " " + $("#packaging option:selected").text()).killWhiteSpace();
             $("#prompt").load("/app/manage/items/checkItemPackDesc.php?p=" + p + "&d=" + d);
        })
        
    $( ".add-item-dialog" ).dialog({
        title: "Add Item Error",
        minHeight: 120,
        width: 350,
        modal: true,
        closeOnEscape: true,
        resizable: false,
        buttons: {
            Close: function() {
                $(this).dialog( "close" );
            }
        }
    });
            
     $("#addItemForm").unbind("submit").submit(function(e){
        e.preventDefault();
        $("input[name=addItem]").attr("disabled", "disabled");
        var manufacturer = $("#manufacturer").val();
        var brand = $("#brand").val();
        var type = $("#type").val();
        var packaging = $("#packaging").val();
        var department = $("#department").val();
        var category = $("#category").val();
        var measurement = $("input[name=quantity]").val() + " " + $("#unit").val();
        var desc1 = $("#desc1").val();
        var desc2 = $("#desc2").val();
        var genericName = $("#genericName").val();
        var price = $("input[name=price]").val();
        var tax = $("#tax").val();
        var supplier = $("#itemSupplier").val();
        if(department != "" && category != "" && packaging != "" && $("#unit").val() != "" && $("input[name=quantity]").val() != ""){
        $.ajax({
            type: "POST",
            url: "/app/manage/items/add.php",
            data: { manufacturer: manufacturer, brand: brand, type: type, packaging: packaging, department: department, category: category, measurement: measurement, desc1: desc1, desc2: desc2, genericName: genericName, price: price, tax: tax, supplier: supplier },
            success: function(){
                $.uinotify({
	                'text'		: 'New Item Added',
	                'duration'	: 3000
                });
                setTimeout(function() {
                    $("#addItem").trigger("click");
                }, 3500);
            }
        })
        }
     })
</script>

<div class="custom-form grid_18 omega">
    <form id="addItemForm">
        <div class="grid_6 alpha">
            <label for="manufacturer">Manufacturer:</label>
            <select id="manufacturer" name="manufacturer">
                <option value=""></option>
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
        <div class="grid_6">
            <label for="brand">Brand:</label>
            <select id="brand" name="brand">
                <option value=""></option>
            </select>
        </div>
        <div class="grid_6 omega">
            <label for="type">Type:</label>
            <select id="type" name="type">
                <option value=""></option>
            </select>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <div>
                <label for="department">Department:</label>
                <select id="department" name="department">
                    <option value=""></option>
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
        </div>
        <div class="grid_6">
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="grid_6 omega">
            <div class="uom">
                <label>UOM:</label>
                <input type="text" id="quantity" name="quantity" required="required" value="<?php if(isset($_POST["quantity"])){ echo $_POST["quantity"]; } ?>"/>
                <select id="unit" name="unit">
                    <option value=""></option>
                    <?php
                        $measurementData = getMeasurements();
                        while($mea = mysql_fetch_assoc($measurementData)){
                    ?>
                    <option value="<?php echo $mea["measurement_name"]; ?>"><?php echo $mea["measurement_name"]; ?></option>
                    <?php
                        }
                    ?>
                </select>
                <select id="packaging" name="packaging">
                    <option value=""></option>
                    <?php
                        $packagingData = getPackagings();
                        while($pack = mysql_fetch_assoc($packagingData)){
                    ?>
                    <option value="<?php echo $pack["code"]; ?>"><?php echo "(" . $pack["description"] . ")"; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_18">
            <div id="prompt">

            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <div>
                <label for="desc1">Description 1:</label>
                <textarea id="desc1" name="desc1" readonly="readonly"></textarea> 
            </div>
        </div>
        <div class="grid_6">
            <div>
                <label for="desc2">Description 2:</label>
                <textarea id="desc2" required="required" readonly="readonly" name="desc2"></textarea>  
            </div>
        </div>
        <div class="grid_6 omega">
            <div>
                <label for="genericName">Generic name:</label>
                <textarea id="genericName" required="required" name="genericName"></textarea>  
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <div>
                <label for="price">Price:</label>
                <input type="text" required="required" id="price" name="price"/> 
            </div>
        </div>
        <div class="grid_6">
            <label for="category">Sales Tax:</label>
            <option value=""></option>
            <select id="tax" name="tax">
                <?php
                    $taxData = getTaxes();
                    while($tax = mysql_fetch_assoc($taxData)){
                ?>
                <option value="<?php echo $tax["code"]; ?>"><?php echo $tax["type"]; ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
        <div class="grid_6 omega">
            <label for="itemSupplier">Supplier:</label>
            <select id="itemSupplier">
                <?php
                    $supplierData = getSuppliers();
                    while($sup = mysql_fetch_assoc($supplierData)){
                ?>
                <option value="<?php echo $sup["code"]; ?>"><?php echo $sup["supplier_name"]; ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
        <div class="clear"></div>
        <div class="grid_18">
            <div class="submit">
                <input type="submit" name="addItem" value="Add Item"/> 
            </div>
        </div>
    </form>
</div>

