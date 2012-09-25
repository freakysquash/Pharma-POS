<?php
    include("../../library/config.php");
    
    $_SESSION["userId"] = getUserId($_GET["b32c3765e5dce97e32597c99fcaee9af2f24c79b"], $_GET["249ba36000029bbe97499c03db5a9001f6b734ec"]);
    $_SESSION["store"] = getAssignedStore($_SESSION["userId"]);
    $_SESSION["register"] = getAssignedRegister($_SESSION["userId"]);
    $_SESSION['loginStart'] = time();
    $_SESSION['loginExpire'] = $_SESSION['loginStart'] + (60*60*24);

    if(isset($_GET["r"])){
        header("Location: " . $_GET["r"]);
    }
    else{
        header("Location: http://" . ROOT);
    }
?>