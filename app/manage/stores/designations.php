<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<p>designations</p>