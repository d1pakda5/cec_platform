<?php
session_start();
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$content = "";
foreach ($_GET as $key => $value) {
	$content .= $key."=".urldecode($value)."; ";	
}
$db->execute("INSERT INTO `longcode_message`(`id`, `msisdn`, `message`, `ip`, `request_date`) VALUES ('', '".$_GET['from']."', '".$_GET['message']."', '".$ip."', NOW())");

if(isset($_GET['from']) && $_GET['from'] != '' && isset($_GET['message']) && $_GET['message'] != '') {
	if(strlen($_GET['from'])=='10') {
		$mobile = $_GET['from'];
	} else {
		$mobile = substr($_GET['from'], 2, 10);
	}
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".trim($mobile)."' AND status = '1' ");
	if($user_info) {
		$msg = trim($_GET['message']);
		
		$__msg = explode(" ", $msg);
		if($__msg[0] == 'RR') {
			$code = substr($msg, 3, 2);
			$msg_param = substr($msg, 5);
		} else {			
			$code = substr($msg, 0, 2);
			$msg_param = substr($msg, 3);
		}
		
		if($code == 'CB') {
			include(DIR."/longcode/balance.php");
		}
	}
}
die();
?>
