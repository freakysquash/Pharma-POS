<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    /* ADD SUPPLIER */
    if(isset($_POST["supplier"])){
        $errors = null;
        $table = "suppliers";
        $column = "code";
        $code = getAvailableId($table, $column);
        $supplierName = mres(ucwords($_POST["supplier"]));
        if(empty($supplierName)){
            $errors .= "<li>Supplier name is required</li>";
        }
        $contactNo = mres($_POST["contactNo"]);
        $address1 = mres(ucwords($_POST["address1"]));
        $address2 = mres(ucwords($_POST["address2"]));
        $accreditation = mres($_POST["accreditation"]);
        $tinNo = mres($_POST["tinNo"]);
        $terms = mres($_POST["terms"]);
        $contract = mres($_POST["contract"]);
        if(empty($errors)){
            addSupplier($code, $supplierName, $contactNo, $address1, $address2, $accreditation, $tinNo, $terms, $contract);
        }
        else {
            echo "<div class='error-dialog'><ul>" . $errors . "</ul></div>";
        }
    }
    
    /*----------------------------------------------------------*/
?>

<script>
    $(document).ready(function(){
        $("#addSupplierForm").unbind("submit").submit(function(event){
            event.preventDefault();
            var supplier = $("input[name=supplier]").val();
            var contactNo = $("input[name=contactNo]").val();
            var address1 = $("input[name=address1]").val();
            var address2 = $("input[name=address2]").val();
            var accreditation = $("input[name=accreditation]").val();
            var tinNo = $("input[name=tinNo]").val();
            var terms = $("input[name=terms]").val();
            var contract = $("input[name=contract]").val();
            $.ajax({                                      
            type: 'POST',
            url: '/app/manage/suppliers/add.php',      
            data: {supplier: supplier, contactNo: contactNo, address1: address1, address2: address2, accreditation: accreditation, tinNo: tinNo, terms: terms, contract: contract},          
            success: function()
            {
                $.uinotify({
                    'text'		: 'Supplier Added.',
                    'duration'	: 3000
                });
                
                $("#addSupplier").trigger("click");
            } 
            });
        })
        return false;
    })
</script>

<div class="custom-form">
    <form method="post" action="" id="addSupplierForm">
            <div class="grid_9">
                <div>
                    <label for="supplier">Supplier:</label>
                    <input type="text" id="supplier" required="required" name="supplier"/>
                </div>
            </div>
            <div class="grid_9">
                <div>
                    <label for="contact_no">Contact no:</label>
                    <input type="text" id="contactNo" name="contactNo"/>
                </div>
            </div>
            <div class="clear"></div>
            <div class="grid_9">
                <div>
                    <label for="address1">Address 1:</label>
                    <input type="text" id="address1" name="address1"/>
                </div>
            </div>
            <div class="clear"></div>
            <div class="grid_9">
                <div>
                    <label for="address2">Address 2:</label>
                    <input type="text" id="address2" name="address2"/>
                </div>
            </div>
            <div class="clear"></div>
            <div class="grid_9">
                <div>
                    <label for="accreditation">Accreditation:</label>
                    <input type="text" id="accreditation" name="accreditation"/>
                </div>
            </div>
            <div class="grid_9">
                <div>
                    <label for="tinNo">Tin #:</label>
                    <input type="text" id="tinNo" name="tinNo"/>
                </div>
            </div>
            <div class="clear"></div>
            <div class="grid_9">
                <div>
                    <label for="terms">Terms:</label>
                    <input type="text" id="terms" name="terms"/>
                </div>
            </div>
            <div class="grid_9">
                <div>
                    <label for="contract">Contract:</label>
                    <input type="text" id="contract" name="contract"/>
                </div>
            </div>
            <div class="clear"></div>
            <div class="grid_10 submit">
                <div class="submit">
                    <input type="submit" name="addSupplier" value="Add Supplier"/>
                </div>
            </div>
    </form>
</div>


