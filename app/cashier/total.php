<?php
    include("../../library/config.php");

    echo "{";
    echo '"total":', json_encode(sprintf("%.2f", getTransactionTotalAmount($_GET["t"]))), "\n";
    echo "}";

?>