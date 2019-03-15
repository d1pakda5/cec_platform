<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$ip = $_SERVER['REMOTE_ADDR'];
if(isset($_POST['account']) && isset($_POST['amount']) && isset($_POST['operator']) && isset($_POST['pin'])) {	

	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$api_selected = $_POST['api'];
	$recharge_id = $_POST['recharge_id'];

// 	echo $api_selected;
// 	die;
	$mode = "WEB";
	$account = htmlentities(addslashes($_POST['account']),ENT_QUOTES);
	$amount = is_numeric($_POST['amount']) ? $_POST['amount'] : 0;
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$operator_code = $_POST['operator'];
	$reference_txn_no = '';
	$customer_account = isset($_POST['customer_account']) ? htmlentities(addslashes($_POST['customer_account']),ENT_QUOTES) : '';
	$dob = isset($_POST['dob']) ? htmlentities(addslashes($_POST['dob']),ENT_QUOTES) : '';
	$sub_division = isset($_POST['sub_division']) ? htmlentities(addslashes($_POST['sub_division']),ENT_QUOTES) : '';
	$billing_cycle = isset($_POST['bill_cycle']) ? htmlentities(addslashes($_POST['bill_cycle']),ENT_QUOTES) : '';
	$billing_unit = isset($_POST['billing_unit']) ? htmlentities(addslashes($_POST['billing_unit']),ENT_QUOTES) : '';
	$pc_number = isset($_POST['pc_number']) ? htmlentities(addslashes($_POST['pc_number']),ENT_QUOTES) : '';
	
	//BSNL LANDLINE SERVICE TYPE
	$bsnl_service_type = isset($_POST['bsnl_service_type']) ? htmlentities(addslashes($_POST['bsnl_service_type']),ENT_QUOTES) : '';
	
	$customer_name = isset($_POST['customer_name']) ? htmlentities(addslashes($_POST['customer_name']),ENT_QUOTES) : '';
	$customer_mobile = isset($_POST['customer_mobile']) ? htmlentities(addslashes($_POST['customer_mobile']),ENT_QUOTES) : '';
	$customer_email = isset($_POST['customer_email']) ? htmlentities(addslashes($_POST['customer_email']),ENT_QUOTES) : '';
	$customer_city = isset($_POST['customer_city']) ? htmlentities(addslashes($_POST['customer_city']),ENT_QUOTES) : '';
	
	$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,dist_id,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE user_id='6136' ");
	if($agent_info) {
		if($agent_info->pin == $pin) {
			include(DIR . '/library/recharge-request-offline.php');
		} else {
			//Error Code: User PIN not matched
			$result_code = '313';
		}	
	} else {
		//Error Code: User not found
		$result_code = '312';
	}
} else {
	//Error Code: Paramets are missing
	$result_code = '311';
}

if($result_code == '300') {
	$response_msg = "Transaction Successful Amount Debited";
} else if($result_code == '301') {
	$response_msg = "Transaction Processed Amount Debited";
} else if($result_code == '302') {
	$response_msg = "Transaction Failed Amount Reversed";
} else if($result_code == '303') {
	$response_msg = "Transaction Failed Amount Refunded";
} else if($result_code == '304') {
	$response_msg = "Transaction Failed Amount Reversed";
} else if($result_code == '305') {
	$response_msg = "Transaction Pending Amount Debited";
} else if($result_code == '306') {
	$response_msg = "Error, Request failed. Try Again";
} else if($result_code == '307') {
	$response_msg = "Transaction Processed Amount Debited";
} else if($result_code == '308') {
	$response_msg = "Transaction Submitted Amount Debited";
} else if($result_code == '309') {
	$response_msg = "NA";
} else if($result_code == '310') {
	$response_msg = "Error, Duplicate recharge try after 2 Hours";
} else if($result_code == '311') {
	$response_msg = "Error, Parameters are missing";
} else if($result_code == '312') {
	$response_msg = "Error, Invalid User";
} else if($result_code == '313') {
	$response_msg = "Error, Invalid User Pin";
} else if($result_code == '314') {
	$response_msg = "Error, Invalid Operator";
} else if($result_code == '315') {
	$response_msg = "Error, Service Downtime. Try Later";
} else if($result_code == '316') {
	$response_msg = "Error, API Downtime. Try Later";
} else if($result_code == '317') {
	$response_msg = "Error, Operator Downtime. Try Later";
} else if($result_code == '318') {
	$response_msg = "Error, Invalid Amount.";
} else if($result_code == '319') {
	$response_msg = "Error, Insufficiant Balance";
} else if($result_code == '320') {
	$response_msg = "NA";
} else if($result_code == '321') {
	$response_msg = "NA";
} else {
	$response_msg = "Error";
}
echo ("<SCRIPT LANGUAGE='JavaScript'>
		window.alert('".$response_msg."')
		window.location.href='recharge2.php';
		</SCRIPT>");
?>