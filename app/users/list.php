<?php
    include("../../library/config.php");
    authenticate();

    $userData = suggestUser($_GET["term"]);
	$users = array();
    while($u = mysql_fetch_array($userData)) {
        $users[] = $u["username"];
    }
    echo json_encode($users);
?>
