<?php
	/*
	* Modem Erecharge Asia (RoundPay) API 
	*/
	$request_txn_no = $recharge_id;
	
	$url = "http://erechargeasia.in/apis/RecrgRequest.aspx?uid=".$modem_rp['uid']."&pass=".$modem_rp['pass']."&mno=".$account."&op=".$operator_info->code_modem_rp."&amt=".$amount."&refid=".$request_txn_no;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($output) {
		libxml_use_internal_errors(true);
		$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);
		$api_txn_no = isset($xml['REFID']) && $xml['REFID'] != '' ? $xml['REFID'] : '';
		$api_status = isset($xml['STATUS']) ? strtoupper($xml['STATUS']) : '';
		$api_status_details = isset($xml['REASON']) && $xml['REASON'] != '' ? $xml['REASON'] : '';
		$operator_ref_no = '';
		if ($api_status == 'FAILED') {
			$status = '2';
			$status_details = 'Transaction Failed';
		} else if($api_status == 'PENDING') {
			$status = '1';
			$status_details = 'Transaction Pending';
		} else {		
			$status = '1';
			$status_details = 'Transaction Pending';							
		}	
	}