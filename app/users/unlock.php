<?php
    include("../../library/config.php");
    
    if(isset($_POST["f30552a9730deb17649759e307e336c6"])){
        $username = $_POST["f30552a9730deb17649759e307e336c6"];
        $password = $_POST["d40d04ce5922305fc7a1f3208fb37f82"];
        $user = loginAccount($username, $password);
        $u = mysql_fetch_assoc($user);
        if(mysql_num_rows($user) == 1 && $_SESSION["username"] == $username){
            $currentUser = $u["username"];
            unset($_SESSION["f5ebd1bab16ff5845411f18788c2ca1e"]);
        }
        else{
            $currentUser = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
        }
        echo "{";
        echo '"bc268ffea25b473196a0833c90a0085f":', json_encode(sha1($currentUser)), "\n";
        echo "}";
    }
?>
