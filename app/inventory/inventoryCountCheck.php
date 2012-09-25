<?php
    include("../../library/config.php");
    authenticate();

    
    if(isset($_POST["sku"])){
        $inventoryData = getItemInventoryCount($_POST["sku"]);
        $inventory = mysql_fetch_row($inventoryData);
        echo json_encode($inventory);
    }
   
?>