<?php
	/*
	* Achariya 
	*/
	$request_txn_no = $recharge_id;
	if($operator_info->service_type == '5') {			
		$url = "http://smsalertbox.com/api/recharge.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&number=".$account."&operator=".$operator_info->code_achariya."&circle=&account=".$customer_account."&amount=".$amount."&usertx=".$request_txn_no."&format=json&version=4";
	} else if($operator_info->service_type == '6') {			
		$url = "http://smsalertbox.com/api/recharge.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&number=".$account."&operator=".$operator_info->code_achariya."&circle=&account=".$customer_account."&amount=".$amount."&usertx=".$request_txn_no."&format=json&version=4&cycle=".$billing_cycle."&subdiv=".$sub_division;
	} else if($operator_info->service_type == '8') {	
		$url = "http://smsalertbox.com/api/recharge.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&number=".$account."&operator=".$operator_info->code_achariya."&amount=".$amount."&dob=".$dob."&usertx=".$request_txn_no."&format=json&version=4";
	} else {	
		$url = "http://smsalertbox.com/api/recharge.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&number=".$account."&operator=".$operator_info->code_achariya."&circle=&amount=".$amount."&usertx=".$request_txn_no."&format=json&version=4";		
	}	
	
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
		$api_txn_no = isset($json['txid']) && $json['txid'] != '' ? $json['txid'] : '';
		$api_status = isset($json['status']) ? $json['status'] : '';
		$api_status_details = isset($json['message']) ? $json['message'] : '';
		$operator_ref_no = isset($json['operator_ref']) && $json['operator_ref'] != ''  ? $json['operator_ref'] : '';		
		if($api_status == 'SUCCESS') {		
			$status = '0';
			$status_details = 'Transaction Successful';	
		} else if ($api_status == 'FAILURE') {			
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