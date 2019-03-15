<?php
	/*
	* Modem Erecharge Asia (RoundPay) API 
	*/
	$request_txn_no = $recharge_id;
	$url = "http://erechargeasia.in/API/APIService.aspx?userid=".$modem_rp['userid']."&pass=".$modem_rp['pass']."&mob=".$account."&opt=".$operator_info->code_modem_rp."&amt=".$amount."&agentid=".$request_txn_no."&fmt=Json";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($output) {
		$json = json_decode($output, true);
		$api_status = $json['STATUS'];
		$api_txn_no = isset($json['RPID']) && $json['RPID'] != '' ? $json['RPID'] : '';	
		$api_status_details = isset($json['MSG']) && $json['MSG'] != '' ? $json['MSG'] : '';
		$operator_ref_no = isset($json['OPID']) && $json['OPID'] != '' ? $json['OPID'] : '';
		if ($api_status == 'FAILED') {
			$status = '2';
			$status_details = 'Transaction Failed';
		} else if($api_status == 'PENDING') {
			$status = '1';
			$status_details = 'Transaction Pending';
		} else if(preg_match("/FAILED/i", $api_status)) {	
			$status = '2';
			$status_details = 'Transaction Failed';							
		} else {		
			$status = '1';
			$status_details = 'Transaction Pending';							
		}
	}