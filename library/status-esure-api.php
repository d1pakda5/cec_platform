<?php
	$api_status = "Check Manully";
	$api_status_details = "Check Manully";
	$operator_ref_no = "";
    $request_txn_no=$recharge_id;
	
	$url = "http://esuresolution.com/API/APIService.aspx?userid=".$esure['userid']."&pass=".$esure['pass']."&csagentid=".$request_txn_no."";
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