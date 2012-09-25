<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_GET["s"])){
        echo "{";
        echo '"reorder":', json_encode(getReorderLevelBySku($_GET["s"])), "\n";
        echo "}";
    }
?>
