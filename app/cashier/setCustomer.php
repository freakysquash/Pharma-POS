<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_POST["customer"])){
        $transNo = mres($_POST["transaction"]);
        $customer = mres($_POST["customer"]);
        trackCustomerTransaction($transNo, $customer);
    }
?>
