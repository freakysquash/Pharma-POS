<?php
    include("../../library/config.php");
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    $(".toolbar-menu").click(function(){
        var menu = $(this).attr("data-action");
        
        switch(menu){
            case "new":
               $(".users-workspace").load("/app/users/" + menu + ".php");
               break;
        }
    })
    
    $("#usersTable").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
    
    $("#activateUserDialog").dialog({
        title: "Activate User",
        autoOpen:false,
        modal:true,
        resizable:false,
        draggable:false,
        closeOnEscape:true,
        height:190,
        width:300
    })
    
    $(".activate").click(function(){
        $("input[name=userId]").val($(this).attr("data-user"));
        $("input[name=ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14]").val($(this).attr("data-ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14"));
        $("input[name=b2c9f691be99ef8b4371cdc82725179378117e95]").val($(this).attr("data-b2c9f691be99ef8b4371cdc82725179378117e95"));
        
        $("#activateUserDialog").dialog("open");
    })
    
    $("#activateUserForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var user = $("input[name=userId]").val();
        var group = $("#group").val();
        var ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14 = $("input[name=ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14]").val();
        var b2c9f691be99ef8b4371cdc82725179378117e95 = $("input[name=b2c9f691be99ef8b4371cdc82725179378117e95]").val();
        
        $.ajax({
            type: "POST",
            url: "/app/users/activate.php",
            data: { user:user, group:group },
            success: function(){
                
            }
        })
        $.ajax({
            type: "POST",
            url: "/library/tenancy/activateUser.php",
            data: { user:user, ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14:ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14, b2c9f691be99ef8b4371cdc82725179378117e95:b2c9f691be99ef8b4371cdc82725179378117e95 },
            success: function(){
                $("#activateUserDialog").dialog("close");
                $.uinotify({
                    "text": "User activated",
                    "duration": 2000
                });
                setTimeout(function () {
                    window.location.href = "/?module=manage&page=users"
                }, 2000);
            }
        })
    })
</script>

<div class="x-toolbar">
    <ul>
        
        <li><a href="#" class="toolbar-menu" data-action="new">New User</a></li>
    </ul>
</div>

<div class="users-workspace">
    <table id="usersTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Date Created</th>
                <th>&nbsp;</th>
            </tr>
        </thead> 
        <tbody>
            <?php
                $users = getUsers();
                while($u = mysql_fetch_assoc($users)){
            ?>
            <tr>
                <td><?php echo $u["username"]; ?></td>
                <td><?php echo $u["firstname"]; ?></td>
                <td><?php echo $u["lastname"]; ?></td>
                <td><?php echo date("Y-m-d", strtotime($u["date_created"])); ?></td>
                <td><?php if($u["tenant_auth_active"] == 0){?> <a href="#" class="activate" id="activate" data-user="<?php echo $u["user_id"]; ?>" data-ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14="<?php echo $u["username"]; ?>" data-b2c9f691be99ef8b4371cdc82725179378117e95="<?php echo $u["password"]; ?>">Activate</a> <?php } ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

<div id="dialogs">
    <div id="activateUserDialog" class="ui-dialog-form">
        <form id="activateUserForm">
            <input type="hidden" name="userId"/>
            <input type="hidden" name="ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14"/>
            <input type="hidden" name="b2c9f691be99ef8b4371cdc82725179378117e95"/>
            <div>
                <label>Access Level:</label>
                <select id="group">
                    <?php
                        $levels = getUserGroups();
                        while($l = mysql_fetch_assoc($levels)){
                    ?>
                    <option value="<?php echo $l["id"]; ?>"><?php echo $l["group_name"]; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <div>
                <input type="submit" value="Activate"/>
            </div>
        </form>
    </div>
</div>