<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
require(DIR."/system/php-excel.class.php");
ob_start();
$data[] = array('Date','Type','User','Ref.','Remark','Action','Amount');
$nFrom = date("Y-m-d 00:00:00", strtotime('-24 HOUR'));
$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
if($from >= $nFrom) {
	$aFrom = $from;
} else {
	$aFrom = $nFrom;
}
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE trans.account_id = '".$_SESSION['apiuser_uid']."' AND trans.transaction_date LIKE '%".$aFrom."%' ";
$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid $sWhere ORDER BY trans.transaction_date DESC";



$query = $db->query("SELECT * FROM {$statement}");
while($result = $db->fetchNextObject($query)) {
     if($result->transaction_user_type == '0') { 
     $user=SITENAME;
     } 
     else
     { 
      $user= $result->company_name;
     }
	$data[] = array($result->transaction_date, $result->transaction_term, $user, $result->transaction_ref_no, $result->remark, $result->type, round($result->amount,2));
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('transaction_report_'.date("d-M-Y"));
ob_end_flush();