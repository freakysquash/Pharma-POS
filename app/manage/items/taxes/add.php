<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_POST["type"])){
        $table = "taxes";
        $column = "code";
        $code = getAvailableId($table, $column);
        $type = mres($_POST["type"]);
        $description = mres($_POST["description"]);
        $rate = preg_replace("/[^0-9]/", '', $_POST["rate"]);
        addSalesTax($code, $type, $description, $rate);
    }
?>

<script type="text/javascript">
    
    $(".num").keydown(function(event){if(event.shiftKey)event.preventDefault();if(event.keyCode==46||event.keyCode==8);else if(event.keyCode<95){if(event.keyCode<48||event.keyCode>57)event.preventDefault()}else if(event.keyCode<96||event.keyCode>105)event.preventDefault()});

    
    $("#addSalesTaxForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var type = $("input[name=taxType]").val();
        var description = $("textarea#taxDescription").val();
        var rate = parseFloat($("input[name=taxRate]").val());
        $.ajax({
            type: "POST",
            url: "/app/manage/items/taxes/add.php",
            data: {type: type, description: description, rate: rate},
            success: function(){
                $.uinotify({
	                'text'		: 'New Sales Tax Added',
	                'duration'	: 3000
                });
                $("input[name=taxType]").val("");
                $("textarea#taxDescription").val("");
                $("input[name=taxRate]").val("");
            }
        });
    })
</script>

<div class="custom-form">
    <form method="post" action="" id="addSalesTaxForm">
        <div class="grid_6 alpha">
            <label for="taxType">Tax type: </label>
            <input type="text" id="taxType"  required="required" name="taxType"/>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <label for="taxDescription">Description:</label>
            <textarea id="taxDescription" name="taxDescription"></textarea>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha">
            <label for="taxRate">Rate: </label>
            <input type="text" id="taxRate" class="num" required="required" name="taxRate" placeholder="Enter rate in percentage"/>
        </div>
        <div class="clear"></div>
        <div class="grid_6 alpha submit">
            <input type="submit" name="addSalesTax" value="Add Sales Tax"/>
        </div>
    </form>
</div>