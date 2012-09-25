<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["type"])){
        $table = "discounts";
        $column = "code";
        $code = getAvailableId($table, $column);
        $type = mres($_POST["type"]);
        $description = mres($_POST["description"]);
        $rate = mres($_POST["rate"] * .01);
        addDiscount($code, $type, $description, $rate);
    }
?>

<script type="text/javascript">

    $("#addDiscountForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var type = $("input[name=discountType]").val();
        var description = $("textarea#discountDescription").val();
        var rate = parseFloat($("input[name=discountRate]").val());
        $.ajax({
            type: "POST",
            url: "/app/manage/items/discounts/add.php",
            data: {type: type, description: description, rate: rate},
            success: function(){
                $.uinotify({
	                'text'		: 'New Discount Added',
	                'duration'	: 3000
                });
                $("input[name=discountType]").val("");
                $("textarea#discountDescription").val("");
                $("input[name=discountRate]").val("");
            }
        });
    })
</script>

<div class="custom-form">
    <form method="post" action="" id="addDiscountForm">
        <div class="grid_6 alpha">
            <label for="discountType">Discount type: </label>
            <input type="text" id="discountType" required="required" name="discountType"/>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <label for="discountDescription">Description:</label>
            <textarea id="discountDescription" required="required" name="discountDescription"></textarea>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <label for="discountRate">Rate: </label>
            <input type="text" id="discountRate" required="required" name="discountRate" placeholder="Enter rate in percentage"/>
        </div>
        <div class="clear"></div>
        <div class="grid_6 submit">
            <input type="submit" name="addDiscount" value="Add Discount"/>
        </div>
    </form>
</div>