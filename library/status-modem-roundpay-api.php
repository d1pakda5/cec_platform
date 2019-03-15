<?php
	$api_status = "NA";
	$api_status_details = "NA";
	$operator_ref_no = "NA";
	
	$url="http://erechargeasia.in/API/APIService.aspx?userid=".$roundpay['userid']."&pass=".$roundpay['pass']."&csagentid=".$request_txn_no;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	if($output !== false) {
		libxml_use_internal_errors(true);
		$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);				
		$api_status = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
		$operator_ref_no = isset($xml['OPID']) && $xml['OPID'] != '' ? $xml['OPID'] : '';
		$api_status_details = isset($xml['MSG']) && $xml['MSG'] != '' ? $xml['MSG'] : '';
	}
