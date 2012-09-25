<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    $brandData = getBrandByManufacturer($_GET["m"]);
    $brand = array();
    while($b = mysql_fetch_assoc($brandData)){
        $brand[] = $b;
    }
    
    echo json_encode($brand);
?>
