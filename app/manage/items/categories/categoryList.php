<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    $categoryData = getCategoryByDepartment($_GET["d"]);
    $category = array();
    while($c = mysql_fetch_assoc($categoryData)){
        $category[] = $c;
    }
    
    echo json_encode($category);
?>
