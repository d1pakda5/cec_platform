<?php
include("/home/recharge/public_html/config.php");
ini_set('memory_limit','128M');
include("/home/recharge/public_html/system/php-excel.class.php");

$aFrom = date("Y-m-d 00:00:00");
$aTo = date("Y-m-d 23:59:59");
$sWhere = "WHERE (rqst.user_type = '3' or rqst.user_type='6') ";
$sWhere .= " AND rqst.status = '1' ";
$statement = "fund_requests rqst LEFT JOIN apps_user user ON rqst.request_user = user.uid $sWhere ORDER BY request_date DESC";
$query = $db->query("SELECT rqst.* FROM {$statement}");
					while($result = $db->fetchNextObject($query)) {
					setlocale(LC_MONETARY, 'en_IN');
					$amount = money_format('%!i', $result->amount);
				// 	echo $amount;
			
           $msg=$db->queryUniqueObject("SELECT * FROM mobile_sms WHERE msg like '%".$amount."%'  and (provider like '%".$result->to_bank_account."%' Or msg like '%".$result->transaction_ref_no."%' )");
        //   echo "SELECT * FROM mobile_sms WHERE msg like '%".$amount."%'  and (provider like '%".$result->to_bank_account."%' Or msg like '%".$result->transaction_ref_no."%' )";
           if($msg)
           {
           	// 	print_r($msg);
           	// 	print_r($result);
           	// 	die;
           	
           	$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
					if($admin_wallet->balance >= $result->amount) {
						$db->query("START TRANSACTION");
						
						
           	            /* Add daily sale entry*/
        				date_default_timezone_set('Asia/Kolkata');
        				$date= date("Y-m-d");
        				$time= date("H:i:s"); 
        				$admin_id = $db->queryUniqueValue("SELECT assign_id FROM apps_user WHERE uid ='".$result->request_user."')");
        				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$result->request_user."','".$result->amount."','cr')");
            			/* End daily sale entry*/
					
					
					    /*
						* Credit Transaction
						*/					
						$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$result->request_user."' ");
						$closing_balance = $wallet->balance + $result->amount;
						$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
						$ts1 = mysql_affected_rows();
						$remark = "FUND |Auto Success | $result->transaction_ref_no | $result->your_bank_name | $result->amount";
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', 'cr', '".$result->amount."', '".$closing_balance."', 'FUND', '".$result->request_id."', '".$remark."', '0', '1') ");					
						$ts2 = mysql_affected_rows();
						$updated_ref_id = $db->lastInsertedId();
						
						if($wallet && $ts1 && $ts2) {
							$commit = $db->query("COMMIT");
							if($commit) {
								$db->execute("UPDATE `fund_requests` SET `updated_date` = NOW(), `updated_ref_id` = '".$updated_ref_id."', is_admin = '1', `updated_by` = '1', status = '1' WHERE request_id = '".$request->request_id."' ");
								$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$result->request_user."' ");
								mailFundTransfer($user_info->email, $user_info->fullname, $result->amount, $closing_balance, date("d-m-Y"));
								
							} else {
								// $error = 4;
							}
						} else {
				// 			$error = 4;
						}
					    
					    
					    
					}
					
					
           }


}
?>