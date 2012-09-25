<?php
    include("../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["sku"])){
        $sku = mres($_POST["sku"]);
        $stockCount = mres($_POST["stockCount"]);
        $reorderMin = mres($_POST["reorderMin"]);
        $reorderLevel = mres($_POST["reorderLevel"]);
        $lastUpdate = date("Y-m-d h:i:s");
        updateInventoryCount($sku, $stockCount, $reorderMin, $reorderLevel, $lastUpdate);
    }
?>
