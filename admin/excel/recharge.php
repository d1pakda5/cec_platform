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
$data[] = array('S.No', 'Txn No', 'API Name', 'Mode', 'Recharge Date', 'Retailer Name', 'Retailer UID', 'Account Number', 'Operator', 'Service', 'Amount', 'Surcharge', 'Status', 'Status Detail', 'Request Txn No', 'API Txn No', 'API Status', 'Operator Ref No', 'Refunded', 'IP Address');

$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET['opr']) && $_GET['opr'] != '') {
	$sWhere .= " AND recharge.operator_id = '".mysql_real_escape_string($_GET['o'])."' ";
}
if(isset($_GET['api']) && $_GET['api'] != '') {
	$sWhere .= " AND recharge.api_id = '".mysql_real_escape_string($_GET['api'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid'] != '') {
	$sWhere .= " AND recharge.uid = '".mysql_real_escape_string($_GET['user'])."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND recharge.status = '".mysql_real_escape_string($_GET['status'])."' ";
}
if(isset($_GET['mode']) && $_GET['mode'] != '') {
	$sWhere .= " AND recharge.recharge_mode = '".mysql_real_escape_string($_GET['mode'])."' ";
}
$array['recharge'] = getRechargeStatusList();
$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN apps_user user ON recharge.uid = user.uid LEFT JOIN api_list api ON recharge.api_id = api.api_id $sWhere ORDER BY recharge.request_date ASC";
$scnt = 1;
$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name, api.api_name FROM {$statement} ");
while($result = $db->fetchNextObject($query)) {
	$data[] = array($scnt++, $result->recharge_id, $result->api_name, $result->recharge_mode, $result->request_date, $result->company_name, $result->uid, $result->account_no, $result->operator_name, $result->service_type, $result->amount, $result->surcharge, getRechargeStatus($array['recharge'],$result->status), $result->status_details, $result->request_txn_no, $result->api_txn_no, $result->api_status, $result->operator_ref_no, $result->is_refunded, $result->recharge_ip);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('recharge_report_'.date("d-M-Y"));
ob_end_flush();