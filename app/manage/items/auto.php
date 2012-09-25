<?php
    include("../../../library/config.php");
    authenticate();
    
    $itemData = suggestItemName($_GET["term"]);
	$items = array();
    while($i = mysql_fetch_array($itemData)) {
        $items[] = $i["description_1"];
    }
    echo json_encode($items);
?>