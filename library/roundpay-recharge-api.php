<?php
	/*
	* Roundpay Recharge API 
	*/
	$request_txn_no = $recharge_id;
	
	$url = "http://roundpayapi.in/API/APIService.aspx?userid=".$roundpay['userid']."&pass=".$roundpay['pass']."&mob=".$account."&opt=".$operator_info->code_roundpay."&amt=".$amount."&agentid=".$request_txn_no."&fmt=Json";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	//{"STATUS":"FAILED","MOBILE":"9826386456","AMOUNT":"12","RPID":"16010823084714D907","AGENTID":"11499852","OPID":"Invalid Service Provider","BAL":302.15,"MSG":"Transaction Failed"}
	if($output) {
		if(isJson($output)) {	
			$json = json_decode($output, true);			
		} else {
			libxml_use_internal_errors(true);
			$json = json_decode(json_encode((array) simplexml_load_string($output)), 1);
		}
		
		$api_status = $json['STATUS'];
		$api_txn_no = isset($json['RPID']) && $json['RPID'] != '' ? $json['RPID'] : '';	
		$api_status_details = isset($json['MSG']) && $json['MSG'] != '' ? $json['MSG'] : '';
		$operator_ref_no = isset($json['OPID']) && $json['OPID'] != '' ? $json['OPID'] : '';
		
		if($api_status == 'SUCCESS') {		
			$status = '0';
			$status_details = 'Transaction Successful';			
		} else if ($api_status == 'FAILED') {			
			$status = '2';
			$status_details = 'Transaction Failed';			
		} else if($api_status == 'PENDING') {		
			$status = '1';
			$status_details = 'Transaction Pending';					
		} else {
			if(preg_match("/FAILED/i", $api_status_details)) {
				$status = '2';
				$status_details = 'Transaction Failed';	
			} else {		
				$status = '1';
				$status_details = 'Transaction Pending';
			}
		}
	}