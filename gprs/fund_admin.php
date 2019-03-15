<?php
$exp_msg = explode("A", $msg_param);
$to_username = trim($exp_msg[0]);
$amount = trim($exp_msg[1]);
$fromUid = $user_info->uid;
$pin=$user_info->pin;
$admin_id=$user_info->admin_id;
$admin_mobile=$user_info->mobile;
$fromUserType = $user_info->user_type;



if($mobile == $to_username) {
	echo "Error, Self transfer not permitted";
	exit();
} else {
	
	$to_user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".$to_username."' AND status = '1' ");
	if($to_user_info) {		
		$amount = mysql_real_escape_string($amount);
		$toUid = $to_user_info->uid;
		$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
		if($admin_wallet->balance >= $amount) {	
					$is_balance = true;
				} else {
					$is_balance = false;
				}
		if($is_balance){
		/*
		* Start Transaction
		*/		
		$db->query("START TRANSACTION");	
		$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$toUid."' ");
		$admin_closing_balance = $admin_wallet->balance - $amount;
					$closing_balance = $wallet->balance + $amount;
					$to_type = "dr";
					$from_type = "cr";
					
		 if($manager_id!==""||$manager_id!==null)
		    {
		         /* Add daily sale entry*/
				date_default_timezone_set('Asia/Kolkata');
				$date= date("Y-m-d");
				$time= date("H:i:s"); 
				$manager = $manager_id;
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$manager."','".$toUid."','".$amount."','cr')");
				
					/* End daily sale entry*/
		    }
		   else
		   {
		       
		   }
				
			/*
			* Debit Transaction
			*/
			$db->query("UPDATE apps_admin_wallet SET balance = '".$admin_closing_balance."' WHERE admin_wallet_id = '".$admin_wallet->admin_wallet_id."' ");
			$ts1 = mysql_affected_rows();
// 			$remark = "GPRS FUND: $amount transfer by Mobile (admin)";
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '".$toUid."', 'dr', '".$amount."', '".$admin_closing_balance."', 'FUND', '', '".$remark."', '0', '".$admin_id."') ");
			/*
			* Credit Transaction
			*/
			$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
				$ts2 = mysql_affected_rows();
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', 'cr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$remark."', '0', '".$admin_id."') ");	
				
			if($wallet && $ts1 && $ts2) {
				$commit = $db->query("COMMIT");
				if($commit) {
				        $user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$toUid."' ");
						
						$message = smsFundTransfer($amount, SITENAME, $to_user_info->company_name);
					
						smsSendSingle($to_user_info->mobile, $message, 'fund_transfer');
					    smsSendSingle($admin_mobile, $message, 'fund_transfer');
						echo "Success, ".$amount." Rs has been successfully transfered to ".$to_user_info->company_name;
					exit();
				} else {
					echo "Error, Transaction not completed due to internal error.";
					exit();
				}
			} else {
				echo "Error, Transaction not completed due to internal error.";
				exit();
			}
			
		} else {
			echo "Error, Insufficient balance";
			exit();
		}
		echo "Error, Service is not available";
		exit();
	} else {
		echo "Error, Invalid Account details";
		exit();
	}
}