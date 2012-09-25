<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["branch"])){
        $table = "stores";
        $column = "code";
        $code = getAvailableId($table, $column);
        $branch = mres(ucwords($_POST["branch"]));
        $address1 = mres($_POST["address1"]);
        $address2 = mres($_POST["address2"]);
        addStore($code, $branch, $address1, $address2);
    }
    
    if(isset($_POST["registerStore"])){
        $table = "registers";
        $column = "code";
        $code = getAvailableId($table, $column);
        $storeCode = mres($_POST["registerStore"]);
        $registerDescription = mres($_POST["registerDescription"]);
        addStoreRegister($code, $storeCode, $registerDescription);
    }
?>

<script>
    
    $("#designationsTable").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
    
    $("#designationsDialog").dialog({
        title: "Store Designations",
        autoOpen: false,
        draggable: false,
        resizable:false,
        modal:true,
        minHeight:300,
        width:500,
        closeOnEscape: true,
        buttons: false
    })
    
    $("#designations").click(function(){
        $("#designationsDialog").dialog("open");
    })
    
    $("#stores-table").dataTable({
        "sPaginationType": "full_numbers",
        "bJQueryUI": true
    });
    
    $("#addStoreDialog").dialog({
        autoOpen: false,
        title: "Add Store",
        modal: true,
        resizable: false,
        draggable: false,
        closeOnEscape: true,
        width:350,
        height:335,
        buttons: false
    })
    
    $("#addStore").click(function(){
        $("#addStoreDialog").dialog("open");
    })
    
    $("#addStoreForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var branch = $("input[name=branch]").val();
        var address1 = $("input[name=address1]").val();
        var address2 = $("input[name=address2]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/stores/index.php",
            data: { branch: branch, address1: address1, address2: address2 },
            success: function(){
                $("input[name=branch]").val("");
                $("input[name=address1]").val("");
                $("input[name=address2]").val("");
                $("#addStoreDialog").dialog("close");
                window.location.href = '?module=manage&page=store';
            }
        })
    })
    
    $("#storeRegister").dialog({
        title: "Store Registers",
        autoOpen: false,
        draggable: false,
        resizable:false,
        modal:true,
        minHeight:300,
        width:400,
        closeOnEscape: true,
        buttons: false
    })
    
    $(".store").click(function(){
        $("#storeRegister").load("/app/manage/stores/registers/list.php?s=" + $(this).attr("data-store") + "#existingStoreRegisters");
        $("#storeRegister").dialog("open");
    })
    
    $("#existingStoreRegisters").hide();
    
    $("#addRegisterDialog").dialog({
        title: "Store Registers",
        autoOpen: false,
        draggable: false,
        resizable:false,
        modal:true,
        minHeight:300,
        width:400,
        closeOnEscape: true,
        buttons: false
    })
    
    $("#addRegister").click(function(){
        $("#addRegisterDialog").dialog("open");
    })
    
    $("#storeCode").bind("change click", function(){
        $("#existingStoreRegisters").load("/app/manage/stores/registers/list.php?s=" + $("#storeCode").val())
        $("#existingStoreRegisters").show();
    })
    
    $("#addRegisterForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var registerStore = $("#storeCode").val();
        var registerDescription = $("input[name=registerDescription]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/stores/index.php",
            data: { registerStore: registerStore, registerDescription: registerDescription },
            success: function(){
                $("input[name=registerDescription]").val("");
                $("#addRegisterDialog").dialog("close");
            }
        })
    })
    
</script>

<div class="x-toolbar">
    <ul>
        <li><a href="#" id="addStore">Add Store</a></li>
        <li><a href="#" id="addRegister">Add Register</a></li>
        <li><a href="#" id="designations">Store Designations</a></li>
    </ul>
</div>

<table id="stores-table">
    <thead>
        <tr>
            <td>Code</td>
            <td>Branch name</td>
            <td>Address</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $storeData = getStores();
            while($s = mysql_fetch_assoc($storeData)){
        ?>
        <tr>
            <td><?php echo $s["code"]; ?></td>
            <td><a href="#" class="store" data-store="<?php echo $s["code"]; ?>"><?php echo $s["branch"]; ?></a></td>
            <td><?php echo $s["address_1"] . ", " . $s["address_2"];?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>

<div id="dialogs">
    <div id="addStoreDialog" class="ui-dialog-form">
        <form id="addStoreForm">
            <div>
                <label>Branch:</label>
                <input type="text" id="branch" name="branch"/>
            </div>
            <div>
                <label>Address 1</label>
                <input type="text" id="address1" name="address1"/>
            </div>
            <div>
                <label>Address 2</label>
                <input type="text" id=address2"" name="address2"/>
            </div>
            <div class="submit">
                <input type="submit" value="Add Store"/>
            </div>
        </form>
    </div>
    
    <div id="storeRegister" class="ui-dialog-form">
        STORE Registers
    </div>
    
    <div id="addRegisterDialog" class="ui-dialog-form">
        <form id="addRegisterForm">
            <div>
                <label>Select Branch:</label>
                <select id="storeCode">
                    <?php
                        $store = getStores();
                        while($st = mysql_fetch_assoc($store)){
                    ?>
                    <option value="<?php echo $st["code"]; ?>"><?php echo $st["branch"]; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <div>
                <label>Description:</label>
                <input type="text" id="registerDescription" name="registerDescription"/>
            </div>
            <div>
                <input type="submit" name="addRegister" value="Add Register"/>
            </div>
        </form>
        <div id="existingStoreRegisters">
            
        </div>
    </div>
    
    <div id="designationsDialog">
        <table id="designationsTable">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Register</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $storeList = storeDesignations();
                    while($sl = mysql_fetch_assoc($storeList)){
                ?>
                <tr>
                    <td><?php echo getStoreName($sl["store_code"]); ?></td>
                    <td><?php echo getRegisterDescription($sl["store_code"], $sl["register_code"]); ?></td>
                    <td><?php echo getUserCompleteName($sl["user_code"]); ?></td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
    
</div>

