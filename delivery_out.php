<?php
$db = mysql_connect('localhost', 'root', '') or die('Server Connection Error : '.mysqli_error());
mysql_select_db('sample', $db) or die('Database Connection Error : '.mysqli_error());

//id 	purchase_no 	supplier_code 	sku 	quantity 	amount 	unit_price 	vatable 	vat_amount 	line_vat 	remaining 	discrepancy 	status 	received_by 	date_received 	delivery_receipt_no 	sales_invoice_no 	batch_no 	item_record_no 	expiration_date 	expenses 	doc_date 	doc_time

$select = "SELECT * FROM deliveries";
$query = mysql_query($select);
$row = mysql_num_rows($query);
$info = mysql_fetch_array($query);

//echo "Row: " . $row . "<br/>";

$order = 101;
$inx = 1;
$y = 1;

while($y<=$row) //$info = mysql_fetch_array($query))
{

	echo "SKU: " . $info['sku'] . " ";
	echo "Quantity: " . $info['quantity'] . " ";
	echo "Batch No.: " . $info['batch_no'] . " ";
	echo "Item Record: " . $info['item_record_no'];
	echo "<br>";

	$sku = $info['sku'];
	$qty = $info['quantity'];
	$batch = $info['batch_no'];
	$item = $info['item_record_no'];

	$i = 1;
	$ctr = 0;
	$count = 0;
	
	$y++;

}

$sku1 = $info['sku'];
$trans = "001";

while($inx<=$order)
{
	$select1 = "SELECT * FROM deliveries WHERE quantity <> '0' AND sku = '$sku1'";
	$query1 = mysql_query($select1);
	$info1 = mysql_fetch_array($query1);
	$row1 = mysql_num_rows($query1);
	
	$qty = $info1['quantity'];
	$qty1 = $qty - 1;
	
	$batch1 = $info1['batch_no'];
	$item1 = $info1['item_record_no'];
	
	if($row<>0)
	{
		$update = "UPDATE deliveries SET quantity = '$qty1' WHERE sku = '$sku1'  AND batch_no = '$batch1' AND item_record_no = '$item1' ";
		$update_query = mysql_query($update);
	}
	
	$select_order = "SELECT * FROM ordertrail WHERE TRANS_NO = '$trans' AND SKU = '$sku1' AND BATCH = '$batch1' AND ITEM = '$item1'";
	$query_order = mysql_query($select_order);		
	$row_order = mysql_num_rows($query_order);
	$info_order = mysql_fetch_array($query_order);
		
	if($row_order<>0)
	{
		$qty_order = $info_order['QUANTITY'] + 1;
		$update_order = "UPDATE ordertrail SET QUANTITY = '$qty_order' WHERE SKU = '$sku1' AND BATCH = '$batch1' AND ITEM = '$item1' AND TRANS_NO = '$trans'";
		$up_que_order = mysql_query($update_order);
	}
	else
	{			
		$qty_order = 1;
		$status = "OUT";
		//$batch = $info_order['BATCH'];
		//$item = $info_order['ITEM'];
		$insert_order = "INSERT INTO ordertrail (SKU,QUANTITY,BATCH,ITEM,TRANS_NO,STATUS) VALUES ('$sku1','$qty_order','$batch1','$item1','$trans','$status')";
		$in_que_order = mysql_query($insert_order);
	}
	
	echo "Order: " . $inx . "<br/>";
	$inx++;
}

//$update = "UPDATE deliveries SET quantity = '' WHERE sku = ''  AND batch_no = '' AND item_record_no = '' ";

//SKU, QUANTITY, BATCH, ITEM, TRANS_NO

?>