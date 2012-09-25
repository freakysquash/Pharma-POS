<?php
    include("../../library/config.php");
    include("../header.php");
    authenticate();
    list($usersUsername, $usersFirstname, $usersLastname, $usersEmailAddress, $usersContactNo, $usersAddress1, $usersAddress2, $usersCity, $usersProvince, $usersCountry, $usersPostalCode) = getUserDataById($_SESSION["userId"]);
    
    if(isset($_POST["updateAccount"])){
        $errors = null;
        $firstname = mres(ucwords($_POST["firstname"]));
        if(empty($firstname)){
            $errors .= "<li>Firstname is required</li>";
        }
        $lastname = mres(ucwords($_POST["lastname"]));
        if(empty($lastname)){
            $errors .= "<li>Lastname is required</li>";
        }
        $emailAddress = mres($_POST["emailAddress"]);
        if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
            $errors .= "<li>Invalid email address</li>";
        }
        $contactNo = mres($_POST["contactNo"]);
        $address1 = mres(ucwords($_POST["address1"]));
        $address2 = mres(ucwords($_POST["address2"]));
        $city = mres($_POST["city"]);
        $province = mres($_POST["province"]);
        $country = mres($_POST["country"]);
        $postalCode = mres($_POST["postalCode"]);
        $dateUpdated = date("Y-m-d h:m:s");
        $updatedBy = $usersUsername;
        if(empty($errors)){
           updateAccount($_SESSION["userId"], $firstname, $lastname, $emailAddress, $contactNo, $address1, $address2, $city, $province, $country, $postalCode, $dateUpdated, $updatedBy);
           header("Location: /");
        }
        else{
            echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
        }
    }
?>

    <script type="text/javascript">
        $(function() {
            $( ".error-dialog" ).dialog({
                title: "Form Submission Error",
                minHeight: 120,
                modal: true,
                closeOnEscape: true,
                resizable: false,
                buttons: {
                    Close: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });
	</script>
    <div class="ui-form col_12">
        <form method="post" action="">
            <div class="col_4 clearleft">
                <div>
                    <label for="firstname">Firstname:</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo $usersFirstname; ?>"/>
                </div>
                <div>
                    <label for="lastname">Lastname:</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo $usersLastname ?>"/>
                </div>
                <div>
                    <label for="emailAddress">Email address:</label>
                    <input type="text" id="emailAddress" name="emailAddress" value="<?php echo $usersEmailAddress ?>"/>
                </div>
                <div>
                    <label for="contactNo">Contact no:</label>
                    <input type="text" id="contactNo" name="contactNo" value="<?php echo $usersContactNo ?>"/>
                </div>
                <div>
                    <label for="address1">Address 1:</label>
                    <input type="text" id="address1" name="address1" value="<?php echo $usersAddress1 ?>"/>
                </div>
            </div>
            <div class="col_4 last">
                <div>
                    <label for="address2">Address 2:</label>
                    <input type="text" id="address2" name="address2" value="<?php echo $usersAddress2 ?>"/>
                </div>
                <div>
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo $usersCity ?>"/>
                </div>
                 <div>
                    <label for="province">Province:</label>
                    <input type="text" id="province" name="province" value="<?php echo $usersProvince ?>"/>
                </div>
                 <div>
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" value="<?php echo $usersCountry ?>"/>
                </div>
                 <div>
                    <label for="postalCode">Postal code:</label>
                    <input type="text" id="postalCode" name="postalCode" value="<?php echo $usersPostalCode ?>"/>
                </div>
                 <div class="submit">
                    <input class="ui-button-elem ui-button-elem-blue" type="submit" name="updateAccount" value="Save"/>
                </div>
            </div>
        </form>
    </div>
<?php
    include("../footer.php");
?>