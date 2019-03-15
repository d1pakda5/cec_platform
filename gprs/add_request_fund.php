	<?php
	include('../config.php');
	if($_GET['rqstamt'] == '' || $_GET['addonamt'] == '' || $_GET['totalamt'] == '' || $_GET['remark'] == '') {
	echo "Oops, Some parameters are missing!";		
	} else {	
		$post_request_id = htmlentities(addslashes($_GET['request_id']),ENT_QUOTES);
		$manager_id = htmlentities(addslashes($_GET['manager_id']),ENT_QUOTES);
		if($_GET['action'] == '1') {	
			$request_info = $db->queryUniqueObject("SELECT * FROM fund_requests WHERE request_id = '".$post_request_id."' ");
			if($request_info) {
				if($request_info->status != '0') {
				echo "Transaction has been already processed!";
				}  else {
					$totalamt = htmlentities(addslashes($_GET['totalamt']),ENT_QUOTES);	
					$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
					if($admin_wallet->balance >= $totalamt) {
						$db->query("START TRANSACTION");
						
						
             			if($manager_id!==""||$manager_id!==null)
                            {
                                 /* Add daily sale entry*/
                            	date_default_timezone_set('Asia/Kolkata');
                            	$date= date("Y-m-d");
                            	$time= date("H:i:s"); 
                            	$manager = $manager_id;
                            	$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$manager."','".$request_info->request_user."','".$_GET['totalamt']."','cr')");
                            	
                            		/* End daily sale entry*/
                            }
                            else
                            {
                               
                            }
						
						
						
						/*
						* Debit Transaction
						*/
						$admin_closing_balance = $admin_wallet->balance - $totalamt;
						$db->query("UPDATE apps_admin_wallet SET balance = '".$admin_closing_balance."' WHERE admin_wallet_id = '".$admin_wallet->admin_wallet_id."' ");
						$admin_remark = "FUND | $request_info->transaction_ref_no | $request_info->your_bank_name | $totalamt";
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '".$request_info->request_user."', 'dr', '".$totalamt."', '".$admin_closing_balance."', 'FUND', '".$request_id."', '".$admin_remark."', '0', '1') ");
						
						/*
						* Credit Transaction
						*/					
						$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$request_info->request_user."' ");
						$closing_balance = $wallet->balance + $totalamt;
						$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
						$ts1 = mysql_affected_rows();
						$remark = "FUND | $request_info->transaction_ref_no | $request_info->your_bank_name | $totalamt";
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', 'cr', '".$totalamt."', '".$closing_balance."', 'FUND', '".$request_id."', '".$remark."', '0', '1') ");					
						$ts2 = mysql_affected_rows();
						$updated_ref_id = $db->lastInsertedId();
						
						if($wallet && $ts1 && $ts2) {
							$commit = $db->query("COMMIT");
							if($commit) {
								$db->execute("UPDATE `fund_requests` SET `updated_date` = NOW(), `updated_ref_id` = '".$updated_ref_id."', is_admin = '1', `updated_by` = '1', status = '".$_GET['action']."' WHERE request_id = '".$post_request_id."' ");
								$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$request_info->request_user."' ");
								mailFundTransfer($user_info->email, $user_info->fullname, $totalamt, $closing_balance, date("d-m-Y"));
							echo "Updated successfully";
							
							} else {
								echo "Transaction has been rollback due to internal error.";
							}
						} else {
						echo "Transaction has been rollback due to internal error.";
						}
					} else {
					echo "Insufficent Fund in account.";
					}
				}
			} else {
			echo "Oops, Some parameters are missing!";
			}
		} else if($_GET['action'] == '2') {
			$db->execute("UPDATE `fund_requests` SET `updated_date` = NOW(), is_admin = '1', `updated_by` = '1', status = '".$_GET['action']."' WHERE request_id = '".$post_request_id."' ");
			echo "Fund request has been rejected successfully";
		} else {
			echo "Please select a valid action to do transaction.";
		}	
	
}
?>