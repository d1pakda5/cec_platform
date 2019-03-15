<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../../config.php");
ini_set('memory_limit','128M');
set_time_limit(9999999999);
require(DIR."/system/php-excel.class.php");
ob_start();
//
$data[] = array('S.No', 'User Name', 'User Type', 'Month', 'Invoice No.', 'Invoice Type', 'Net', 'SGST Tax', 'CGST Tax', 'IGST Tax', 'Total');
//
function getGstTypeAmt($type,$amount) {
	$sgst = 0;
	$igst = 0;
	if($type=='2') {
		//SGST
		$sgst = $amount/2;
	} else {
		//IGST
		$igst = $amount;
	}
	return array('igst'=>$igst,'sgst'=>$sgst,'cgst'=>$sgst);
}

$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : '2017';
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$utype = isset($_GET["utype"]) && $_GET["utype"]!='' ? mysql_real_escape_string($_GET["utype"]) : '';
//
if($month=='all') {
	$dtFrom = "2017-07-01 00:00:00";
	$dtTo = "2017-12-31 23:23:59";
}
//
$sWhere = "WHERE gst.id!='' AND gst.invoice_date BETWEEN '".$dtFrom."' AND '".$dtTo."'";
if(isset($_GET['utype']) && $_GET['utype']!='') {
	$sWhere .= " AND user.user_type='".mysql_real_escape_string($_GET["utype"])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND gst.uid='".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"]!='') {
	$sWhere .= " AND gst.bill_type='".mysql_real_escape_string($_GET["type"])."' ";
}
//
$statement = "gstinvoices gst LEFT JOIN apps_user user ON gst.uid=user.uid $sWhere ORDER BY gst.invoice_month,gst.id DESC";
//
$subtotal_sum = 0;
$sgst_sum = 0;
$cgst_sum = 0;
$igst_sum = 0;
$total_sum = 0;
$scnt = 1;
//
$query = $db->query("SELECT gst.*, user.company_name,user.user_type,user.gst_type FROM {$statement}");
while($row = $db->fetchNextObject($query)) {
	$user_type = getUserType($row->user_type);
	$invoice_month = date("F, Y", strtotime($row->invoice_month));
	$billing_type = strtoupper(getBillingType($row->bill_type));
	$subtotal_sum += $row->sub_total;
	$gst = getGstTypeAmt($row->gst_type,$row->tax_amount);
	$sgst_sum += $gst['sgst'];
	$cgst_sum += $gst['cgst'];
	$igst_sum += $gst['igst'];
	$total_sum += $row->total_amount;
	$data[] = array($scnt++, $row->company_name." (".$row->uid.")", $user_type, $invoice_month, $row->invoice_num, $billing_type, $row->sub_total, $gst['sgst'], $gst['cgst'], $gst['igst'], $row->total_amount);
}

$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('gst_invoices_month_'.$month.'_'.date("d-M-Y"));
ob_end_flush();