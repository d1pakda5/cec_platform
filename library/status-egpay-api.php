<?php
	/*
	* EGPAY RECHARGE STATUS
	*/
	$url = "http://api.egpay.in/GetUpdatedStatus.aspx?UID=".$eg_pay['uid']."&UPASS=".$eg_pay['pass']."&SUBUID=".$eg_pay['subuid']."&SUBUPASS=".$eg_pay['subupass']."&TXNNO=".$request_txn_no;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output !== false) {					
		//libxml_use_internal_errors(true);
		$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);		
		$api_txn_no = isset($xml['API-Corporate-TXN-ID']) && $xml['API-Corporate-TXN-ID'] != '' ? $xml['API-Corporate-TXN-ID'] : '';
		$api_status = isset($xml['Response_code']) ? $xml['Response_code'] : '';
		$api_status_details = isset($xml['Response-status']) ? $xml['Response-status'] : '';
		$operator_ref_no = isset($xml['Operater-ID']) && $xml['Operater-ID'] != ''  ? $xml['Operater-ID'] : '';
	}
