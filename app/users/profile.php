<?php
    include("../library/config.php");
    include("../header.php");
    authenticate();
    list($usersUsername, $usersFirstname, $usersLastname, $usersEmailAddress, $usersContactNo, $usersAddress1, $usersAddress2, $usersCity, $usersProvince, $usersCountry, $usersPostalCode) = getUserDataById($_SESSION["userId"]);
?>
<h3>Hello <?php echo $usersUsername; ?>!</h3>
<a class="chiclet" href="../users/edit.php">Edit Personal Information</a>
<?php
    include("../footer.php");
?>
