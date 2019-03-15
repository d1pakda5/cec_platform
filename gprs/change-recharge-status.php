<?php

include('../config.php');
	header("Access-Control-Allow-Origin: *");
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$status = isset($_GET['status']) && $_GET['status']!='' ? mysql_real_escape_string($_GET['status']) : 0;
$recharge_id = isset($_GET['recharge_id']) && $_GET['recharge_id']!='' ? mysql_real_escape_string($_GET['recharge_id']) : 0;
$operator_ref_no = isset($_GET['operator_ref_no']) && $_GET['operator_ref_no']!='' ? mysql_real_escape_string($_GET['operator_ref_no']) : '';
$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id='".$recharge_id."' ");
if($recharge_info) {
	$status_details = "";
	if($status == '0') {			
		$status_details = "Transaction Successful Amount Debited";
		$db->query("UPDATE apps_recharge SET is_status_changed='1' WHERE recharge_id='".$recharge_id."' ");
		
		 $wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance,cuttoff FROM apps_wallet WHERE uid='".$recharge_info->uid."' ");
        	$amount=$recharge_info->amount;
            $closing_balance = $wallet->balance-$amount;
            $txn_remark = "PENDING DEDUCT: $amount | $closing_balance";	
        	$db->execute("INSERT INTO `transactions_adm`(`txndate`, `txntouid`, `txntype`, `txnamount`, `closingbalance`, `txnterm`, `txnrefno`, `remark`, `txnuser`) VALUES (NOW(), '".$wallet->uid."', 'dr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$txn_remark."', '".$_SESSION['admin']."')");
	} else if($status == '1') {
		$status_details = "Transaction Pending Amount Debited";
	} else if($status == '2') {
		$status_details = "Transaction Failed Amount Reversed";
	} else if($status == '3') {
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '4') {
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '5') {
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '6') {
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '7') {
		$status_details = "Transaction Processed Amount Debited";
	} else if($status == '8') {
		$status_details = "Transaction Submitted Amount Debited";
	} else {
		$status_details = "Transaction Successful!";
	}
	$db->query("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$recharge_id."' ");
   
	echo "Recharge has been saved as success!";
	
	exit();
} else {
	echo "ERROR, Invalid recharge ID.";
	exit();
}