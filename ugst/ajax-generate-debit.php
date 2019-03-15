<?php
session_start();
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!= '' ? mysql_real_escape_string($_GET['error']) : 0;
if($_POST['bill_type']!='' || $_POST['uid']!='' || $_POST['invoice_date']!='' || $_POST['bill_month']!='') {
	$in_date = explode("-",$_POST['invoice_date']);
	$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0];
	$in_month = explode("-",$_POST['invoice_month']);
	$invoice_month = $in_month[2]."-".$in_month[1]."-".$in_month[0];
	$db->execute("INSERT INTO `gst_debitnote`(`uid`, `bill_type`, `invoice_date`, `invoice_month`, `bill_mode`, `description`, `recharge_amount`, `comm_amount`, `debit_amount`, `unit`, `create_date`) VALUES ('".$_POST['uid']."', '".$_POST['bill_type']."', '".$invoice_date."', '".$invoice_month."', '".$_POST['bill_mode']."', '".$_POST['description']."', '".$_POST['amount']."', '".$_POST['commission']."', '".$_POST['debit_amount']."', '".$_POST['unit']."', NOW())");
	$invoiceid = $db->lastInsertedId();
	echo $invoiceid;
} else {
	echo 0;
}