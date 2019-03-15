<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../../config.php');

if(isset($_POST['request_id']) && $_POST['request_id']!='') {
	$request_id = isset($_POST['request_id']) ? mysql_real_escape_string($_POST['request_id']) : 0;
	$request_info = $db->queryUniqueObject("SELECT * FROM dmt_activation_request WHERE dmt_request_id='".$request_id."' ");
	if($request_info) {
		$uid = $request_info->dmt_request_uid;
		$amount = $request_info->dmt_activation_charge;
		$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
		if(isset($_POST['action']) && $_POST['action']=='1') {
			
			$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."'");
			if($agent_info) {	
			
				$db->query("START TRANSACTION");
				$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance,cuttoff FROM apps_wallet WHERE uid='".$uid."' ");
				if(($wallet->balance-$wallet->cuttoff) >= $amount) {				
					
					/*
					* Debit Transaction
					*/
					$closing_balance = $wallet->balance - $amount;
					$db->query("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
					$agent_remark = "FUND : DMR ACTIVATION | CHARGES - $amount | AUTO TRANSACTION";
					$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', 'dr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$agent_remark."', '0', '".$_SESSION['admin']."') ");		
					
					/*
					* Credit Transaction
					*/
					$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
					$admin_closing_balance = $admin_wallet->balance + $amount;
					$db->query("UPDATE apps_admin_wallet SET balance = '".$admin_closing_balance."' WHERE admin_wallet_id='".$admin_wallet->admin_wallet_id."' ");
					$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '".$uid."', 'cr', '".$amount."', '".$admin_closing_balance."', 'FUND', '', '".$agent_remark."', '0', '".$_SESSION['admin']."') ");
					
					if($wallet) {
						$commit = $db->query("COMMIT");
						if($commit) {
							
							$db->execute("UPDATE `dmt_activation_request` SET `dmt_request_status`='1', `dmt_update_date`=NOW(), `dmt_update_remark`='".$remark."', `dmt_update_status`='1', `dmt_update_usertype`='0', `dmt_update_user`='".$_SESSION['admin']."' WHERE dmt_request_id='".$request_info->dmt_request_id."' ");
							$db->query("UPDATE apps_user SET is_money='a' WHERE uid='".$uid."' ");
							
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
				echo "Invalid user, Try again";
				exit();
			}
		} else if(isset($_POST['action']) && $_POST['action']=='2') {
			$db->execute("UPDATE `dmt_activation_request` SET `dmt_request_status`='1', `dmt_update_date`=NOW(), `dmt_update_remark`='".$remark."', `dmt_update_status`='2', `dmt_update_usertype`='0', `dmt_update_user`='".$_SESSION['admin']."' WHERE dmt_request_id='".$request_info->dmt_request_id."' ");
			echo "Updated successfully";
			exit();
		} else {
			echo "Please select a valid action!";
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