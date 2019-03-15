<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$product_id = isset($_POST['productid']) && $_POST['productid']!='' ? mysql_real_escape_string($_POST['productid']) : 0;
$product_name = isset($_POST['product_name']) && $_POST['product_name']!='' ? mysql_real_escape_string($_POST['product_name']) : '';
if(isset($_POST['account']) && isset($_POST['amount']) && isset($_POST['operator']) && isset($_POST['pin'])) {
	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$mode = "WEB";
	$account = htmlentities(addslashes($_POST['account']),ENT_QUOTES);
	$amount = is_numeric($_POST['amount']) ? $_POST['amount'] : 0;
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$operator_code = $_POST['operator'];
	$reference_txn_no = '';	
	$customer_name = isset($_POST['customer_name']) ? htmlentities(addslashes($_POST['customer_name']),ENT_QUOTES) : '';
	$customer_mobile = isset($_POST['account']) ? htmlentities(addslashes($_POST['account']),ENT_QUOTES) : '';
	$customer_email = isset($_POST['customer_email']) ? htmlentities(addslashes($_POST['customer_email']),ENT_QUOTES) : '';
    $customer_address = isset($_POST['address']) ? htmlentities(addslashes($_POST['address']),ENT_QUOTES) : '';
    $customer_city = isset($_POST['city']) ? htmlentities(addslashes($_POST['city']),ENT_QUOTES) : '';
    $customer_pincode = isset($_POST['pincode']) ? htmlentities(addslashes($_POST['pincode']),ENT_QUOTES) : '';
    $customer_state = isset($_POST['state']) ? htmlentities(addslashes($_POST['state']),ENT_QUOTES) : '';
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$_SESSION['retailer']."' ");
	if($agent_info) {
		if($agent_info->pin==$pin) {
			include(DIR . '/library/recharge-request.php');
			if($status!='2') {
				$message = getProductsWelcome($product_name,$amount,$recharge_id);
				if($customer_mobile!='') {
					smsSendSingle($customer_mobile, $message, 'recharge');
				}
			}
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
	$response_msg = "Transaction successfully submitted, Amount Debited";
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
		window.location.href='products.php';
		</SCRIPT>");
?>