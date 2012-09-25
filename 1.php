<?php
    include("library/config.php");
    
    echo "1." . str_replace(".", "", getTaxRate(getTaxCodeBySku(getSkuByCashierEntryId("5"))));
?>