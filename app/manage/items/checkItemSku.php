<?php
    include("../../../library/config.php");
    authenticate();
    
    echo "{";
    echo '"sku":', json_encode(getSkuByDescription($_GET["d"]));
    echo "}";

?>