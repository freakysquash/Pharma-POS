<?php
    include("../../library/config.php");
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
?>

<script>
    $("#newUserForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var username = $("input[name=username]").val();
        var password = $("input[name=password]").val();
        var firstname = $("input[name=firstname]").val();
        var lastname = $("input[name=lastname]").val();
        var emailAddress = $("input[name=emailAddress]").val();
        var contactNo = $("input[name=contactNo]").val();
        var address1 = $("input[name=address1]").val();
        var address2 = $("input[name=address2]").val();
        var city = $("input[name=city]").val();
        var province = $("input[name=province]").val();
        var country = $("input[name=country]").val();
        var postalCode = $("input[name=postalCode]").val();
        $.ajax({
            type: "POST",
            url: "/app/users/register.php",
            data: { username:username, password:password, firstname:firstname, lastname:lastname, emailAddress:emailAddress, contactNo:contactNo, address1:address1, address2:address2, city:city, province:province, country:country, postalCode:postalCode },
            success: function(){
                $.uinotify({
                    "text": "User registered",
                    "duration": 2000
                });
                setTimeout(function () {
                    window.location.href = "/?module=manage&page=users"
                }, 2000);
            }
        })
    })
</script>

<div class="custom-form">
    <form  id="newUserForm">
        <div class="grid_6 alpha">
            <div class="grid_6 alpha">
                <label for="username">Username:</label>
                <input type="text" id="username"required="required" name="username" value="<?php if(isset($_POST["username"])){ echo $_POST["username"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 alpha">
                <label for="password">Password:</label>
                <input type="password" id="password"required="required" name="password"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 alpha">
                <label for="firstname">Firstname:</label>
                <input type="text" id="firstname"required="required" name="firstname" value="<?php if(isset($_POST["firstname"])){ echo $_POST["firstname"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 alpha">
                <label for="lastname">Lastname:</label>
                <input type="text" id="lastname"required="required" name="lastname" value="<?php if(isset($_POST["lastname"])){ echo $_POST["lastname"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 alpha">
                <label for="emailAddress">Email address:</label>
                <input type="text" id="emailAddress"required="required" name="emailAddress" value="<?php if(isset($_POST["emailAddress"])){ echo $_POST["emailAddress"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 alpha">
                <label for="contactNo">Contact no:</label>
                <input type="text" id="contactNo"required="required" name="contactNo" value="<?php if(isset($_POST["contactNo"])){ echo $_POST["contactNo"]; } ?>"/>
            </div>
        </div>
        <div class="grid_6 omega">
            <div class="grid_6">
                <label for="address1">Address 1:</label>
                <input type="text" id="address1"required="required" name="address1" value="<?php if(isset($_POST["address1"])){ echo $_POST["address1"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 omega">
                <label for="address2">Address 2:</label>
                <input type="text" id="address2"required="required" name="address2" value="<?php if(isset($_POST["address2"])){ echo $_POST["address2"]; } ?>"/>
            </div>
            <div class="clear"></div>
             <div class="grid_6 omega">
                <label for="city">City:</label>
                <input type="text" id="city"required="required" name="city" value="<?php if(isset($_POST["city"])){ echo $_POST["city"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 omega">
                <label for="province">Province:</label>
                <input type="text" id="province"required="required" name="province" value="<?php if(isset($_POST["province"])){ echo $_POST["province"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 omega">
                <label for="country">Country:</label>
                <input type="text" id="country"required="required" name="country" value="<?php if(isset($_POST["country"])){ echo $_POST["country"]; } ?>"/>
            </div>
            <div class="clear"></div>
            <div class="grid_6 omega">
                <label for="postalCode">Postal code:</label>
                <input type="text" id="postalCode"required="required" name="postalCode" value="<?php if(isset($_POST["postalCode"])){ echo $_POST["postalCode"]; } ?>"/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_12">
            <input class="x-button" type="submit"required="required" name="registerAccount" value="Register"/>
        </div>
    </form>
</div>