<?php

include("../config.php");
	header("Access-Control-Allow-Origin: *");
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;		
		$txn_no = $recharge_info->recharge_id;
		$uid = $recharge_info->uid;
		$api_id = $recharge_info->api_id;
		$service_type = $recharge_info->service_type;
		$operator_id = $recharge_info->operator_id;
		$operator_name = $recharge_info->operator_name;
		$account_no = $recharge_info->account_no;
		$amount = $recharge_info->amount;
		$surcharge = $recharge_info->surcharge;
		$status = $recharge_info->status;
		$status_details = $recharge_info->status_details;
		$request_date = $recharge_info->request_date;
		$request_txn_no = $recharge_info->request_txn_no;
		$reference_txn_no = $recharge_info->reference_txn_no;
		$api_txn_no = $recharge_info->api_txn_no;
		$response_date = $recharge_info->response_date;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$operator_ref_no = $recharge_info->operator_ref_no;
		$recharge_ip = $recharge_info->recharge_ip;
		$is_refunded = $recharge_info->is_refunded;
		$is_complaint = $recharge_info->is_complaint;
	
		$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$uid."' ");
// 		echo json_encode($wallet);
    	echo json_encode($recharge_info);
		if($is_complaint == 'y') {
			$com_info = $db->queryUniqueObject("SELECT complaint_id FROM complaints WHERE txn_no = '".$recharge_id."' ");
			if($com_info) {
				$complaint_id = $com_info->complaint_id;
			} else {
				$complaint_id = '';
			}
		} else {
			$complaint_id = '';
		}
			
	} else {
		echo "ERROR,Invalid Recharge ID";
		exit();		
	}
	
} else {
	echo "ERROR,Invalid Transaction ID";
	exit();
}

?>

