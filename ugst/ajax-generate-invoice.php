<?php
session_start();
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!= '' ? mysql_real_escape_string($_GET['error']) : 0;
if($_POST['bill_type']!='' || $_POST['uid']!='' || $_POST['invoice_date']!='' || $_POST['bill_month']!='') {
	$in_date = explode("-",$_POST['invoice_date']);
	$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0];
	$in_month = explode("-",$_POST['invoice_month']);
	$invoice_month = $in_month[2]."-".$in_month[1]."-".$in_month[0];
	if($_POST['bill_type']=='1' || $_POST['bill_type']=='3') {
		//
		$invs = $db->queryUniqueObject("SELECT * FROM gst_invoices WHERE bill_type!='2' ORDER BY id DESC ");
		$last_num = $invs->invoice_num;
		$new_num = ++$last_num;
		$invoice_num = sprintf('%06d', $new_num);
		//
	}
	$db->execute("INSERT INTO `gst_invoices`(`invoice_num`, `uid`, `bill_type`, `invoice_date`, `invoice_month`, `bill_mode`, `quantity`, `sub_total`, `tax_amount`, `total_amount`, `tax_type`, `create_date`) VALUES ('".$invoice_num."', '".$_POST['uid']."', '".$_POST['bill_type']."', '".$invoice_date."', '".$invoice_month."', '".$_POST['bill_mode']."', '".$_POST['quantity']."', '".$_POST['sub_total']."', '".$_POST['tax_total']."', '".$_POST['total_amt']."', '".$_POST['tax_type']."', NOW())");
	$invoiceid = $db->lastInsertedId();
	
	$item = $_POST['item_id'];
	foreach($item as $key=>$val) {
		$item_desc = htmlentities(addslashes($_POST['item_desc'][$key]),ENT_QUOTES);
		$item_sac = htmlentities(addslashes($_POST['item_sac'][$key]),ENT_QUOTES);
		$item_amt = htmlentities(addslashes($_POST['item_amt'][$key]),ENT_QUOTES);
		$item_net = htmlentities(addslashes($_POST['item_net'][$key]),ENT_QUOTES);
		$item_taxable = htmlentities(addslashes($_POST['item_taxable'][$key]),ENT_QUOTES);
		$item_tax = htmlentities(addslashes($_POST['item_tax'][$key]),ENT_QUOTES);
		$item_rate = htmlentities(addslashes($_POST['item_rate'][$key]),ENT_QUOTES);
		$item_unit = htmlentities(addslashes($_POST['item_unit'][$key]),ENT_QUOTES);
		$db->execute("INSERT INTO `gst_invoicesitems`(`invoiceid`, `uid`, `description`, `hsn_sac`, `amount`, `net_amount`, `taxable_amount`, `tax_amount`, `rate`, `unit`) VALUES ('".$invoiceid."', '".$_POST['uid']."', '".$item_desc."', '".$item_sac."', '".$item_amt."', '".$item_net."', '".$item_taxable."', '".$item_tax."', '".$item_rate."', '".$item_unit."')");
	}
	echo $invoiceid;
} else {
	echo 0;
}
