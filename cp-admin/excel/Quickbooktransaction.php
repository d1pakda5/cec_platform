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
$data[] = array('S.No', 'Date','Txn Time', 'User Details','Final Name', 'Type', 'Debit Amount', 'Credit Amount', 'Closing Balance', 'Term', 'Ref Txn No', 'Remark', 'User Type', 'Transaction User','Rate','Qty','Memo','Item','Desc','Message');

$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE trans.transaction_user_type = '0' AND trans.account_id = '0' AND trans.transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
	$sWhere .= " AND trans.to_account_id = '".mysql_real_escape_string($_GET["uid"])."' ";
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
	$date=explode(" ",$result->transaction_date);
	
	$data[] = array($scnt++,$date[0],$date[1],$result->company_name,"Upload Invoice", $result->type, $debit_amount, $credit_amount, $result->closing_balance, $result->transaction_term, $result->transaction_ref_no, $result->remark, $result->transaction_user_type, $result->transaction_by,"1",$debit_amount,$result->company_name." ; ".$result->remark,"erecharge",$result->company_name." ; ".$result->remark,$result->company_name." ; ".$result->remark);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('quickbook_transaction_report_'.date("d-M-Y"));
ob_end_flush();