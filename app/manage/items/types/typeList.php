<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    $typeData = getTypeByBrand($_GET["b"]);
    $type = array();
    while($t = mysql_fetch_assoc($typeData)){
        $type[] = $t;
    }
    
    echo json_encode($type);
?>

