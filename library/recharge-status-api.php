<?php
include("../config.php");
$request_id = isset($_GET['recharge_id']) ? mysql_real_escape_string($_GET['recharge_id']) : 0;
$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id = '".$request_id."' ");
if($recharge_info) {
	$recharge_id = $recharge_info->recharge_id;		
	$txn_no = $recharge_info->recharge_id;
	$api_id = $recharge_info->api_id;
	$service_type = $recharge_info->service_type;
	$operator_id = $recharge_info->operator_id;
	$account_no = $recharge_info->account_no;
	$amount = $recharge_info->amount;
	$surcharge = $recharge_info->surcharge;
	$status = $recharge_info->status;
	$status_details = $recharge_info->status_details;
	$request_date = $recharge_info->request_date;
	$request_txn_no = $recharge_info->request_txn_no;
	$response_date = $recharge_info->response_date;
	$api_txn_no = $recharge_info->api_txn_no;
	$api_status = $recharge_info->api_status;
	$api_status_details = $recharge_info->api_status_details;
	$operator_ref_no = $recharge_info->operator_ref_no;
	$recharge_ip = $recharge_info->recharge_ip;
	$is_refunded = $recharge_info->is_refunded;
	$check_api = true;		
	if($check_api == true) {
		if($api_id == '1') {
			include(DIR . "/library/status-egpay-api.php");
		} else if($api_id == '2') {
			include(DIR . "/library/status-royal-capital-api.php");
		} else if($api_id == '3') {
			include(DIR . "/library/status-achariya-api.php");
		} else if($api_id == '4') {
			include(DIR . "/library/status-modem-api.php");
		} else if($api_id == '5') {
			include(DIR . "/library/status-modem-roundpay-api.php");
		} else if($api_id == '6') {
			include(DIR . "/library/status-roundpay-api.php");
		} else if($api_id == '7') {
			include(DIR . "/library/status-exioms-api.php");
		} else if($api_id == '8') {
			include(DIR . "/library/status-pay-manthra-api.php");
		} else if($api_id == '9') {
			include(DIR . "/library/status-ambika-api.php");
		} else if($api_id == '10') {
			include(DIR . "/library/status-cyberplat-api.php");
		} else if($api_id == '11') {
			include(DIR . "/library/status-offline-api.php");
		}
	}		
	$return = array('api_status'=>$api_status, 'api_status_details'=>$api_status_details, 'operator_ref_no'=>$operator_ref_no);
	echo json_encode($return);
	exit();
		
} else {
	echo "ERROR,Invalid Recharge ID";
	exit();		
}
?>