<?php
session_start();
include('config.php');
$error = isset($_GET['error']) && $_GET['error']!= '' ? mysql_real_escape_string($_GET['error']) : 0;
if($_POST['bill_type']!='' || $_POST['uid']!='' || $_POST['invoice_date']!='' || $_POST['bill_month']!='') {
	$in_date = explode("-",$_POST['invoice_date']);
	$invoice_date = $in_date[2]."-".$in_date[1]."-".$in_date[0];
	$in_month = explode("-",$_POST['invoice_month']);
	$invoice_month = $in_month[2]."-".$in_month[1]."-".$in_month[0];
	
	$invs = $db->queryUniqueObject("SELECT * FROM gst_invoices WHERE id='".$_POST['invoiceid']."' ");
	if($invs) {
		$invoiceid = $invs->id;
			
		$db->execute("UPDATE `gst_invoicesitems` SET `net_amount`='".$_POST['item_net'][0]."', `taxable_amount`='".$_POST['item_taxable'][0]."', `tax_amount`='".$_POST['item_tax'][0]."', `rate`='".$_POST['item_rate'][0]."' WHERE invoiceid='".$invoiceid."' AND  description='".$_POST['item_desc'][0]."'");
		
		$db->execute("UPDATE `gst_invoicesitems` SET `net_amount`='".$_POST['item_net'][1]."', `taxable_amount`='".$_POST['item_taxable'][1]."', `tax_amount`='".$_POST['item_tax'][1]."', `rate`='".$_POST['item_rate'][1]."' WHERE invoiceid='".$invoiceid."' AND  description='".$_POST['item_desc'][1]."'");
		
		$db->execute("UPDATE `gst_invoices` SET `quantity`='".$_POST['quantity']."', `sub_total`='".$_POST['sub_total']."', `tax_amount`='".$_POST['tax_total']."', `total_amount`='".$_POST['total_amt']."', `tax_type`='".$_POST['tax_type']."', `create_date`=NOW(), is_update='1' WHERE id='".$invoiceid."'");
		
		echo $invoiceid;
		
	} else {
		echo 0;
	}
	
} else {
	echo 0;
}
