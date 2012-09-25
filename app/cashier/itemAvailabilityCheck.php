<?php
    include("../../library/config.php");
    if(checkItemAvailability($_GET["i"])){
        echo "available";
    }
    else {
        echo "out of stock";
    }
?>