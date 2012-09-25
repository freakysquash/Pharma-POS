<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    /* ADD TYPE */
    if(isset($_POST["brand"])){
        $errors = null;
        $table = "types";
        $column = "code";
        $code = getAvailableId($table, $column);
        $brand = mres($_POST["brand"]);
        $type = mres(ucwords($_POST["type"]));
        if(empty($errors)){
            addType($code, $brand, $type);
        }
        else {
            echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
        }
    }
    /*----------------------------------------------------------*/
?>

<script type="text/javascript">

    $("#category-list").load("/app/manage/items/categories/categoryList.php");
    $("#department").change(function(){
        var c = $("#department").val();
        $("#category-list").load("/app/manage/items/categories/categoryList.php?c=" + c);
    });
    
    $("#addTypeForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var brand = $("#brand").val();
        var type = $("input[name=type]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/types/add.php",
            data: {brand: brand, type: type},
            success: function(){
                $.uinotify({
	                'text'		: 'New Type Added',
	                'duration'	: 3000
                });
                $("input[name=type]").val("");
            }
        })
    })
</script>


<div class="custom-form">
    <form method="post" action="" id="addTypeForm">
        <div class="grid_8 alpha">
            <div>
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
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <div>
                <label for="type">Type:</label>
                <input type="text" required="required" id="type" name="type"/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9">
            <div class="submit">
                <input type="submit" name="addType" value="Add Type"/>
            </div>
        </div>
    </form>
</div>

