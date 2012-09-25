<?php
    include("../../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    /* ADD MEASUREMENT */
    if(isset($_POST["measurement"])){
        $errors = null;
        $measurement = mres($_POST["measurement"]);
        if(empty($measurement)){
            $errors .= "<li>Measurement name is required</li>";
        }
        if(empty($errors)){
            addMeasurement($measurement);
        }
        else {
            echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
        }
    }
    /*----------------------------------------------------------*/
?>

<script>

    $("#addMeasurementForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var measurement = $("input[name=measurement]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/items/measurements/add.php",
            data: {measurement: measurement},
            success: function(){
                $.uinotify({
	                'text'		: 'New Unit of Measurement Added',
	                'duration'	: 3000
                });
                $("input[name=measurement]").val("");
            }
        })
    })
</script>

<div class="custom-form">
    <form method="post" action="" id="addMeasurementForm">
        <div class="grid_7 alpha">
            <div>
                <label for="measurement">Measurement:</label>
                <input type="text" id="measurement"  required="required" name="measurement"/>
            </div>
        </div>
        <div class="clear"></div>
        <div class="grid_8 omega">
            <div class="submit">
                <input type="submit" name="addMeasurement" value="Add Measurement"/>
            </div>
        </div>
    </form>
</div>

