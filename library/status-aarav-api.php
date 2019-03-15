<?php
	/*
	* Aarav Recharge Status API
	*/
	$api_status = "Check Manully";
	$api_status_details = "Check Manully";
	$operator_ref_no = "";
	
	$url = "http://182.18.129.45:8280/RechargeApp/rest/apiCallForTxn/checkstatus?transactionID=".$api_txn_no;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output!==false) {	
		$json = json_decode($output, true);
		$api_status = isset($json['STATUS']) && $json['STATUS']!='' ? $json['STATUS']:'';
		$api_txn_no = isset($json['TXNID']) && $json['TXNID']!='' ? $json['TXNID']:'';
		$api_status_details = "-";
		//$api_status_details = isset($json['message']) ? $json['message'] : '-';
		//$operator_ref_no = isset($json['operator_ref']) && $json['operator_ref'] != ''  ? $json['operator_ref'] : '-';
	}
