<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);

    /* ADD CATEGORY */
    if(isset($_POST["department"])){
        $table = "categories";
        $column = "code";
        $code = getAvailableId($table, $column);
        $departmentCode = mres($_POST["department"]);
        $categoryName = mres(ucwords($_POST["category"]));
        addCategory($code, $departmentCode, $categoryName);
    }
    /*----------------------------------------------------------*/
?>
