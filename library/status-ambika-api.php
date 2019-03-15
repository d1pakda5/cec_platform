<?php
	$api_status = "Check Manully";
	$api_status_details = "Check Manully";
	$operator_ref_no = "";
	if($api_txn_no!="" || $api_txn_no!=null)
	{
	$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&csrpid=".$api_txn_no."&fmt=json&fmt=Json";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	}
	else{
	if($request_txn_no==""||$request_txn_no==null)
	{
	    $request_txn_no=$recharge_id;
	}
	
	$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&csagentid=".$request_txn_no."&fmt=Json";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	}
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