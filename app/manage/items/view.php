<?php
    include("../../../library/config.php");
    authenticate();
    $group = checkUserGroup($_SESSION["userId"]);
    checkIfAdministrator($group);
    
    if(isset($_GET["s"])){
        $i = mysql_fetch_assoc(getItemDetailsBySku($_GET["s"]));
?>

<script>
    $(function(){

    $("#changeImage").click(function(){
        $("input[name=imageSku]").val($(this).attr("data-sku"));
        $("#viewItemDialog").dialog("close");
        $("#changeImageDialog").dialog("open");
    })

    $("#deleteItem").click(function(){
        var sku = $(this).attr("data-sku");
        $(".manage-workspace").load("/app/manage/items/delete.php?s=" + sku);
        $("#viewItemDialog").dialog("close");
    })
        
    })
</script>

<style>
    #image{float:left;width:220px;height:200px;overflow:hidden;background:url("http://<?php echo ROOT; ?>/template/images/no-image-available.png") no-repeat}#image img{height:200px;width:220px}#details{margin:8px 0 0 10px;height:190px;width:220px;overflow:hidden;float:left}#details span{display:block;margin:0 0 10px 0}#action{clear:both;height:30px;text-align:right}
</style>

<div id="image">
    <img src="http://<?php echo ROOT; ?>/app/manage/items/image.php?s=<?php echo $_GET["s"]; ?>"/>
</div>
<div id="details">
    <span><?php echo $i["description_1"]; ?></span>
    <span><?php echo getPackagingName($i["packaging_code"]); ?></span>
    <span>Generic: &nbsp;<?php echo $i["generic_name"]; ?></span>
    <span>Price: &nbsp;<?php echo $i["price"]; ?></span>
    <span>Dep: &nbsp;<?php echo getDepartmentNameByCode($i["department_code"]); ?></span>
    <span>Cat: &nbsp;<?php echo getCategoryNameByCode($i["category_code"]); ?></span>
</div>
<div id="action">
    <button id="changeImage" data-sku="<?php echo $_GET["s"]; ?>">Change image</button>
</div>

<?php
    }
?>


