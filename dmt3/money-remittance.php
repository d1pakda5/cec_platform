<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

if($amount >= '1' && $amount <= '2500') {
	$operator_code = 'DMTS1';
	$amount_all = $amount + 6;
} else if($amount >= '2501' && $amount <= '5000') {
	$operator_code = 'DMTS2';
	$amount_all =  $amount + 6;
} else {
	$operator_code = 'INVALIDOPERAOTRREQUEST';
	$amount_all = $amount;
}

if($agent_info) {  
    
    $is_money=$db->queryUniqueObject("SELECT is_money from apps_admin where admin_id='".$agent_info->assign_id."'");

	$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_code='".$operator_code."' ");
    if($is_money!=i)
    {
    	if($operator_info) {
    		if($operator_info->ser_status=='1') {
    			if($operator_info->api_status=='1') {
    				if($operator_info->status=='1') {
    					if($agent_info->user_type=='1') {				
    						$sCommission = getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
    					} else {
    						$sCommission = getUsersCommission(trim($agent_info->dist_id), $operator_info->operator_id, $amount, 'r');
    					}
    					
    					if($sCommission['surcharge']=='y') {
    						$rch_comm_type = '1';
    						$rch_comm_value = $sCommission['samount'];
    					} else {
    						$rch_comm_type = '0';
    						$rch_comm_value = $sCommission['rtCom'];
    					}
    										
    					$api_id = $operator_info->api_id;
    					
    					$isfund = $db->queryUniqueObject("SELECT wallet_id,balance,cuttoff FROM apps_wallet WHERE user_id='".$agent_info->user_id."' ");
    					if($isfund && (($isfund->balance - $isfund->cuttoff) >= ($amount + $sCommission['samount']))) {
    						
    						//Send to check api
    						include(DIR."/dmt3/money-remittance-check.php");
    						
    						if(isset($dmCheckStatus) && $dmCheckStatus=="0") {
    							
    							$status = '1';
    							
    							//Insert into recharge table
    							$db->execute("INSERT INTO `apps_recharge`(`recharge_id`, `uid`, `recharge_mode`, `api_id`, `service_type`, `operator_id`, `account_no`, `amount`, `surcharge`, `status`, `status_details`, `request_date`, `reference_txn_no`, `recharge_ip`) VALUES ('', '".$agent_info->uid."', '".$mode."', '".$api_id."', '".$operator_info->service_name."', '".$operator_info->operator_id."', '".$account."', '".$amount."', '".$sCommission['samount']."', '".$status."', '', NOW(), '".$reference_txn_no."', '".$ip."')");
    							$recharge_id = $db->lastInsertedId();
    							
    							//insert reference parameter if required
    							if($operator_info->service_type=='9') {
    								
    							}
    							
    							$db->query("START TRANSACTION");
    							$wallet = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");
    							$tax = getUserGstTxns($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
    							if($sCommission['surcharge']=='y') {
    								$debit_amount = $amount + $tax['total_debit'];
    							} else {
    								$debit_amount = $amount - $tax['total_debit'];
    							}
    							$close_balance = $wallet->balance - $debit_amount;
    							$db->query("UPDATE apps_wallet SET balance='".$close_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
    							$ts1 = mysql_affected_rows();
    							if($wallet && $ts1) {
    								$commit = $db->query("COMMIT");
    								if($commit) {
    									$remark = "DMT3 Remittance: $recharge_id, $account, $amount, $debit_amount";
    									$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'dr', '".$debit_amount."', '".$close_balance."',  'RECHARGE', '".$recharge_id."', '".$remark."', '5', '".$agent_info->uid."')");
    									$transaction_id = $db->lastInsertedId();
    									//Insert GST
    									$db->execute("INSERT INTO `gst_transactions`(`uid`, `recharge_id`, `operator_id`, `transaction_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`, `tds_value`, `tds_rate`, `tds_amount`, `trans_date`) VALUES ('".$agent_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$transaction_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."', '".$tax['tds_value']."', '".$tax['tds_rate']."', '".$tax['tds_amount']."', NOW())");
    									//Send to payment api
    									include(DIR."/dmt3/money-remittance-pay.php");
    									
    									if($status=='0') {
    										
    										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
    										
    										/*
    										* Update commission for all parent users
    										*/
    										if($sCommission['surcharge']=='n' && $sCommission['rtCom'] > '0') {
    											$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->uid."', '".$sCommission['rtCom']."', '".$close_balance."', NOW())");
    										}										
    										if($agent_info->user_type!='1') {								
    											//Commission Distributor
    											if($sCommission['dsCom'] > '0') {
    												$db->query("START TRANSACTION");
    												$rDs = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->dist_id."' ");
    												if($rDs) {										
    													$ds_close_balance = $rDs->balance + $sCommission['dsCom'];
    													$db->execute("UPDATE apps_wallet SET balance='".$ds_close_balance."' WHERE wallet_id='".$rDs->wallet_id."'");
    													$ts2 = mysql_affected_rows();
    													$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->dist_id."', '".$sCommission['dsCom']."', '".$ds_close_balance."', NOW())");
    													if($ts2) {
    														$commit = $db->query("COMMIT");
    													} else {
    														$db->query("ROLLBACK");
    													}
    												} else {
    													$db->query("ROLLBACK");
    												}
    											}
    										}
    									}
    									elseif($status=='2') {										
    										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");										
    										//Revert Amount for beneficiary validation
    										$db->query("START TRANSACTION");													
    										$wallet1 = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");													
    										$new_close_balance = $wallet1->balance + $debit_amount;
    										$db->execute("UPDATE apps_wallet SET balance='".$new_close_balance."' WHERE wallet_id='".$wallet1->wallet_id."' ");
    										$ts4 = mysql_affected_rows();
    										if($wallet1 && $ts4) {													
    											$commit = $db->query("COMMIT");	
    											if($commit) {													
    												$remark_new = "DMT3 REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
    												$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$new_close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");												
    												/*
    												* Update response status for failed recharge
    												*/
    												$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', is_refunded='y' WHERE recharge_id='".$recharge_id."' ");												
    											}												
    										} else {
    											$db->query("ROLLBACK");
    										}				
    									} else {									
    										$db->execute("UPDATE apps_recharge SET status='1', status_details='na', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
    									}
    									//Update Output
    									if(isset($output)) {
    										$db->execute("INSERT INTO `apps_response_log`(`log_id`, `txn_no`, `api_id`, `http_response_code`, `http_response_content`, `update_date`) VALUES ('', '".$recharge_id."', '".$api_id."', '".$http_code."', '".mysql_real_escape_string($output)."', NOW())");
    									}
    								} else {
    									$jsDmtResponse = '{"RESP_CODE":"27","RESP_MSG":"Request processed"}';
    								}
    							} else {							
    								$db->query("ROLLBACK");
    								$db->execute("UPDATE apps_recharge SET status='6', status_details='Transaction cancelled', response_date=NOW() WHERE recharge_id='".$recharge_id."' ");
    								$jsDmtResponse = '{"RESP_CODE":"26","RESP_MSG":"Request validation cancelled"}';
    							}
    						} else if(isset($dmt_check_status) && $dmt_check_status=="1") {
    							$jsDmtResponse = $jsDmtResponse;							
    						} else {
    							$jsDmtResponse = $jsDmtResponse;			
    						}						
    					} else {						
    						$jsDmtResponse = '{"RESP_CODE":"25","RESP_MSG":"Insufficient Balance in agent account"}';									
    					}
    				} else {
    					$jsDmtResponse = '{"RESP_CODE":"29","RESP_MSG":"Operator Downtime, Try after sometime"}';	
    				}
    			} else {
    				$jsDmtResponse = '{"RESP_CODE":"30","RESP_MSG":"Operator API is Down"}';	
    			}
    		} else {
    			$jsDmtResponse = '{"RESP_CODE":"31","RESP_MSG":"Operator Service is Down"}';	
    		}		
    	} else {
    		$jsDmtResponse = '{"RESP_CODE":"23","RESP_MSG":"Invalid DMT operation"}';	
    	}
    } else {
	$jsDmtResponse = '{"RESP_CODE":"25","RESP_MSG":"Service Disabled"}';
}
} else {
	$jsDmtResponse = '{"RESP_CODE":"24","RESP_MSG":"Agent not found"}';
}
?>