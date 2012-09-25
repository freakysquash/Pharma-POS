<?php
    include("../../library/config.php");
    
    if(isset($_GET["s"])){
        $taxData = applyTaxAmount($_GET["s"]);
        $tax = mysql_fetch_row($taxData);
        echo json_encode($tax);
    }
?>