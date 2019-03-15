<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
require(DIR."/system/php-excel.class.php");
ob_start();
$data[] = array('Txn No', 'Mode', 'Recharge Date', 'Account Number', 'Operator', 'Service', 'Amount', 'Surcharge', 'Status', 'Status Detail', 'Request Txn No', 'Operator Ref No', 'IP Address');
$nFrom = date("Y-m-d", strtotime('-168 HOUR'));
$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
if($from >= $nFrom) {
	$aFrom = $from;
} else {
	$aFrom = $nFrom;
}
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE recharge.uid = '".$_SESSION['apiuser_uid']."' AND recharge.request_date LIKE '%".$aFrom."%' and tran.transaction_term='RECHARGE' and tran.type='dr' ";

$array['recharge'] = getRechargeStatusList();
$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN transactions tran ON recharge.recharge_id = tran.transaction_ref_no $sWhere ORDER BY recharge.request_date ASC";

$query = $db->query("SELECT recharge.*,tran.closing_balance, opr.operator_name FROM {$statement} ");
while($result = $db->fetchNextObject($query)) {
	$data[] = array($result->recharge_id, $result->recharge_mode, $result->request_date, $result->account_no, $result->operator_name, $result->service_type, $result->amount, $result->surcharge, getRechargeStatusUser($result->status), $result->status_details, $result->request_txn_no, $result->operator_ref_no, $result->recharge_ip);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('recharge_report_'.date("d-M-Y"));
ob_end_flush();