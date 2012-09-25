<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["user"])){
        $store = mres($_POST["store"]);
        $register = mres($_POST["register"]);
        $username = mres($_POST["user"]);
        $user = getUserIdByUsername($username);
        $lastUpdated = date("Y-m-d h:i:s");
        assignUserToRegister($store, $register, $user, $lastUpdated);
    }
?>
