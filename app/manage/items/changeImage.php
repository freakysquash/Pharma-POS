<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["imageSku"])){
        $sku = mres($_POST["imageSku"]);
        $filename = $_FILES["image"]["name"];
        $mimeType = $_FILES["image"]["type"];
        $size = $_FILES["image"]["size"];

        $image = file_get_contents($_FILES["image"]["tmp_name"]);
        $image = addslashes($image);

        if(!get_magic_quotes_gpc()){
            $filename = addslashes($filename);
        }

        changeItemImage($sku, $filename, $mimeType, $size, $image);
        header("Location: http://" . ROOT . "?module=manage&page=itemList");
    }
?>
