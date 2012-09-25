<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    /* ADD MANUFACTURER */
    if(isset($_POST["manufacturer"])){
        $errors = null;
        $table = "manufacturers";
        $column = "code";
        $code = getAvailableId($table, $column);
        $manufacturerName = mres(ucwords($_POST["manufacturer"]));
        if(empty($errors)){
            addManufacturer($code, $manufacturerName);
        }
        else {
            echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
        }
    }
    /*----------------------------------------------------------*/
?>
