<?php
include("../config.php");
$content = $_SERVER['REQUEST_METHOD'].": ";
foreach ($_GET as $key => $value) {
	$content .= $key."=".urldecode($value)."; ";	
}
$db->query("INSERT INTO apps_reverse_response (reverse_response_id, api_id, reverse_response_content, response_time) VALUES ('', '7', '".$content."', NOW())");
//GET :: optxid=608608; accountid=20161062815; transtype=2; 
if(isset($_GET['transtype']) && $_GET['transtype'] != '' && isset($_GET['accountid']) && $_GET['accountid'] != '') {
	
	$api_status = trim(mysql_real_escape_string($_GET['transtype']));
	$api_txn_no = trim(mysql_real_escape_string($_GET['accountid']));
	
	$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE api_txn_no = '".$api_txn_no."' AND api_id = '7' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;
		$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id = '".$recharge_info->operator_id."'");	
		$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$recharge_info->uid."' ");
		if($agent_info) {			
			if($api_status == '1') {
				$status = '0';
				$status_details = 'Transaction Successful';	
				$operator_ref_no = trim(mysql_real_escape_string($_GET['optxid']));
				/*
				* Update recharge response
				*/
				$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', response_date = NOW(), operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_id."' ");
				/*
				* Update commission for all parent users
				*/		
				//$sCommission = getUserCommission(trim($agent_info->mdist_id), $operator_info->operator_id, $recharge_info->amount);	
					
				if($agent_info->user_type == '1') {				
					$sCommission = getUserCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
				} else {
					$sCommission = getUserCommission(trim($agent_info->mdist_id), $operator_info->operator_id, $amount, 'r');
				}
					
				if($sCommission['rtCom'] > '0') {
					$rt = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND transaction_term = 'RECHARGE' AND account_id = '".$agent_info->uid."' ");
					$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->uid."', '".$sCommission['rtCom']."', '".$rt->closing_balance."', NOW())");
				}			
				
				//Commission Distributor
				if($sCommission['dsCom'] > '0') {
					$db->query("START TRANSACTION");
					$ds = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->dist_id."' ");
					if($ds) {										
						$ds_close_balance = $ds->balance + $sCommission['dsCom'];
						$db->execute("UPDATE apps_wallet SET balance='".$ds_close_balance."' WHERE wallet_id='".$ds->wallet_id."'");
						$ts1 = mysql_affected_rows();
						$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->dist_id."', '".$sCommission['dsCom']."', '".$ds_close_balance."', NOW())");
						if($ts1) {
							$commit = $db->query("COMMIT");
						} else {
							$db->query("ROLLBACK");
						}
					} else {
						$db->query("ROLLBACK");
					}
				}
				
				//Commission Master Distributor
				if($sCommission['mdCom'] > '0') {
					$db->query("START TRANSACTION");
					$md = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->mdist_id."' ");
					if($md) {										
						$md_close_balance = $md->balance + $sCommission['mdCom'];
						$db->execute("UPDATE apps_wallet SET balance='".$md_close_balance."' WHERE wallet_id='".$md->wallet_id."'");
						$ts2 = mysql_affected_rows();
						$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->mdist_id."', '".$sCommission['mdCom']."', '".$md_close_balance."', NOW())");
						if($ts2) {
							$commit = $db->query("COMMIT");
						} else {
							$db->query("ROLLBACK");
						}										
					} else {
						$db->query("ROLLBACK");
					}
				}
				
				//If Complaint then resolved
				$complaint_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."'");				
				if($complaint_info && $complaint_info->status == '0') {
					$remark = "Complaint closed, recharge successful @hook";
					$db->query("UPDATE complaints SET status = '1', refund_status = '2', refund_by = '0', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_info->complaint_id."' ");
					$db->execute("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
					$message = "Recharge already successful, Txn: ".$recharge_id.", ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ref. Id: ".$operator_ref_no;
					smsSendSingle($agent_info->mobile, $message, 'complaint_refund');
				}	
				
			} else if ($api_status == '2') {
				$status = '2';
				$status_details = 'Transaction Failed';						
				/*
				* Update response status for failed recharge
				*/
				$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', response_date = NOW(), api_status = '".$api_status."' WHERE recharge_id = '".$recharge_id."' ");
				
				$resp_time = date("Y-m-d H:i:s", strtotime('-2 minutes'));
				if($recharge_info->request_date > $resp_time) {
					/*
					* Revert recharge amount to agent Use a refund function
					*/
					if($recharge_info->is_refunded == 'n') {
						$account_id = $recharge_info->uid;
						$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND account_id = '".$account_id."' ORDER BY transaction_date DESC");
						if($trans_info) {				
							if($trans_info->type == 'dr') {
								$recharge_amount = $recharge_info->amount;						
								$debit_amount = $trans_info->amount;
								$db->query("START TRANSACTION");
								$wallet = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->uid."' ");													
								$close_balance = $wallet->balance + $debit_amount;
								$db->execute("UPDATE apps_wallet SET balance = '".$close_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
								$ts1 = mysql_affected_rows();
								if($wallet && $ts1) {													
									$commit = $db->query("COMMIT");	
									if($commit) {													
										$remark_new = "REVERT: $recharge_id, $account, $recharge_amount, $debit_amount, failed revert";
										$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");
										
										//If Complaint then resolved
										$complaint_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."'");				
										if($complaint_info && $complaint_info->status == '0') {
											$remark = "Complaint closed, recharge failed @hook";
											$db->query("UPDATE complaints SET status = '1', refund_status = '1', refund_by = '0', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_info->complaint_id."' ");
											$db->execute("UPDATE apps_recharge SET is_refunded = 'y', is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
											$message = "Complaint Refund Successful, Txn: ".$recharge_id.", ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ur Bal: ".$closing_balance;
											smsSendSingle($agent_info->mobile, $message, 'complaint_refund');
										} else {
											$db->execute("UPDATE apps_recharge SET is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");										
											$message = "Transaction Failed, ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_amount." Txn: ".$recharge_id.". Ur Bal: ".$close_balance;
											smsSendSingle($agent_info->mobile, $message, 'recharge');											
										}
										
									}
										
								} else {
									$db->query("ROLLBACK");
								}
							}
						}
					}
					//End amount not refunded
				}
				//End of 2 Minutes
			}
			//End of api status
		}
	}
}
?>