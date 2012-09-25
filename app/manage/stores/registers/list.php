<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    $(function(){
        $("#pos-table").dataTable({
            "sPaginationType": "full_numbers",
            "bFilter": false,
            "bLengthChange": false,
            "bJQueryUI": true
        });
    })
    
    $("#addRegisterLink").click(function(){
        $("#addRegisterDialog").dialog("open");
        $("#storeRegister").dialog("close");
    })
    
    $("#registerUserDialog").dialog({
        title: "Assign Users for this Register",
        autoOpen: false,
        modal: true,
        closeOnEscape: false,
        resizable:false,
        draggable:false,
        height:190,
        width:400
    })
    
    $(".register").click(function(){
        $("#registerUserDialog").dialog("open");
        $("#user").attr("data-store", $(this).attr("data-store"));
        $("#user").attr("data-register", $(this).attr("data-register"));
        $("#user").autocomplete({
            source: "/app/users/list.php",
            width:300,
            minLength: 3
        })
    })
    
    $("#assignUsersForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var user = $("input[name=user]").val();
        var store = $("input[name=user]").attr("data-store");
        var register = $("input[name=user]").attr("data-register");
        $.ajax({
            type: "POST",
            url: "/app/manage/stores/registers/assign.php",
            data: { user: user, store: store, register: register },
            success: function(){
                $("input[name=user]").val("");
                $("#registerUserDialog").dialog("close");
                $("#addRegisterDialog").dialog("close");
            }
        })
    })
    
</script>
<button id="addRegisterLink">Add Register</button><br/><br/>

<div id="existingStoreRegisters">
<p>Available Registers</p><br/>
<table id="pos-table">
    <thead>
        <tr>
            <td>Code</td>
            <td>Description</td>
        </tr>
    </thead>
    <tbody>
<?php
    $regData = getRegistersByStore($_GET["s"]);
    while($r = mysql_fetch_assoc($regData)){
?>
        <tr>
            <td><?php echo $r["code"]; ?></td>
            <td><a href="#" data-store="<?php echo $_GET["s"]; ?>" data-register="<?php echo $r["code"]; ?>" class="register" style="color:#0f82db;"><?php echo $r["description"]; ?></a></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>
</div>

<div id="dialogs">
    <div id="registerUserDialog" class="ui-dialog-form">
        <form id="assignUsersForm">
            <div>
                <label>Select User:</label>
                <input type="text" name="user" data-store="" data-register="" id="user"/>
            </div>
            <div>
                <input type="submit" value="Add this User"/>
            </div>
        </form>
    </div>
</div>
