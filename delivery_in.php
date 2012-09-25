<?php
$db = mysql_connect('localhost', 'root', '') or die('Server Connection Error : '.mysqli_error());
mysql_select_db('sample', $db) or die('Database Connection Error : '.mysqli_error());


$select_order = "SELECT * FROM ordertrail";
$query_order = mysql_query($select_order);
$row_order = mysql_num_rows($query_order);

echo "Yes! " . $row_order . " ";

if($row_order<>0)
{
	echo "Show!" . "<br/>";
	$row = 0;
	while($info_order = mysql_fetch_array($query_order)) //$row<=$row_order)
	{
		echo "Order: " . $row . "<br/>";
		
		$sku = $info_order['SKU'];
		$qty = $info_order['QUANTITY'];	
		$batch = $info_order['BATCH'];
		$item = $info_order['ITEM'];
		
		$select_del = "SELECT * FROM deliveries WHERE sku = '$sku' AND batch_no = '$batch' AND item_record_no = '$item'";
		$query_del = mysql_query($select_del);
		$info_del = mysql_fetch_array($query_del);
		
		$qty_del = $info_del['quantity'];
		
		$qty1 = $qty + $qty_del;
		
		echo $sku . " " . $qty . " " . $batch . " " . $item . "<br/>";
		
		//$trans = "001";
		$select = "UPDATE deliveries SET quantity = '$qty1' WHERE sku ='$sku' AND batch_no = '$batch' AND item_record_no = '$item'";
		$query = mysql_query($select);	

		$trans = $info_order['TRANS_NO'];
		$delete = "DELETE FROM ordertrail WHERE TRANS_NO = '$trans'";
		$query_delete = mysql_query($delete);
		
		//$row++;
	}
} 
?>