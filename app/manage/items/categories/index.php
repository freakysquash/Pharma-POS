<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>

    $("#addCategoryDialog").dialog({
        title: "Add Category",
        autoOpen:false,
        resizable:false,
        draggable:false,
        closeOnEscape:true,
        modal:true,
        height:300,
        width:400,
        buttons:false
    })
    
    $("#addCategory").click(function(){
        $("#addCategoryDialog").dialog("open");
    })

    $("#addCategoryForm").unbind("submit").submit(function(e){
        e.preventDefault();
        $("input[name=addCategory]").attr("disabled", "disabled");
        var department = $("#department").val();
        var category = $("input[name=category]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/categories/add.php",
            data: {department: department, category: category},
            success: function(){
                $.uinotify({
	                'text'		: 'New Category Added',
	                'duration'	: 3000
                });
                $("input[name=category]").val("");
                $("#addCategoryDialog").dialog("close");
                $("input[name=addCategory]").removeAttr("disabled");
                $("#categories").trigger("click");
            }
        })
    })
    
    $("#categories-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="addCategory">Add</a></li>
    </ul>
</div>

<table id="categories-table">
    <thead>
        <tr>
            <td>Department</td>
            <td>Category</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $categories = getCategories();
            while($c = mysql_fetch_assoc($categories)){
        ?>
        <tr>
            <td><?php echo getDepartmentNameByCode($c["department_code"]); ?></td>
            <td><?php echo $c["category_name"]; ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>


<div class="ui-dialog-form" id="addCategoryDialog">
    <form method="post" action="" id="addCategoryForm">
        <div>
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
        </div>
        <div>
            <div>
                <label for="category">Category:</label>
                <input type="text" id="category" required="required" name="category" value=""/>
            </div>
        <div>
            <div class="submit">
                <input type="submit" name="addCategory" value="Add Category"/>
            </div>
        </div>
    </form>
</div>
