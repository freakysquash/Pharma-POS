<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    /* ADD MEASUREMENT */
    if(isset($_POST["packaging"])){
        $table = "packagings";
        $column = "code";
        $code = getAvailableId($table, $column);
        $packaging = ucwords($_POST["packaging"]);
        $quantity = mres($_POST["quantity"]);
        $description = mres(ucfirst($_POST["description"]));
        addPackaging($code, $packaging, $quantity, $description);
    }
    /*----------------------------------------------------------*/
?>

<script>

    $("#addPackagingForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var packaging = $("input[name=packaging]").val();
        var quantity = $("input[name=quantity]").val();
        var description = $("input[name=packaging]").val() + " of " + $("input[name=quantity]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/packagings/add.php",
            data: {packaging: packaging, quantity: quantity, description: description},
            success: function(){
                $.uinotify({
	                'text'		: 'New Packaging Added',
	                'duration'	: 3000
                });
                $("input[name=packaging]").val("");
                $("input[name=quantity]").val("");
            }
        })
    })
</script>

<div class="custom-form">
    <form method="post" action="" id="addPackagingForm">
        <div class="grid_9 alpha">
            <div>
                <label for="packaging">Packaging:</label>
                <input type="text" id="packaging" required="required" name="packaging"/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9 alpha">
            <div>
                <label for="quantity">Quantity:</label>
                <input type="text" id="quantity" required="required" name="quantity"/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_9 omega">
            <div class="submit">
                <input type="submit" name="addPackaging" value="Add Packaging"/>
            </div>
        </div>
    </form>
</div>

