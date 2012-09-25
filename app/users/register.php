<?php
    include("../../library/config.php");
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    

    $errors = null;

    /* GET AVAILABLE USER ID */
    $table = "user_accounts";
    $column = "user_id";
    $userId = getAvailableId($table, $column);
    /******************************************/
    $username = mres($_POST["username"]);
    if(empty($username)){
        $errors .= "<li>Username is required</li>";
    }
    if(empty($_POST["password"])){
        $errors .= "<li>Password is required</li>";
    }
    $password = sha1($_POST["password"]);

    $firstname = mres(ucwords($_POST["firstname"]));
    $lastname = mres(ucwords($_POST["lastname"]));
    $emailAddress = mres($_POST["emailAddress"]);
    if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
        $errors .= "<li>Invalid email address</li>";
    }
    $contactNo = mres($_POST["contactNo"]);
    $address1 = mres(ucwords($_POST["address1"]));
    $address2 = mres(ucwords($_POST["address2"]));
    $city = mres($_POST["city"]);
    $province = mres($_POST["province"]);
    $country = mres($_POST["country"]);
    $postalCode = mres($_POST["postalCode"]);
    if(empty($errors)){
        registerAccount($_SESSION["tenant"], $_SESSION["secret"], $userId, $username, $password, $firstname, $lastname, $emailAddress, $contactNo, $address1, $address2, $city, $province, $country, $postalCode);
    }
    else{
        echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
    }
?>
