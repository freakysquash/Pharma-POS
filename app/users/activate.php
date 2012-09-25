<?php
    include("../../library/config.php");
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["user"])){
        $user = mres($_POST["user"]);
        $group = $_POST["group"];
        assignToGroup($group, $user);
        activateUser($user);
    }

?>
