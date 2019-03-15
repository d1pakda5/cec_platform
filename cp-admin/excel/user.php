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

$data[] = array('S.No', 'User Type', 'UID', 'Name', 'Business Name', 'Mobile', 'Balance', 'Cutoff', 'Status');

$sWhere = "WHERE user.user_type IN (1,3,4,5) AND user.status != '9' ";
if(isset($_GET['deduct']) && $_GET['deduct'] != '') {
	$sWhere .= "AND user.is_deduct='".mysql_real_escape_string($_GET['deduct'])."' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id ASC";

$scnt = 1;
$query = $db->query("SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement}");
while($result = $db->fetchNextObject($query)) {
	$user_type = getUserType($result->user_type);
	if($result->status == '1') {
		$status = "Active";
	} else {
		$status = "Inactive";
	}
	$data[] = array($scnt++, $user_type, $result->uid, $result->fullname, $result->company_name, $result->mobile, $result->balance, $result->cuttoff, $status);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('all_user_report_'.date("d-M-Y"));
ob_end_flush();