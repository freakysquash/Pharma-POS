<?php
    include("../../library/config.php");

    echo "{";
    echo '"sku":', json_encode(getlastItemEntry($_GET["t"])), "\n";
    echo "}";

?>