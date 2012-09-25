<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    $company = getCompanyDetails();
    $c = mysql_fetch_assoc($company);
    
    if(isset($_POST["company"])){
        $table = "company";
        $column = "code";
        $code = getAvailableId($table, $column);
        $company = mres(ucwords($_POST["company"]));
        $address1 = mres($_POST["address1"]);
        $address2 = mres($_POST["address2"]);
        $accreditation = mres($_POST["accreditation"]);
        $permitNo = mres($_POST["permitNo"]);
        $tin = mres($_POST["tin"]);
        $serialNo = mres($_POST["serialNo"]);
        setCompany($code, $company, $address1, $address2, $accreditation, $permitNo, $tin, $serialNo);
    }
?>

<script>
    $("#setCompanyForm").unbind("submit").submit(function(e){
        e.preventDefault();
        var company = $("input[name=company]").val();
        var address1 = $("#address1").val();
        var address2 = $("#address2").val();
        var accreditation = $("input[name=accreditation]").val();
        var permitNo = $("input[name=permitNo]").val();
        var tin = $("input[name=tin]").val();
        var serialNo = $("input[name=serialNo]").val();
        $.ajax({
            type: "POST",
            url: "/app/manage/company/index.php",
            data: { company: company, address1: address1, address2: address2, accreditation: accreditation, permitNo: permitNo, tin: tin, serialNo: serialNo },
            success: function(){
                window.location.href = '/?module=manage&page=company';
            }
        })
    })
</script>

<div class="custom-form">
    <form id="setCompanyForm">
        <div class="grid_8 alpha">
            <label>Company name:</label>
            <input type="text" id="company" required="required" name="company" value="<?php echo $c["company_name"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>Address 1:</label>
            <input type="text" id="address1" required="required" name="address1" value="<?php echo $c["address_1"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>Address 2:</label>
            <input type="text" id="address2" name="address2" value="<?php echo $c["address_2"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>Accreditation:</label>
            <input type="text" id="accreditation" name="accreditation" value="<?php echo $c["accreditation"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>Permit no:</label>
            <input type="text" id="permitNo" name="permitNo" value="<?php echo $c["permit_no"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>TIN:</label>
            <input type="text" id="tin" name="tin" value="<?php echo $c["tin"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha">
            <label>Serial no:</label>
            <input type="text" id="serialNo" name="serialNo" value="<?php echo $c["serial_no"]; ?>"/>
        </div>
        <div class="clear"></div>
        <div class="grid_8 alpha submit">
            <input type="submit" value="Set Company"/>
        </div>
    </form>
</div>