<?php
	/*
	* EG Pay 
	*/
	$operator_ref_no = '';
	$request_txn_no = TXNPREFIX.$recharge_id;
	$url = "http://api.egpay.in/index.aspx?UID=".$eg_pay['uid']."&UPASS=".$eg_pay['pass']."&SUBUID=".$eg_pay['subuid']."&SUBUPASS=".$eg_pay['subupass']."&SERTYPE=".$operator_info->service_type_egpay."&OPNAME=".$operator_info->code_egpay."&OPCIRCLE=All+Circle&OPACCNO=".$account."&TXNAMT=".$amount."&TXNNO=".$request_txn_no;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($output) {
		$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);
		$api_status = $xml['Response-code'];
		$api_status_details = isset($xml['Response-status']) ? $xml['Response-status'] : '';
		$api_txn_no = isset($xml['APICorpurate_TXN_ID']) ? $xml['APICorpurate_TXN_ID'] : '';
		
		if($api_status == '0') {
			$status = '0';
			$status_details = 'Transaction Successful';
			
		} else if ($api_status == '101' || $api_status == '102' || $api_status == '103' || $api_status == '104' || $api_status == '105' || $api_status == '106' || $api_status == '107' || $api_status == '108' || $api_status == '109' || $api_status == '110' || $api_status == '111' || $api_status == '112' || $api_status == '117' || $api_status == '121' || $api_status == '122' || $api_status == '123' || $api_status == '124' || $api_status == '125' || $api_status == '126' || $api_status == '127' || $api_status == '128' || $api_status == '129' || $api_status == '130' || $api_status == '131' || $api_status == '132' || $api_status == '133' || $api_status == '134' || $api_status == '135' || $api_status == '136' || $api_status == '137' || $api_status == '138' || $api_status == '139' || $api_status == '140' || $api_status == '141' || $api_status == '142' || $api_status == '143' || $api_status == '144' || $api_status == '145' || $api_status == '146' || $api_status == '147' || $api_status == '148' || $api_status == '149' || $api_status == '150' || $api_status == '151' || $api_status == '152' || $api_status == '153' || $api_status == '154' || $api_status == '155' || $api_status == '156' || $api_status == '157' || $api_status == '158' || $api_status == '159' || $api_status == '160' || $api_status == '161' || $api_status == '162' || $api_status == '163' || $api_status == '164' || $api_status == '165' || $api_status == '166' || $api_status == '167' || $api_status == '168' || $api_status == '169' || $api_status == '170' || $api_status == '171' || $api_status == '172') {
			
			$status = '2';
			$status_details = 'Transaction Failed';		
			
		} else if($api_status == '1') {
		
			$status = '1';
			$status_details = 'Transaction Pending';
		}	
	}