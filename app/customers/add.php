<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["customerName"])){
        $code = getAvailableCustomerCode();
        $customerName = mres($_POST["customerName"]);
        $address = mres($_POST["address"]);
        $email = mres($_POST["email"]);
        $contactNo = mres($_POST["contactNo"]);
        newCustomer($code, $customerName, $address, $email, $contactNo);
    }
    else{
        echo "Why are you here?!";
    }
?>