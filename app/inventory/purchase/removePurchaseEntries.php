<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["purchaseNo"])){
        removeAllPOEntries($_POST["purchaseNo"]);
    }
?>
