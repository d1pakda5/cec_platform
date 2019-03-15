<?php
	/*
	* Ambika Multiservices Recharge API 
	*/
	$request_txn_no = $recharge_id;
	 
	$url = "http://web.jbkonlineservices.com/Apirequest?userId=".$easy['userid']."&password=".$easy['pass']."&tranPin=".$easy['pin']."&mobile=".$account."&amount=".$amount."&opCode=".$operator_info->code_easyrecharge."&request_id=".$request_txn_no;

	
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($output!="" || $output!=null) {	
	
		$json = json_decode($output, true);			
		$api_status = $json['status'];
		$api_txn_no = isset($json['TransactionId']) && $json['TransactionId'] !='' ? $json['TransactionId'] : '';
		$api_status_details = isset($json['message']) && $json['message'] !='' ? $json['message'] : '';
	    $operator_ref_no = isset($json['operatorid']) && $json['operatorid'] !='' ? $json['operatorid'] : '';
	     $msg = isset($json['message']) && $json['message'] !='' ? $json['message'] : '';
		
		if($api_status == 'SUCCESS' && $operator_ref_no!="" ) {		
			$status = '0';
			$status_details = 'Transaction Successful';			
		} else if ($api_status == 'FAILED') {			
			$status = '2';
			$status_details = 'Transaction Failed';			
		} else {		
			if(preg_match("/FAILED/i", $api_status)) {
				$status = '2';
				$status_details = 'Transaction Failed';	
			} else if(preg_match("/Submit/i", $msg)) {		
				$status = '1';
				$status_details = 'Transaction Pending';
			} else {		
				$status = '1';
				$status_details = 'Transaction Pending';
			}			
		}
	}