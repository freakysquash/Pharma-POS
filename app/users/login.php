    <?php
    include("../../library/tenancy/config.php");
    
    if(isset($_SESSION["userId"])){
        header("Location: http://" . ROOT);
    }
    else{
    if(isset($_POST["loginAccount"])){
        $username = mysql_real_escape_string($_POST["username"]);
        $password = sha1($_POST["password"]);
        $checkUser = loginTenantAccount($username, $password);
        $user = mysql_fetch_assoc($checkUser);
        if(mysql_num_rows($checkUser) == 1){
            $_SESSION["username"] = $user["username"];
            $_SESSION["tenant"] = $user["tenant_code"];
            $_SESSION["secret"] = $user["secret_key"];
            $_SESSION["db"] = $user["tenant_code"];
            if(isset($_GET["r"])){
                header("Location: auth.php?r=" . $_GET["r"] . "&b32c3765e5dce97e32597c99fcaee9af2f24c79b=" . $user["tenant_code"] . "&249ba36000029bbe97499c03db5a9001f6b734ec=" . $user["username"]);
            }
            else{
                header("Location: auth.php?b32c3765e5dce97e32597c99fcaee9af2f24c79b=" . $user["tenant_code"] . "&249ba36000029bbe97499c03db5a9001f6b734ec=" . $user["username"]);
            }
        }
        else {
            header("Location: login.php");
        }

    }
    include("../../template/header.php");
?>

<script>
    $(document).ready(function(){
        $(".content-block").css({"box-shadow":"none", "border":"none", "background":"none"});
    })
</script>

<div id="login" class="grid_20 push_2 alpha">
    <div class="grid_18 push_1 alpha" id="greet">
        <span>Welcome</span>
    </div>
    <div class="clear"></div>
    <div class="grid_10 push_1 alpha" id="features">
        <div id="promote">myPharmaPOS is the simplest way to avail a smarter Pharmaceutical Point of Sale solution.</div>
        <div class="clear"></div>
        <div class="stats">
            <div class="grid_2 alpha"><img src="http://<?php echo ROOT; ?>/template/images/browser.png"/></div>
            <div class="grid_8 omega">
                <span class="stat-title">Web based</span>
                <p>Donec ut est mauris. Aliquam erat volutpat. Ut ligula erat, dictum vel pulvinar porta, aliquam sed felis.</p>
            </div>
        </div>
        <div class="stats">
            <div class="grid_2 alpha"><img src="http://<?php echo ROOT; ?>/template/images/browser.png"/></div>
            <div class="grid_8 omega">
                <span class="stat-title">Web based</span>
                <p>Donec ut est mauris. Aliquam erat volutpat. Ut ligula erat, dictum vel pulvinar porta, aliquam sed felis.</p>
            </div>
        </div>
        <div class="stats">
            <div class="grid_2 alpha"><img src="http://<?php echo ROOT; ?>/template/images/browser.png"/></div>
            <div class="grid_8 omega">
                <span class="stat-title">Web based</span>
                <p>Donec ut est mauris. Aliquam erat volutpat. Ut ligula erat, dictum vel pulvinar porta, aliquam sed felis.</p>
            </div>
        </div>
    </div>
    <div class="grid_8 push_1 omega" id="login-form">
        <form method="post">
            <span>Login Account</span>
            <div>
                <label>Username</label>
                <input type="text" id="username" required="required" name="username"/>
            </div>
            <div>
                <label>Password</label>
                <input type="password" id="password" required="required" name="password"/>
            </div>
            <div id="submit">
                <input type="hidden" name="returnUrl" value="<?php if(!empty($_GET["r"])){ echo $_GET["r"]; }?>"/>
                <input type="submit" name="loginAccount" value="Login"/>
            </div>
        </form>
    </div>
</div>
<div class="ui-widget-overlay" style="z-index:1"></div>
<?php
    }
    include("../../template/footer.php");
?>