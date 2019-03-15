<?php
$agent_info = $user_info;
$exp_msg = explode("A", $msg_param);
$mode = "GPRS";
$account = trim(mysql_real_escape_string($exp_msg[0]));
$amount = trim(mysql_real_escape_string($exp_msg[1]));
$operator_code = $code;
$reference_txn_no = '';
$customer_account = $customer_account;
$dob = $dob;
$sub_division =$sub_division;
$bsnl_service_type=$bsnl_service_type;
$billing_cycle = $billing_cycle;
$billing_unit = $billing_unit;
$pc_number = $pc_number;	
$customer_name = '';
$customer_mobile = '';
$customer_email = '';
$customer_city = '';
if($agent_info->user_type == '5'|| $agent_info->user_type == '6') {
	include(DIR . '/library/recharge-request.php');
} else {
	$result_code = '321';
}
if($result_code == '300') {
	$response_msg = "Transaction Successful Amount Debited, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
} else if($result_code == '301') {
	$response_msg = "Transaction Successful Amount Debited, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
	//.". Ur Current balance ".$close_balance
} else if($result_code == '302') {
	$response_msg = "Transaction Failed Amount Reversed, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
} else if($result_code == '303') {
	$response_msg = "Transaction Failed Amount Reversed";
} else if($result_code == '304') {
	$response_msg = "Transaction Failed Amount Reversed, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
} else if($result_code == '305') {
	$response_msg = "Transaction Pending Amount Debited";
} else if($result_code == '306') {
	$response_msg = "Error, Request failed. Try Again";
} else if($result_code == '307') {
	$response_msg = "Transaction Processed Amount Debited, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
} else if($result_code == '308') {
	$response_msg = "Transaction Submitted Amount Debited, ".$operator_info->operator_name.", ".$account.", ".$amount." Txn: ".$recharge_id.". Ur Current balance ".$close_balance;
} else if($result_code == '309') {
	$response_msg = "NA";
} else if($result_code == '310') {
	$response_msg = "Error, Duplicate Entry";
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
	$response_msg = "Error, Service not available";
} else {
	$response_msg = "Error";
}
echo $response_msg;
exit();