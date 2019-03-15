<?php
session_start();
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!= '' ? mysql_real_escape_string($_GET['error']) : 0;
if($_POST['bill_type']!='' || $_POST['uid']!='' || $_POST['invoice_date']!='' || $_POST['bill_month']!='') {
	$in_date = explode("-",$_POST['invoice_date']);
	$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0];
	$in_month = explode("-",$_POST['invoice_month']);
	$invoice_month = $in_month[2]."-".$in_month[1]."-".$in_month[0];
	$invs = $db->queryUniqueObject("SELECT * FROM gst_debitnote WHERE id='".$_POST['invoiceid']."' ");
	if($invs) {
		$invoiceid = $invs->id;
		$db->execute("UPDATE `gst_debitnote` SET `comm_amount`='".$_POST['commission']."', `debit_amount`='".$_POST['debit_amount']."', `create_date`=NOW(), is_update='1' WHERE id='".$invoiceid."' ");
		echo $invoiceid;
	} else {
		echo 0;
	}
} else {
	echo 0;
}