<?php
//ST7798993278A3090
$exp_msg = explode("A", $msg_param);
$to_username = isset($exp_msg[1]) ? trim($exp_msg[0]) : 0;
$amount = isset($exp_msg[1]) ? trim($exp_msg[1]) : 0;
$fromUid = $user_info->uid;
$fromUserType = $user_info->user_type;

if($mobile == $to_username) {
	$message = "Error, Self transfer not permitted";
	smsSendSingle($user_info->mobile, $message, 'fund_transfer');
	exit();
} else {
	if($user_info->user_type == "3") {
		$sParent = " AND mdist_id = '".$fromUid."' ";
	} else if($user_info->user_type == "4") {
		$sParent = " AND dist_id = '".$fromUid."' ";
	} else {
		echo "Error, Service is not available1";
		exit();
	}		
	$to_user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".$to_username."' $sParent AND status = '1' ");
	if($to_user_info) {		
		$amount = mysql_real_escape_string($amount);
		$toUid = $to_user_info->uid;
		/*
		* Start Transaction
		*/		
		$db->query("START TRANSACTION");			
		$fromWallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$fromUid."' ");
		if($fromWallet && (($fromWallet->balance - $fromWallet->cuttoff) >= $amount)) {				
			/*
			* Debit Transaction
			*/
			$from_closing_balance = $fromWallet->balance - $amount;
			$db->query("UPDATE apps_wallet SET balance = '".$from_closing_balance."' WHERE wallet_id = '".$fromWallet->wallet_id."' ");
			$ts1 = mysql_affected_rows();
			$remark = "SMS FUND: $amount transfer by $user_info->company_name";
			$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$fromUid."', '".$toUid."', 'dr', '".$amount."', '".$from_closing_balance."', 'FUND', '', '".$remark."', '".$fromUserType."', '".$fromUid."') ");		
			/*
			* Credit Transaction
			*/
			$toWallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$toUid."' ");				
			$closing_balance = $toWallet->balance + $amount;				
			$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$toWallet->wallet_id."' ");
			$ts2 = mysql_affected_rows();
			$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$toUid."', '".$fromUid."', 'cr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$remark."', '".$fromUserType."', '".$fromUid."') ");				
			if($ts1 && $ts2) {
				$commit = $db->query("COMMIT");
				if($commit) {
					$message = smsFundTransfer($amount, $user_info->company_name, $to_user_info->company_name);
					smsSendSingle($user_info->mobile, $message, 'fund_transfer');
					smsSendSingle($to_user_info->mobile, $message, 'fund_transfer');
					echo "Success, ".$amount." Rs has been successfully transfered to ".$to_user_info->company_name.", Ur Current balance ".$from_closing_balance;
					exit();
				} else {
					$message = "Error, Transaction Failed";
					smsSendSingle($user_info->mobile, $message, 'fund_transfer');
					exit();
				}
			} else {
				$message = "Error, Transaction Failed";
				smsSendSingle($user_info->mobile, $message, 'fund_transfer');
				exit();
			}
			
		} else {
			$message = "Error, Insufficient balance";
			smsSendSingle($user_info->mobile, $message, 'fund_transfer');
			exit();
		}
		echo "Error, Service is not available";
		exit();
	} else {
		echo "Error, Invalid Account details";
		exit();
	}
}