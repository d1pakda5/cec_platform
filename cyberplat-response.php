<?php
/*
 * Last update by Sunil on 04-09-2017
 * callback
 */
include('config.php');
$content="";
foreach ($_GET as $key => $value) {
	$content.= $key."=".urldecode($value)."; ";	
}
$db->query("INSERT INTO apps_reverse_response (api_id, reverse_response_content, response_time) VALUES ('10', '".$content."', NOW())");

if(isset($_GET['CIPLTransID']) && $_GET['CIPLTransID']!='' && isset($_GET['DateTime']) && $_GET['DateTime']!='' && isset($_GET['DealerTransID']) && $_GET['DealerTransID']!='' ) {
	
	$api_txn_no=trim(mysql_real_escape_string($_GET['CIPLTransID']));
	$api_status=trim(mysql_real_escape_string($_GET['ErrorDesc']));
	$DateTime=trim(mysql_real_escape_string($_GET['DealerTransID']));
	$recharge_id=trim(mysql_real_escape_string($_GET['DealerTransID']));
	$ErrorDesc=trim(mysql_real_escape_string($_GET['ErrorDesc']));
	$ErrorCode=trim(mysql_real_escape_string($_GET['ErrorCode']));
	
	$recharge_info=$db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id='".$recharge_id."' ");
	if($recharge_info) {
		//operator row
		$operator_info=$db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$recharge_info->operator_id."'");
		$operator_ref_no=trim(mysql_real_escape_string($_GET['OperatorTransID']));
		
		//retailer row
		$agent_info=$db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$recharge_info->uid."'");
		if($agent_info) {
			
			if($api_status=='Success') {
				if($ErrorCode=='0')
				{
				$status='0';
				$status_details='Transaction Successful';	
				$operator_ref_no=trim(mysql_real_escape_string($_GET['OperatorTransID']));
				/*
				* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
				*/
				$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', api_status='', api_status_details='', response_date=NOW(), operator_ref_no='".$operator_ref_no."', is_callback='1' WHERE recharge_id='".$recharge_info->recharge_id."' ");
				
				if($recharge_info->is_callback=='0') {
					/*
					 * Update commission for all parent users
					 */	
					if($agent_info->user_type=='1') {				
						$sCommission=getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
					} else {
						$sCommission=getUsersCommission(trim($agent_info->dist_id), $operator_info->operator_id, $amount, 'r');
					}
						
					if($sCommission['rtCom']>'0') {
						$rt = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND transaction_term='RECHARGE' AND account_id='".$agent_info->uid."'");
						$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->uid."', '".$sCommission['rtCom']."', '".$rt->closing_balance."', NOW())");
					}
								
					//Commission Distributor
					if($sCommission['dsCom']>'0') {
						$db->query("START TRANSACTION");
						$ds=$db->queryUniqueObject("SELECT wallet_id,balance FROM apps_wallet WHERE uid='".$agent_info->dist_id."'");
						if($ds) {										
							$ds_close_balance=$ds->balance+$sCommission['dsCom'];
							$db->execute("UPDATE apps_wallet SET balance='".$ds_close_balance."' WHERE wallet_id='".$ds->wallet_id."'");
							$ts1=mysql_affected_rows();
							$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->dist_id."', '".$sCommission['dsCom']."', '".$ds_close_balance."', NOW())");
							if($ts1) {
								$commit=$db->query("COMMIT");
							} else {
								$db->query("ROLLBACK");
							}
						} else {
							$db->query("ROLLBACK");
						}
					}
					
					/*
					 * If Complaint then resolved
					 */
					$complaint_info=$db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."'");				
					if($complaint_info && $complaint_info->status == '0') {
						$remark = "Complaint closed, recharge successful @hook";
						$db->query("UPDATE complaints SET status = '1', refund_status = '2', refund_by = '0', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_info->complaint_id."' ");
						$db->execute("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
						$message = "Recharge already successful, Txn: ".$recharge_id.", ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ref. Id: ".$operator_ref_no;
						smsSendSingle($agent_info->mobile, $message, 'complaint_refund');
					}
				}
				}
			} elseif ($api_status=='Cancelled') {
				if($ErrorCode!='0')
				{
				$status='2';
				$status_details='Transaction Failed';
				$account_id2=$recharge_info->offline_uid;
							$trans_info2=$db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND account_id='".$account_id2."' ORDER BY transaction_date DESC");
							if($trans_info2) {				
								if($trans_info2->type=='dr') {
									$recharge_amount2=$recharge_info->amount;						
									$debit_amount2=$trans_info2->amount;	
									$wallet2 = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='20018105' ");													
											$new_close_balance2 = $wallet2->balance + $debit_amount2;
											$db->execute("UPDATE apps_wallet SET balance='".$new_close_balance2."' WHERE wallet_id='".$wallet2->wallet_id."' ");
											
	                                                $remark_new2 = "REVERT: $recharge_id, $account, $recharge_amount2, $debit_amount2, failed revert";
													$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '20018105', '20018105', 'cr', '".$debit_amount2."', '".$new_close_balance2."',  'FAILURE', '".$recharge_id."', '".$remark_new2."', '5', '20018105')");
								}
							}
				$offline_retailer=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and user_type='".$agent_info->user_type."' and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
				if($operator_info->operator_id=='20' && $amount>=90)
							{
								$request_txn_no = $recharge_id;
							
								$status_details = 'Transaction Submitted';
								
							$db->execute("UPDATE apps_recharge SET status='8',api_id='11',org_api_id='10', status_details='Transaction Submitted', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."' WHERE recharge_id='".$recharge_id."' ");
							//Error Code: Recharge Submitted
							$result_code = '308';
							}
											      
        				
                              else if($offline_retailer)
                                {
                                
        						$db->execute("UPDATE apps_recharge SET status='1', org_status='2',request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."' WHERE recharge_id='".$recharge_id."' ");
        						//Error Code: Recharge Pending
        					    $result_code = '301';
                                }
        									
                                                    	
				else {			
					$status='2';
				/*
				* Update response status for failed recharge
				*/
				$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), api_status='".$api_status."', is_callback='1' WHERE recharge_id='".$recharge_id."' ");				
				
				if($recharge_info->is_callback=='0') {
					/*
					 * Check if response is older than 5 minutes
					 */
					$resp_time=date("Y-m-d H:i:s", strtotime('-5 minutes'));
					if($recharge_info->request_date>$resp_time) {
						/*
						* Revert recharge amount to agent Use a refund function
						*/
						if($recharge_info->is_refunded=='n') {
							$account_id=$recharge_info->uid;
							$trans_info=$db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND account_id='".$account_id."' ORDER BY transaction_date DESC");
							if($trans_info) {				
								if($trans_info->type=='dr') {
									$recharge_amount=$recharge_info->amount;						
									$debit_amount=$trans_info->amount;
									$db->query("START TRANSACTION");
									$wallet=$db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");													
									$closing_balance=$wallet->balance+$debit_amount;
									$db->execute("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
									$ts1 = mysql_affected_rows();
									if($wallet && $ts1) {													
										$commit=$db->query("COMMIT");	
										if($commit) {													
											
											$remark_new="REVERT: $recharge_id, $account, $recharge_amount, $debit_amount, failed revert";										
											$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$closing_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");
											$ts_reference_id=$db->lastInsertedId();
											
											//If Complaint then resolved
											$complaint_info=$db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no='".$recharge_id."'");				
											if($complaint_info && $complaint_info->status=='0') {
												$remark = "Complaint closed, recharge failed @hook";
												$db->query("UPDATE complaints SET status='1', refund_status='1', refund_by='0', refund_date=NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_info->complaint_id."' ");
												$db->execute("UPDATE apps_recharge SET is_refunded='y', is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
												$message="Complaint Refund Successful, Txn: ".$recharge_id.", ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ur Bal: ".$closing_balance;
												smsSendSingle($agent_info->mobile, $message, 'complaint_refund');
											} else {
												$db->execute("UPDATE apps_recharge SET is_refunded='y' WHERE recharge_id='".$recharge_id."' ");										
												$message = "Transaction Failed, ".$operator_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_amount." Txn: ".$recharge_id.". Ur Bal: ".$closing_balance;
												smsSendSingle($agent_info->mobile, $message, 'recharge');											
											}
										}
											
									} else {
										$db->query("ROLLBACK");
									}
								}
							}
						} //End amount not refunded						
					} //End of 2 Minutes
				}				
			} 
		}
		}
			
			// End of api status
			
			//HIT BACK			
			if($agent_info->user_type=='1') {
				$setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id='".$agent_info->user_id."' ");
				if($setting_info->reverse_url!='') {
				
					$url_txid = $recharge_id;
					$url_status = $api_status;
					$offline_api=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and user_type='".$agent_info->user_type."' and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
					if($api_status == 'Success') {
						if($ErrorCode=='0')
						{
						$url_opref = $operator_ref_no;
						$url_msg = "Transaction Successful Amount Debited";
					}
					} else {
					    
                            if($operator_info->operator_id=='20' && $amount>=90)
								{
									$url_opref = "NA";
									$url_status = "SUBMITTED";
									$url_msg = 'Transaction Submitted';
								}
							elseif($offline_api)
                                {
                                  $url_opref = "NA";
                                  $url_status = "request send";
					               $url_msg = "Transaction Pending Amount Debited";                     
                                }	
            				else {	
            						$url_opref = "NA";
            						
            						$url_msg = "Transaction Failed Amount Reversed";
            					}
					        }
					$explodUrl = explode('?',$setting_info->reverse_url);				
					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$url_opref, 'msg'=>$url_msg, 'usertxn'=>$recharge_info->reference_txn_no));
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_exec ($ch);
					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);				
					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$agent_info->uid."', '".$recharge_info->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
					
				}
			}
			//END OF HITBACK
		}
	}

}
?>