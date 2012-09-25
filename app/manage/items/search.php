<?php
    include("../../../library/config.php");
    $itemData = searchItem($_POST["sku"], $_POST["description"]);
    $item = mysql_fetch_assoc($itemData);
    echo json_encode($item);
?>
