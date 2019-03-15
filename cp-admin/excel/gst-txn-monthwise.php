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
$data[] = array('S.No', 'Date', 'User Type', 'User', 'Uid', 'Type', 'Operator', 'Amount', 'Comm/Sur', 'Taxable', 'GST', 'Total');
//
$oprs = [];
$query = $db->query("SELECT operator_id,operator_name FROM operators ORDER BY service_type,operator_name ASC ");
while($row = $db->fetchNextObject($query)) {
	$oprs[$row->operator_id] = $row->operator_name;
}
function gstOpName($opr, $id) {
	$result = "";
	foreach($opr as $key=>$data) {
		if($key == $id) {
			$result = $data;
		}
	}
	return $result;
}

$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : '2017';
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$utype = isset($_GET["utype"]) && $_GET["utype"]!='' ? mysql_real_escape_string($_GET["utype"]) : '';
$op = isset($_GET['op']) && $_GET['op']!='' ? mysql_real_escape_string($_GET['op']) : '';
if($month=='all') {
	$dtFrom = "2017-07-01 00:00:00";
	$dtTo = "2017-12-31 23:23:59";
}
$sWhere = "WHERE gst.rch_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
if(isset($_GET['utype']) && $_GET['utype']!='') {
	$sWhere .= " AND gst.user_type='".mysql_real_escape_string($_GET["utype"])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND gst.uid='".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET['op']) && $_GET['op']!='') {
	$sWhere .= " AND gst.operator_id='".mysql_real_escape_string($_GET["op"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"]!='') {
	$sWhere .= " AND gst.bill_type='".mysql_real_escape_string($_GET["type"])."' ";
}

$statement = "gst_monthlytxn gst LEFT JOIN apps_user user ON gst.uid=user.uid $sWhere ORDER BY gst.rch_date ASC";

$scnt = 1;
//
$query = $db->query("SELECT gst.*, user.company_name FROM {$statement} ");
while($row = $db->fetchNextObject($query)) {
	$billing_type = getBillingType($row->bill_type);
	$operator_name = gstOpName($oprs,$row->operator_id);
	$user_type = getUserType($row->user_type);
	$data[] = array($scnt++, $row->rch_date, $user_type, $row->company_name, $row->uid, $billing_type, $operator_name, $row->rch_amount, $row->rch_comm_value, $row->gst_net, $row->gst_tax, $row->taxable_value);
}

$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('gst_txn_monthwise_'.$month.'_'.date("d-M-Y"));
ob_end_flush();