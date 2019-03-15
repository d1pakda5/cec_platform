<?php
if(!defined('BROWSE')) {
	exit('No direct access allowed');
}
$operator_code = "DMTV";
$amount = "5.00";			
if($agent_info) {
     $is_money=$db->queryUniqueObject("SELECT is_money from apps_admin where admin_id='".$agent_info->assign_id."'");
    
	$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_code='".$operator_code."' ");
	if($is_money)
    	{
    	if($operator_info) {		
    		$api_id = $operator_info->api_id;
    		
    		$hasFund = $db->queryUniqueObject("SELECT wallet_id,balance,cuttoff FROM apps_wallet WHERE user_id='".$agent_info->user_id."' ");
    		if($hasFund && ($hasFund->balance >= $amount)) {
    			
    			//SEND TO CHECK API
    			include(DIR."/yesbank/beneficiary-validate-check.php");
    			//
    			if(isset($ybCheckStatus) && $ybCheckStatus=="0") {
    				
    				$status = '1';
    				
    				//INSERT TO RECHARGE TABLE
    				$db->execute("INSERT INTO `apps_recharge`(`recharge_id`, `uid`, `recharge_mode`, `api_id`, `service_type`, `operator_id`, `account_no`, `amount`, `surcharge`, `status`, `status_details`, `request_date`, `reference_txn_no`, `recharge_ip`) VALUES ('', '".$agent_info->uid."', '".$mode."', '".$api_id."', '".$operator_info->service_name."', '".$operator_info->operator_id."', '".$account."', '".$amount."', '0', '".$status."', '', NOW(), '".$reference_txn_no."', '".$ip."')");
    				$recharge_id = $db->lastInsertedId();
    				
    				$db->query("START TRANSACTION");
    				$wallet = $db->queryUniqueObject("SELECT wallet_id,balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");
    				$debit_amount = $amount;
    				$close_balance = $wallet->balance - $debit_amount;
    				$db->query("UPDATE apps_wallet SET balance='".$close_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
    				$ts1 = mysql_affected_rows();
    				if($wallet && $ts1) {
    					$commit = $db->query("COMMIT");
    					if($commit) {
    						$remark = "YB-DMT VALIDATE: $recharge_id, $account, $amount, $debit_amount";
    						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'dr', '".$debit_amount."', '".$close_balance."',  'RECHARGE', '".$recharge_id."', '".$remark."', '5', '".$agent_info->uid."')");
    						$transaction_id = $db->lastInsertedId();
    						
    						//Send to payment api
    						include(DIR."/yesbank/beneficiary-validate-pay.php");
    						
    						if($status=='0') {
    							
    							$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
    							
    						} elseif($status=='2') {
    							
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
    									$remark_new = "YB-DMT VALIDATE REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
    									$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$new_close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");												
    									/*
    									* Update response status for failed recharge
    									*/
    									$db->execute("UPDATE apps_recharge SET is_refunded='y' WHERE recharge_id='".$recharge_id."' ");
    									
    								}
    							} else {
    								$db->query("ROLLBACK");
    							}
    												
    						} else {						
    							$db->execute("UPDATE apps_recharge SET status='1', status_details='na', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
    						}
    						//Update Output
    						if(isset($output)) {
    							$db->execute("INSERT INTO `apps_response_log`(`txn_no`, `api_id`, `http_response_code`, `http_response_content`, `update_date`) VALUES ('".$recharge_id."', '".$api_id."', '".$http_code."', '".mysql_real_escape_string($output)."', NOW())");
    						}						
    					} else {
    						$jsDmtResponse = '{"RESP_CODE":"27","RESP_MSG":"Request processed"}';
    					}					
    				} else {				
    					$db->query("ROLLBACK");
    					$db->execute("UPDATE apps_recharge SET status='6', status_details='Transaction cancelled', response_date=NOW() WHERE recharge_id='".$recharge_id."' ");
    					$jsDmtResponse = '{"RESP_CODE":"26","RESP_MSG":"Request validation cancelled"}';
    				}
    			} else if(isset($ybCheckStatus) && $ybCheckStatus=="1") {
    				$jsDmtResponse = $jsDmtResponse;				
    			} else {
    				$jsDmtResponse = $jsDmtResponse;
    			}			
    		} else {			
    			$jsDmtResponse = '{"RESP_CODE":"25","RESP_MSG":"Insufficient Balance in agent account"}';						
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