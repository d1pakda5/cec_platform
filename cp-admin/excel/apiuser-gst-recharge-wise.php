<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../../config.php");
ini_set('memory_limit','256M');
set_time_limit(9999999999);
require(DIR."/system/php-excel.class.php");
include('../gst-function.php');
ob_start();
//
$data[] = array('S.No', 'Date', 'Txn No', 'User', 'UID', 'State', 'Operator', 'Type', 'Number', 'Amount', 'Surcharge', 'Commission %', 'Net Amount', 'Taxable', 'GST Tax');
//
$from = isset($_GET["from"]) && $_GET["from"]!='' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"]!='' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '';
//
$sWhere = "WHERE rch.request_date BETWEEN '".$aFrom."' AND '".$aTo."' AND rch.status IN (0,1) AND usr.user_type='1' ";

if(isset($_GET['s']) && $_GET['s']!='') {
	$aStr = mysql_real_escape_string($_GET['s']);
	$sWhere .= " AND (rch.recharge_id='".$aStr."' OR rch.account_no='".$aStr."' OR rch.operator_ref_no='".$aStr."' OR rch.api_txn_no='".$aStr."') ";
}
if(isset($_GET['opr']) && $_GET['opr']!='') {
	$sWhere .= " AND rch.operator_id='".mysql_real_escape_string($_GET['opr'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND rch.uid='".mysql_real_escape_string($_GET['uid'])."' ";
}
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= " AND opr.billing_type='".mysql_real_escape_string($_GET['type'])."' ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id LEFT JOIN apps_user usr ON rch.uid=usr.uid LEFT JOIN usercommissions ucm ON usr.uid=ucm.uid AND rch.operator_id=ucm.operator_id $sWhere ORDER BY rch.request_date ASC";
//
$scnt = 1;
$query = $db->query("SELECT rch.*,opr.operator_name,opr.billing_type,opr.is_surcharge,usr.user_type,usr.company_name,usr.states,ucm.comm_dist,ucm.comm_api FROM {$statement}");
while($row = $db->fetchNextObject($query)) {
	$recharge_date = date("d/m/Y", strtotime($row->request_date));
	$billing_type = _gstBillingType($row->billing_type);
	$com_value = $row->comm_api;
	$net_amt = _getCommAmount($row->amount,$com_value,$row->is_surcharge,$row->surcharge);
	$gst = _gstTaxAmount($net_amt);
	$data[] = array($scnt++, $recharge_date, $row->recharge_id, $row->company_name, $row->uid, $row->states, $row->operator_name, $billing_type, $row->account_no, $row->amount, $row->surcharge, $com_value, $net_amt, $gst[0], $gst[1]);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('apiuser_gst_recharge_wise_'.$from.'_to_'.$to);
ob_end_flush();