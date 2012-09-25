<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_GET["i"])){
        echo "{";
        echo '"entryTax":', json_encode(checkEntryTax($_GET["i"])), "\n";
        echo "}";
    }
?>