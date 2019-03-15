<?php
include('config.php');
$content = "";
foreach ($_GET as $key => $value) {
	$content .= $key."=".urldecode($value)."; ";	
}
$db->query("INSERT INTO apps_reverse_response (reverse_response_id, api_id, reverse_response_content, response_time) VALUES ('', '9', '".$content."', NOW())");

if(isset($_GET['status']) && $_GET['status'] != '' && isset($_GET['rpid']) && $_GET['rpid'] != '' && isset($_GET['mobile']) && $_GET['mobile'] != '' && isset($_GET['amount']) && $_GET['amount'] != '') {
	
	$api_status = trim(mysql_real_escape_string($_GET['status']));
	$amount = trim(mysql_real_escape_string($_GET['amount']));
	$account = trim(mysql_real_escape_string($_GET['mobile']));
	$recharge_id = trim(mysql_real_escape_string($_GET['agentid']));
	$api_txn_no = trim(mysql_real_escape_string($_GET['rpid']));
	
	$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE account_no = '".$account."' AND amount = '".$amount."' AND recharge_id = '".$recharge_id."' AND api_id = '9' ");
	if($recharge_info) {
		$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$recharge_info->uid."' ");
		if($agent_info) {
			
			if($api_status == 'SUCCESS') {
				$status = '0';
				$status_details = 'Transaction Successful';	
				$operator_ref_no = trim(mysql_real_escape_string($_GET['opid']));
				/*
				* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
				*/
				$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', api_status = '', api_status_details = '', response_date = NOW(), operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_info->recharge_id."' ");
				/*
				* Update commission for all parent users
				*/
				
				$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id = '".$recharge_info->operator_id."'");
				
				$sCommission = getUserCommission(trim($agent_info->mdist_id), $operator_info->operator_id, $amount);
					
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
				
			} else if ($api_status == 'FAILED') {
				$status = '2';
				$status_details = 'Transaction Failed';	
				/*
				* Update response status for failed recharge
				*/
				$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', response_date = NOW(), api_status = '".$api_status."' WHERE recharge_id = '".$recharge_id."' ");	
			}
			////////////////////////////////////
		}
	}
}
?>