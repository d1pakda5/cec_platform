<?php
session_start();
include("config.php");
ini_set('memory_limit','256M');
set_time_limit(9999999999);
require("../system/php-excel.class.php");
function _gstBillType($type) {
	if($type=='1') {
		$result = "P2P";
	}elseif($type=='2') {
		$result = "P2A";
	} elseif($type=='3') {
		$result = "SUR";
	} else {
		$result = "-";
	}
	return $result;
}
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : '2017';
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$uid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$user = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid='".$uid."' ");
if(!$user) {
	exit();
}
ob_start();
$data[] = array('S.No', 'Date', 'Operator Name', 'Type', 'Recharge Amount', 'Comm/Surcharge', 'Net Amount', 'Tax Amount', 'GST Rate');
//
$sWhere = "WHERE gst.uid='".$uid."' AND gst.rch_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
$statement = "gst_monthly_txns gst LEFT JOIN operators opr ON gst.operator_id=opr.operator_id $sWhere ORDER BY gst.rch_date ASC";
$scnt = 1;
$query = $db->query("SELECT gst.*, opr.operator_name FROM {$statement}");
while($row = $db->fetchNextObject($query)) {
	$date = date("F, Y", strtotime($row->rch_date));
	$bill_type = _gstBillType($row->bill_type);
	$operator_name = $row->operator_name;
	$gst_rate = $row->gst_rate." %";
	$data[] = array($scnt++, $date, $operator_name, $bill_type, $row->rch_amount, $row->rch_comm_value, $row->gst_net, $row->gst_tax, $gst_rate);
} 
// generate file (constructor parameters are optional)
$in_month = date("F_Y", strtotime($year."-".$month."-01"));
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML($user->company_name.'_'.$uid.'_statement_'.$in_month);
ob_end_flush();