<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["department"])){
        $errors = null;
        $table = "departments";
        $column = "code";
        $code = getAvailableId($table, $column);
        $department = mres($_POST["department"]);
        if(empty($errors)){
            addDepartment($code, $department);
        }
    }
?>
