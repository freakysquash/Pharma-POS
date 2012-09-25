<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    $itemData = getItemsBySupplier($_GET["s"]);
    $item = array();
    while($i = mysql_fetch_assoc($itemData)){
        $item[] = $i;
    }
    
    echo json_encode($item);
?>
