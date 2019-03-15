<?php
session_start();
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!= '' ? mysql_real_escape_string($_GET['error']) : 0;
if($_POST['bill_type']!='' || $_POST['uid']!='' || $_POST['invoice_date']!='' || $_POST['bill_month']!='') {
	$in_date = explode("-",$_POST['invoice_date']);
	$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0];
	$in_month = explode("-",$_POST['invoice_month']);
	$invoice_month = $in_month[2]."-".$in_month[1]."-".$in_month[0];
	$db->execute("INSERT INTO `gst_p2ainvoices`(`uid`, `bill_type`, `invoice_date`, `invoice_month`, `bill_mode`, `description`, `hsn_sac`, `quantity`, `sub_total`, `tax_amount`, `total_amount`, `tax_type`, `rate`, `unit`, `create_date`) VALUES ('".$_POST['uid']."', '".$_POST['bill_type']."', '".$invoice_date."', '".$invoice_month."', '".$_POST['bill_mode']."', '".$_POST['item_desc']."', '".$_POST['item_sac']."', '".$_POST['quantity']."', '".$_POST['sub_total']."', '".$_POST['tax_total']."', '".$_POST['total_amt']."', '".$_POST['tax_type']."', '".$_POST['item_rate']."', '".$_POST['item_unit']."', NOW())");
	$invoiceid = $db->lastInsertedId();
	echo $invoiceid;
} else {
	echo 0;
}