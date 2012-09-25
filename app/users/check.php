<?php
    include("../../library/config.php");
    
    $now = time();
    $flag = null;
    
    if($_SESSION['loginExpire'] < $now){
        $flag = "6f1c601a03a181f1e34d951ad606b5c49e99f02f";
    }
    else{
        $flag = "0343bb07c98f8a943e8eb80c0ba3d9758d372d22";
    }
    
    echo "{";
    echo '"e387d8b838964b1a98a7de5057fd738e560a1345":', json_encode($flag), "\n";
    echo "}";
?>
