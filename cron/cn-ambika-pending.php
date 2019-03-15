<?php
include("../config.php");
$from = date("2017-05-01 00:00:00");
$to = date("2017-05-03 23:59:59");
$rows = [];		
$query = $db->query("SELECT * FROM apps_recharge WHERE api_id='9' AND status='1' AND request_date BETWEEN '".$from."' AND '".$to."' ORDER BY request_date DESC LIMIT 20");
while($result = $db->fetchNextObject($query)) {	
	$rows[] = $result;
}//end of while loop

foreach($rows as $row) {
	$recharge_id = $row->recharge_id; 
	
	$operator_ref_no = $row->operator_ref_no;
	$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&csagentid=".$recharge_id."&fmt=Json";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output !== false) {
		
		if(isJson($output)) {	
			$json = json_decode($output, true);			
		} else {
			libxml_use_internal_errors(true);
			$json = json_decode(json_encode((array) simplexml_load_string($output)), 1);
		}
					
		$api_status = isset($json['STATUS']) && $json['STATUS'] != '' ? $json['STATUS'] : '';
		$account = isset($json['MOBILE']) && $json['MOBILE'] != '' ? $json['MOBILE'] : '';
		$amount = isset($json['AMOUNT']) && $json['AMOUNT'] != '' ? $json['AMOUNT'] : '';
		$api_txn_no = isset($json['RPID']) && $json['RPID'] != '' ? $json['RPID'] : '';
		$operator_ref_no = isset($json['OPID']) && $json['OPID'] != '' ? $json['OPID'] : '';
		$api_status_details = isset($json['MSG']) && $json['MSG'] != '' ? $json['MSG'] : '';	
	}
	
	if($api_status=='SUCCESS') {
		$status = '0';
		$status_details = 'Transaction Successful';	
		
		/*
		* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
		*/
		$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', api_status='".$api_status."', api_status_details='".$api_status_details."', response_date=NOW(), operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$recharge_id."' ");	
		
		/*
		* Update commission for all parent users
		*/	
		$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."' ");
					
		if($agent_info->user_type=='1') {				
			$sCommission=getUserCommission(trim($agent_info->uid), $row->operator_id, $row->amount, 'api');
		} else {
			$sCommission=getUserCommission(trim($agent_info->mdist_id), $row->operator_id, $row->amount, 'r');
		}
			
		if($sCommission['rtCom']>'0') {
			$rt = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND transaction_term='RECHARGE' AND account_id='".$agent_info->uid."' ");
			$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->uid."', '".$sCommission['rtCom']."', '".$rt->closing_balance."', NOW())");
		}	
				
		//Commission Distributor
		if($sCommission['dsCom']>'0') {
			$db->query("START TRANSACTION");
			$ds = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->dist_id."' ");
			if($ds) {										
				$ds_close_balance=$ds->balance+$sCommission['dsCom'];
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
		if($sCommission['mdCom']>'0') {
			$db->query("START TRANSACTION");
			$md = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->mdist_id."' ");
			if($md) {										
				$md_close_balance=$md->balance+$sCommission['mdCom'];
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
		
		
	} elseif($api_status=='FAILED') {
		$status = '2';
		$status_details = 'Transaction Failed';	
		
		/*
		* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
		*/
		$db->execute("UPDATE apps_recharge SET api_status='".$api_status."', api_status_details='".$api_status_details."' WHERE recharge_id='".$recharge_id."' ");
		
	}
	
	echo $recharge_id.", Time: ".$row->request_date.", Status: ".$status.", Status Details: ".$status_details.", Operator Ref No: ".$operator_ref_no."<br>";
	
} //End of for loop
?>