<?php
	/*
	* Achariya Recharge Status API
	*/
	$url = "http://smsalertbox.com/api/rechargestatus.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&txid=".$api_txn_no."&version=4&format=json";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output !== false) {	
		$json = json_decode($output, true);
		$api_txn_no = isset($json['txid']) && $json['txid'] != '' ? $json['txid'] : '-';
		$api_status = isset($json['status']) ? $json['status'] : '-';
		$api_status_details = isset($json['message']) ? $json['message'] : '-';
		$operator_ref_no = isset($json['operator_ref']) && $json['operator_ref'] != ''  ? $json['operator_ref'] : '-';
	}
