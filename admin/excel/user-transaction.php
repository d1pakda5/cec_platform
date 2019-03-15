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
$data[] = array('S.No', 'Txn Date', 'User Details', 'Type', 'Debit Amount', 'Credit Amount', 'Closing Balance', 'Term', 'Ref Txn No', 'Remark', 'User Type', 'Transaction User');

$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE trans.transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
	$sWhere .= " AND trans.account_id = '".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"] != '') {
	$sWhere .= " AND trans.transaction_term = '".mysql_real_escape_string($_GET["type"])."' ";
}

$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid $sWhere ORDER BY trans.transaction_date DESC";
$scnt = 1;
$query = $db->query("SELECT trans.*, user.company_name FROM {$statement}");
while($result = $db->fetchNextObject($query)) {
	if($result->type == 'dr') {
		$debit_amount = $result->amount;
		$credit_amount = "";
	} else {
		$credit_amount = $result->amount;
		$debit_amount = "";
	}
	$data[] = array($scnt++, $result->transaction_date, $result->company_name, $result->type, $debit_amount, $credit_amount, $result->closing_balance, $result->transaction_term, $result->transaction_ref_no, $result->remark, $result->transaction_user_type, $result->transaction_by);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('user_transaction_report_'.date("d-M-Y"));
ob_end_flush();