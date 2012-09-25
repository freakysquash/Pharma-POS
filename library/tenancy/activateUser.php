<?php
    include("config.php");
    if(isset($_POST["ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14"])){
        $id = nextTenantUserId();
        $username = $_POST["ffcd1a28d8fa7c0c29b92ccd38c2d61015718a14"];
        $password = $_POST["b2c9f691be99ef8b4371cdc82725179378117e95"];
        newInternalAccount($_SESSION["tenant"], $_SESSION["secret"], $id, $username, $password);
    }
?>
