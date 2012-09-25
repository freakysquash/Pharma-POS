<?php
    include("../../library/config.php");

    echo "{";
    echo '"subtotal":', json_encode(sprintf("%.2f", getTransactionSubtotal($_GET["t"]))), "\n";
    echo "}";

?>