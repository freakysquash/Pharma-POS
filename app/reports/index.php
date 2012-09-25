<script>
    $(document).ready(function(){
        
        $(".datepicker").datepicker({dateFormat: "y-mm-dd"});
        
         $("#reportsList").dataTable({
			"sPaginationType": "full_numbers",
            "bJQueryUI": true
        });
        
        /* CREATING DIALOGS */
        $("#reportDialog").dialog({
            title:"Report",
            autoOpen:false,
            draggable:false,
            resizable:false,
            closeOnEscape:true,
            modal:true,
            width:960,
            height:700,
            buttons:{
                "Print":function(){
                    $("#reportDialog").printElement();
                },
                "Close":function(){
                    $(this).dialog("close");
                }
            }
        })
        
        $("#skuFilterDialog").dialog({
            title: "SKU Filter",
            autoOpen:false,
            draggable:false,
            modal:true,
            resizable:false,
            closeOnEscape:true,
            width:270,
            height:220
        })
        
        $("#dateFilterDialog").dialog({
            title: "Date Filter",
            autoOpen:false,
            draggable:false,
            modal:true,
            resizable:false,
            closeOnEscape:true,
            width:250,
            height:200
        })
        
        $("#dateRangeFilterDialog").dialog({
            title: "Date Range Filter",
            autoOpen:false,
            draggable:false,
            modal:true,
            resizable:false,
            closeOnEscape:true,
            width:250,
            height:260
        })
        /* --------------------------------------------------------- */
        /* REPORT TYPE SWITCH */
        $(".reportEntry").click(function(){
            var report = $(this).attr("data-report");
            $("input[name=activeReport]").val(report);
            switch(report){
                case "itemSales":
                    $("#skuFilterDialog").dialog("open")
                    break;
                case "dailySales":
                    $("#dateFilterDialog").dialog("open")
                    break;
                case "rangeDailySales":
                    $("#dateRangeFilterDialog").dialog("open")
                    break;
                default:
                    break;
            }
        })
        /* --------------------------------------------------------- */
        /* FILTER FORM SUBMISSION - REPORT GENERATION */
        $("#skuFilterForm").unbind("submit").submit(function(e){
            e.preventDefault();
            var report = $("input[name=activeReport]").val();
            var sku = $("input[name=sku]").val();
            $("#reportDialog").load("/app/reports/" + report + ".php?sku=" + sku);
            $("#reportDialog").dialog("open");
        })
        
        $("#dateFilterForm").unbind("submit").submit(function(e){
            e.preventDefault();
            var report = $("input[name=activeReport]").val();
            var date = $("input[name=date]").val();
            $("#reportDialog").load("/app/reports/" + report + ".php?d=" + date);
            $("#reportDialog").dialog("open");
        })
        
        $("#dateRangeFilterForm").unbind("submit").submit(function(e){
            e.preventDefault();
            var report = $("input[name=activeReport]").val();
            var start = $("input[name=start]").val();
            var end = $("input[name=end]").val();
            $("#reportDialog").load("/app/reports/" + report + ".php?s=" + start + "&e=" + end);
            $("#reportDialog").dialog("open");
        })
        /* --------------------------------------------------------- */
        /* SKU FILTER AUTOCOMPLETE */
        $("#sku").autocomplete({
            source: "/app/manage/items/auto.php",
            select: function(event, ui) {
                $.getJSON("/app/manage/items/checkItemSku.php?d=" + ui.item.value, function(data){
                    $("#sku").val(data.sku);
                })
            }
        })
        /* --------------------------------------------------------- */
    })
</script>

<input type="hidden" name="activeReport"/>
<div class="window">
    <div class="window-title">
        <span>Reports</span>
    </div>
    <div class="window-content">
        <div class="reports">
            <table id="reportsList">
                <thead>
                    <tr>
                        <td>Report Name</td>
                        <td>Description</td>
                        <td class="actions">&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Item Sales</td>
                        <td>This will generate a report showing the sales data of an item.</td>
                        <td><a href="#" data-report="itemSales" class="reportEntry">View</a></td>
                    </tr>
                    <tr>
                        <td>Daily Sales</td>
                        <td>This will generate a report showing the daily sales data of a given date.</td>
                        <td><a href="#" data-report="dailySales" class="reportEntry">View</a></td>
                    </tr>
                    <tr>
                        <td>Range Daily Sales</td>
                        <td>This will generate a report showing the daily sales data of a given date range.</td>
                        <td><a href="#" data-report="rangeDailySales" class="reportEntry">View</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="dialogs">
    
    <div id="reportDialog">
        
    </div>
   
    <div id="skuFilterDialog" class="ui-dialog-form">
        <form id="skuFilterForm">
            <div>
                <label>SKU/Product name</label>
                <input type="text" id="sku" name="sku"/>
            </div>
            <div>
                <input type="submit" value="Generate"/>
            </div>
        </form>
    </div>
    
    <div id="dateFilterDialog" class="ui-dialog-form">
        <form id="dateFilterForm">
            <div>
                <label>Date:</label>
                <input type="text" class="datepicker" name="date"/>
            </div>
            <div>
                <input type="submit" value="Generate"/>
            </div>
        </form>
    </div>
    
    <div id="dateRangeFilterDialog" class="ui-dialog-form">
        <form id="dateRangeFilterForm" class="ui-dialog-form">
            <div>
                <label>Start:</label>
                <input type="text" class="datepicker" name="start"/>
            </div>
            <div>
                <label>End:</label>
                <input type="text" class="datepicker" name="end"/>
            </div>
            <div>
                <input type="submit" value="Generate"/>
            </div>
        </form>
    </div>
    
</div>
