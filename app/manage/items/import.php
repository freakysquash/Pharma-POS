<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);  
?>

<div class="custom-form">
    <form method="post" action="app/manage/items/input.php" enctype="multipart/form-data">
        <div class="grid_6 alpha">
            <label>Select CSV File:</label>
            <input type="file" id="itemCsv" name="itemCsv"/>
            <br/><br/>
        </div>
        <div class="clear"></div>
        <div class="grid_5 alpha">
            <input type="submit" class="x-button" value="Import Item List"/>
        </div>
    </form>
</div>