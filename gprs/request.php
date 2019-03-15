<?php
session_start();
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
//$db->execute("INSERT INTO `longcode_message`(`id`, `msisdn`, `message`, `ip`, `request_date`) VALUES ('', '".$_GET['uid']."', 'GPRS ".$_GET['msg']."', '".$ip."', NOW())");
ob_start();
if(isset($_GET['uid']) && $_GET['uid'] != '' && isset($_GET['pin']) && $_GET['pin'] != '' && isset($_GET['msg']) && $_GET['msg'] != '') {
	$mobile = htmlentities(addslashes($_GET['uid']),ENT_QUOTES);
	$pin = hashPin(htmlentities(addslashes($_GET['pin']),ENT_QUOTES));
	$msg = htmlentities(addslashes($_GET['msg']),ENT_QUOTES);
	$sub_division = $_GET['sub_division'];
	$billing_cycle =$_GET['billing_cycle'];
	$billing_unit = $_GET['billing_unit'];
	$pc_number = $_GET['pc_number'];	
	$customer_account=$_GET['customer_account'];
	$bsnl_service_type=$_GET['bsnl_service_type'];
	$dob = $_GET['dob'];
	$manager_id = $_GET['manager_id'];
	$remark = $_GET['remark'];
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".trim($mobile)."' AND status = '1' ");
	if($user_info) {
		if($msg == 'CP') {
			include(DIR."/gprs/pin-change.php");
		} else {
			if($user_info->pin == $pin) {
			   
				$length=2;
				if(isset($_GET['length']))
				{
					$length=$_GET['length'];
				}
				$code = substr($msg, 0, $length);
				
				$msg_param = substr($msg, $length);
				if($code == 'CB') {
					include(DIR."/gprs/balance.php");
				} else if($code == 'ST') {
					include(DIR."/gprs/fund.php");
				} else if($code == 'TF') {
					echo "Error, Service not available";
				} else if($code == 'TR') {
					include(DIR."/gprs/complaint-register.php");
				} else if($code == 'CS') {
					include(DIR."/gprs/complaint-status.php");
				} else if($code == 'CK') {
					include(DIR."/gprs/txn-status.php");
				} else if($code == 'SR') {
					include(DIR."/gprs/account-status.php");
				} else if($code == 'LS') {
					include(DIR."/gprs/last-transactions.php");
				} else {
					include(DIR."/gprs/recharge.php");
				}
			} else {
				echo "Error, Invalid Pin";
			}
		}
	} else {
		echo "Error, Invalid User";
	}
} else {
	echo "Error, Invalid Request";
}
?>