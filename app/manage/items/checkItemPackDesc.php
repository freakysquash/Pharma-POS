<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    if(isset($_GET["p"])&& isset($_GET["d"])){
        $result = checkItemPackDescExistence($_GET["p"], $_GET["d"]);
        if($result == 1){
            echo "<div class='ui-state-error' style='padding:10px;width:685px !important;'><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>The same description was already assigned to an existing product!</div>";
            echo "<input type='hidden' name='exist' value='1'/>";
        }
    }
?>

<script>
    if($("input[name=exist]").val() == "1"){
        $("input[name=addItem]").attr("disabled", true);
        $("input[name=addItem]").val("Adding Item Disabled");
    }
    else{
        $("input[name=addItem]").removeAttr("disabled");
        $("input[name=addItem]").val("Add Item");
    }
</script>