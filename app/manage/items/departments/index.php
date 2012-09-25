<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    
    $("#addDepartmentDialog").dialog({
        title: "Add Department",
        autoOpen: false,
        draggable: false,
        resizable:false,
        closeOnEscape: true,
        modal:true,
        width:400,
        height:300,
        buttons: false
    })
    
    $("#addDepartment").click(function(){
        $("#addDepartmentDialog").dialog("open");
    })
    
    
    $("#addDepartmentForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var department = $("input[name=department]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/departments/add.php",
            data: {department: department},
            success: function(){
                $.uinotify({
	                'text'		: 'New Department Added',
	                'duration'	: 3000
                });
                $("input[name=department]").val("");
                $("#addDepartmentDialog").dialog("close");
                $("#departments").trigger("click");
            }
        })
    })
    
    $("#departments-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="addDepartment">Add</a></li>
    </ul>
</div>

<table id="departments-table">
    <thead>
        <tr>
            <td>Department</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $departments = getDepartments();
            while($d = mysql_fetch_assoc($departments)){
        ?>
        <tr>
            <td><?php echo $d["department_name"]; ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<div class="ui-dialog-form" id="addDepartmentDialog">
    <form method="post" action="" id="addDepartmentForm">

        <div class="grid_8 alpha">
            <div>
                <label for="department">Department name:</label>
                <input type="text" id="department" required="required" name="department" value=""/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9 omega">
            <div class="submit">
                <input type="submit" name="addDepartment" value="Add Department"/>
            </div>
        </div>

    </form>
</div>
