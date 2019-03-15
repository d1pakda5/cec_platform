<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['account_id']) && $_POST['account_id'] != '' && isset($_POST['amount']) && $_POST['amount'] != '') {

	$account_id = htmlentities(addslashes($_POST['account_id']),ENT_QUOTES);	
	$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);	
	$transaction_id = htmlentities(addslashes($_POST['transaction_id']),ENT_QUOTES);	
	
	$db->query("START TRANSACTION");
	$wallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$account_id."' ");
	$closing_balance = $wallet->balance + $amount;					
	$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE uid = '".$account_id."' ");
	$ts1 = mysql_affected_rows();	
	if($wallet && $ts1) {
		$commit = $db->query("COMMIT");
		if($commit) {
			$db->execute("INSERT INTO `trans_deduct`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_status`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$account_id."', '0', 'cr', '".$amount."', '".$closing_balance."', 'REVERT', '".$transaction_id."', '', '0', '0', '".$_SESSION['admin']."') ");
			$db->query("UPDATE trans_deduct SET transaction_status = '0' WHERE transaction_id = '".$transaction_id."' ");
			echo "SUCCESS, Fund Revert successfully";
			exit();
		} else {
			echo "SUCCESS, Internal server error";
			exit();
		}
	} else {
		echo "ERROR, Transaction incomplete, try again";
		exit();
	}
} else {
	echo "ERROR, Invalid transaction id";
	exit();
}
?>