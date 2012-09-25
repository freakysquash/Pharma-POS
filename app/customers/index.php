<script>
    $(document).ready(function(){
        
        $("#newCustomerDialog").dialog({
            title: "New Customer",
            autoOpen:false,
            draggable:false,
            resizable:false,
            modal:true,
            height:390,
            width:350
        })
        
        $("#newCustomer").click(function(){
            $("#newCustomerDialog").dialog("open");
        })
        
        $("#customersTable").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        $("#newCustomerForm").unbind("submit").submit(function(e){
            e.preventDefault();
            var customerName = $("input[name=customerName]").val();
            var address = $("input[name=address]").val();
            var email = $("input[name=email]").val();
            var contactNo = $("input[name=contactNo]").val();
            $.ajax({
                type: "POST",
                url: "/app/customers/add.php",
                data: {customerName:customerName, address:address, email:email, contactNo:contactNo},
                success: function(){
                    $("input[name=customerName]").val("");
                    $("input[name=address]").val("");
                    $("input[name=email]").val("");
                    $("input[name=contactNo]").val("");
                    $.uinotify({
                        "text": "Customer added.",
                        "duration": 3000
                    });
                    $("#newCustomerDialog").dialog("close");
                    setTimeout(function(){
                        window.location.href = "?module=customers";
                    }, 3000);
                }
            })
        })
        
    })
</script>

<div class="window">
<div class="window-title">
    <span>Customers</span>
</div>
<div class="x-toolbar">
    <ul>
        <li><a href="#" id="newCustomer">New Customer</a></li>
    </ul>
</div>

<div id="customers">
    <table id="customersTable">
        <thead>
            <tr>
                <td>Name</td>
                <td>Address</td>
                <td>Email</td>
                <td>Contact no</td>
                <td>No of Transactions</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $customers = getCustomers();
                while($c = mysql_fetch_assoc($customers)){
            ?>
            <tr>
                <td><?php echo $c["customer_name"]; ?></td>
                <td><?php echo $c["address"]; ?></td>
                <td><?php echo $c["email_address"]; ?></td>
                <td><?php echo $c["contact_no"]; ?></td>
                <td><?php echo getCustomerTransactionCount($c["code"]); ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>
</div>

<div id="dialogs">
    <div id="newCustomerDialog" class="ui-dialog-form">
        <form id="newCustomerForm">
            <div>
                <label>Customer name:</label>
                <input type="text" id="customerName" required="required" name="customerName"/>
            </div>
            <div>
                <label>Address:</label>
                <input type="text" id="address" name="address"/>
            </div>
            <div>
                <label>Email address</label>
                <input type="text" id="email" name="email"/>
            </div>
            <div>
                <label>Contact no:</label>
                <input type="text" id="contactNo" name="contactNo"/>
            </div>
            <div>
                <input type="submit" value="Save Customer"/>
            </div>
        </form>
    </div>
</div>
