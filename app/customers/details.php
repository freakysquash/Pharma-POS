<?php
    include("../../library/config.php");
    authenticate();
    
    if(isset($_GET["c"])){
        $code = mres($_GET["c"]);
        $customer = getCustomerDetails($code);
        $c = mysql_fetch_assoc($customer);
        
        $details = array("code" => $c["code"],"name" => $c["customer_name"],"address" => $c["address"],"email" => $c["email_address"],"contact" => $c["contact_no"]);
        echo json_encode($details);
    }
?>
