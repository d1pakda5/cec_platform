<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');

if(isset($_POST['uid']) && $_POST['uid']!='' && isset($_POST['amount']) && $_POST['amount']!='') {
	$uid = $_SESSION['retailer_uid'];
	$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);	
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."'");
	if($agent_info) {
		$option_info = $db->queryUniqueObject("SELECT * FROM dmt_options WHERE dmt_option_name='retailer_activation_type' ");
		if($option_info && $option_info->dmt_option_value=='yes') {
		
			$db->query("START TRANSACTION");
			$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance,cuttoff FROM apps_wallet WHERE uid='".$uid."' ");
			if(($wallet->balance-$wallet->cuttoff) >= $amount) {
				
				
				/*
				* Debit Transaction
				*/
				$closing_balance = $wallet->balance - $amount;
				$db->query("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
				$agent_remark = "FUND : DMR ACTIVATION | CHARGES - $amount | AUTO TRANSACTION";
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', 'dr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$agent_remark."', '5', '".$wallet->uid."') ");		
				
				/*
				* Credit Transaction
				*/
				$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
				$admin_closing_balance = $admin_wallet->balance + $amount;
				$db->query("UPDATE apps_admin_wallet SET balance = '".$admin_closing_balance."' WHERE admin_wallet_id='".$admin_wallet->admin_wallet_id."' ");
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '".$uid."', 'cr', '".$amount."', '".$admin_closing_balance."', 'FUND', '', '".$agent_remark."', '0', '".$wallet->uid."') ");
				
				if($wallet) {
					$commit = $db->query("COMMIT");
					if($commit) {
						$remark = "";
						$db->execute("INSERT INTO `dmt_activation_request`(`dmt_request_id`, `dmt_request_date`, `dmt_request_uid`, `dmt_activation_charge`, `dmt_request_status`, `dmt_update_date`, `dmt_update_remark`, `dmt_update_status`, `dmt_update_usertype`, `dmt_update_user`) VALUES ('', NOW(), '".$uid."', '".$amount."', '1', NOW(), '".$remark."', '1', '5', '".$uid."') ");
						$db->query("UPDATE apps_user SET is_money='a' WHERE uid='".$uid."' ");
						
						$message = smsFundDeduct($amount, $agent_info->company_name, SITENAME);
						smsSendSingle($agent_info->mobile, $message, 'fund_transfer');
						if($agent_info->email!='') {
							mailFundTransfer($agent_info->email, $agent_info->company_name, $amount, $closing_balance, date("d-m-Y"));
						}
						
						echo "Success, Updated Successful";
						exit();
						
					} else {
						echo "Error, Internal server error";
						exit();
					}
				} else {
					echo "Error, Transaction roll back";
					exit();
				}
			} else {
				echo "Error, Insufficient balance in your account";
				exit();
			}
		} else {
			$db->execute("INSERT INTO `dmt_activation_request`(`dmt_request_id`, `dmt_request_date`, `dmt_request_uid`, `dmt_activation_charge`, `dmt_request_status`) VALUES ('', NOW(), '".$uid."', '".$amount."', '0') ");
			echo "Success, Request submitted succesfully";
			exit();
		}
	
	} else {
		echo "Invalid detail, Try again";
		exit();
	}
	
} else {
	echo "Invalid parameters, Try again";
	exit();
}
?>