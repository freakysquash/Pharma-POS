<?php
    include("../../../library/config.php");

    $image = getItemImage($_GET["s"]);
    $i = mysql_fetch_assoc($image);
    $content = $i["image"];
    header("Content-type: " . $i["mime_type"]);
    echo $content;
?>