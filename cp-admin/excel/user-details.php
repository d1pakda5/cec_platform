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
$data[] = array('S.No', 'User Type', 'UID', 'Name', 'Business Name', 'Mobile', 'Address', 'Status');

$sWhere = "WHERE user.user_type IN (1,3,4,5,6)";

if(isset($_GET['type']) && $_GET['type'] != '') {
	$sWhere .= " AND user.user_type = '".mysql_real_escape_string($_GET['type'])."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND user.status = '".mysql_real_escape_string($_GET['status'])."' ";
}

$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id ASC";

$scnt = 1;
$query = $db->query("SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement}");

while($result = $db->fetchNextObject($query)) {
	$user_type = getUserType($result->user_type);
	if($result->status == '1') {
		$status = "Active";
	} else if($result->status == '1') {
		$status = "Trash";
	}
	else  {
		$status = "Inactive";
	}
	$data[] = array($scnt++, $user_type, $result->uid, $result->fullname, $result->company_name, $result->mobile, $result->address, $status);
} 
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('all_user_details_'.date("d-M-Y"));
ob_end_flush();