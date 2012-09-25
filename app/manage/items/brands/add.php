<?php
    include("../../../../library/config.php");
    
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    /* ADD BRAND */
    if(isset($_POST["manufacturer"])){
        $table = "brands";
        $column = "code";
        $code = getAvailableId($table, $column);
        $mbTable = "manufacturer_brand";
        $mbColumn = "code";
        $mbCode = getAvailableId($mbTable, $mbColumn);
        $manufacturer = mres($_POST["manufacturer"]);
        $brandName = mres(ucwords($_POST["brand"]));
        addBrand($code, $manufacturer, $brandName);
        newManufacturerBrandCode($mbCode, $manufacturer, $code);
    }
    /*----------------------------------------------------------*/
?>