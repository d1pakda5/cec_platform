<?php
session_start();
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$provider = isset($_GET['provider']) && $_GET['provider'] != '' ? mysql_real_escape_string($_GET['provider']) : "";
$code = isset($_GET['code']) && $_GET['code'] != '' ? mysql_real_escape_string($_GET['code']) : " ";
$db->execute("INSERT INTO `longcode_message`(`id`, `msisdn`,`provider`,`code`, `message`, `ip`, `request_date`) VALUES ('', '".$_GET['msisdn']."','".$provider."','".$code."', '".$_GET['msg']."', '".$ip."', NOW())");
if(isset($_GET['msisdn']) && $_GET['msisdn'] != '' && isset($_GET['msg']) && $_GET['msg'] != '') {
	if(strlen($_GET['msisdn'])=='10') {
		$mobile = $_GET['msisdn'];
	} else {
		$mobile = substr($_GET['msisdn'], 2, 10);
	}
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".trim($mobile)."' AND status = '1' ");
	if($user_info) {
		$msg = trim($_GET['msg']);
		if($ip == '182.18.161.112') {
			$code = substr($msg, 0, 2);
			$msg_param = substr($msg, 2);
		} else {
			$code = substr($msg, 3, 2);
			$msg_param = substr($msg, 5);
		}
		
		if($code == 'CB') {
			include(DIR."/longcode/balance.php");
		} else if($code == 'ST') {
			include(DIR."/longcode/fund.php");
		} else if($code == 'TF') {
			echo "Error, Service not available";
		} else if($code == 'TR') {
			include(DIR."/longcode/complaint-register.php");
		} else if($code == 'CS') {
			include(DIR."/longcode/complaint-status.php");
		} else if($code == 'CK') {
			include(DIR."/longcode/txn-status.php");
		} else if($code == 'SR') {
			include(DIR."/longcode/account-status.php");
		} else if($code == 'LS') {
			include(DIR."/longcode/last-transactions.php");
		} else {
			include(DIR."/longcode/recharge.php");
		}		
	}
}
die();
?>