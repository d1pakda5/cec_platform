<?php
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
function getStatusRechargeMsg($id) {
	if($id == '0') {			
		$result = "Transaction Successful Amount Debited";
	} else if($id == '1') {
		$result = "Transaction Pending Amount Debited";
	} else if($id == '2') {
		$result = "Transaction Failed Amount Reversed";
	} else if($id == '3') {
		$result = "Transaction Failed Amount Refunded";
	} else if($id == '4') {
		$result = "Transaction Failed Amount Reversed";
	} else if($id == '5') {
		$result = "Transaction Processed Amount Debited";
	} else if($id == '6') {
		$result = "Transaction Failed Amount Reversed";
	} else if($id == '7') {
		$result = "Transaction Processed Amount Debited";
	} else if($id == '8') {
		$result = "Transaction Submitted Amount Debited";
	} else if($id == '9') {
		$result = "Transaction Successful Amount Debited";
	} else {
		$result = "Transaction Pending Amount Debited";
	}
	return $result;
}

if(isset($_GET["userid"]) && isset($_GET["key"]) && isset($_GET["txn"])) {
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$txn = mysql_real_escape_string($_GET["txn"]);
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
	if($user_info) {
		if($user_info->status == '1') {			
			$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_code FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.uid = '".$user_info->uid."' AND rch.recharge_id = '".$txn."' ");
			if($recharge_info) {							
				$array['status'] = getRechargeStatusList();
				echo getRechargeStatus($array['status'],$recharge_info->status).",".$recharge_info->recharge_id.",".$recharge_info->operator_code.",".$recharge_info->account_no.",".$recharge_info->amount.",".$recharge_info->operator_ref_no.",".$recharge_info->reference_txn_no.",".getStatusRechargeMsg($recharge_info->status).",".$recharge_info->response_date;
				
			} else {
				echo "ERROR,Invalid Transaction ID";
			}
		} else {
			echo "ERROR,Inactive User";
		}
	} else {
		echo "ERROR,Invalid User ID";
	}
} else {
	echo "ERROR,Parameter Is Missing";
}
?>