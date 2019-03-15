<?php
$ip = $_SERVER['REMOTE_ADDR'];
include('../config.php');
include('../system/class.gst.php');
$gst = new GST();
$mode = "WEB";
$status = "1";
$uid = isset($_GET["u"]) && $_GET["u"]!='' ? mysql_real_escape_string($_GET["u"]) : '0';
$opr = isset($_GET["o"]) && $_GET["o"]!='' ? mysql_real_escape_string($_GET["o"]) : '1';
$amount = isset($_GET['a']) && $_GET['a']!='' ? mysql_real_escape_string($_GET['a']) : '100';
$account = '7566309322';
$reference_txn_no = isset($_GET['usertxn']) && $_GET['usertxn']!='' ? mysql_real_escape_string($_GET['usertxn']) : '';
$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".trim($uid)."' AND status='1' ");
if($agent_info) {
	$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_id='".$opr."' ");
	if($operator_info) {
		$api_id = $operator_info->api_id;
		if($agent_info->user_type=='1') {				
			$sCommission = getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
		} else {
			$sCommission = getUsersCommission(trim($agent_info->dist_id), $operator_info->operator_id, $amount, 'r');
		}
		print_r($sCommission);
		echo "<br>";
		if($sCommission['surcharge']=='y') {
			$rch_comm_type = '1';
			$rch_comm_value = 0;
		} else {
			$rch_comm_type = '0';
			$rch_comm_value = $sCommission['dsCom'];
		}
		//$db->execute("INSERT INTO `apps_recharge`(`uid`, `recharge_mode`, `api_id`, `service_type`, `operator_id`, `account_no`, `amount`, `surcharge`, `status`, `status_details`, `request_date`, `reference_txn_no`, `recharge_ip`) VALUES ('".$agent_info->uid."', '".$mode."', '".$api_id."', '".$operator_info->service_name."', '".$operator_info->operator_id."', '".$account."', '".$amount."', '".$sCommission['samount']."', '".$status."', '', NOW(), '".$reference_txn_no."', '".$ip."')");
		//$recharge_id = $db->lastInsertedId();
		
		//$wallet = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");
		$tax = getUserGstTxns($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
		print_r($tax);
		echo "<br>";
		echo $tax['total_debit'];
	}
}