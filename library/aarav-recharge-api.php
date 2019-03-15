<?php
	/*
	* AARAV Multi Recharge
	*/
	$api_status = '0';
	$request_txn_no = $recharge_id;
	$url = "http://182.18.129.45:8280/RechargeApp/rest/apiCallForTxn/recharge?serviceNo=".$account."&serviceType=".$operator_info->service_type_aarav."&serviceSubType=".$operator_info->code_aarav."&circleCode=0&amount=".$amount."&userCode=".$aarav['usercode']."&password=".$aarav['password']."&clientTransactionId=".$request_txn_no;
	
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
		if(count($json) > 1) {
			$api_txn_no = isset($json['TXN_ID']) && $json['TXN_ID']!='' ? $json['TXN_ID']:'';
			$api_status = isset($json['STATUS_CODE']) && $json['STATUS_CODE']!='' ? $json['STATUS_CODE']:'';
			$api_status_details = isset($json['STATUS']) && $json['STATUS']!='' ? $json['STATUS']:'';
			$operator_ref_no = isset($json['OPR_REF']) && $json['OPR_REF']!='' ? $json['OPR_REF']:'';		
			if($api_status=='9') {		
				$status='0';
				$status_details='Transaction Successful';	
			} else if ($api_status=='7') {			
				$status='2';
				$status_details='Transaction Failed';		
			} else if($api_status=='8') {		
				$status='1';
				$status_details='Transaction Pending';	
			} else if($api_status=='1'||$api_status=='2'||$api_status=='3'||$api_status=='4'||$api_status=='5'||$api_status=='6') {		
				$status='2';
				$status_details='Transaction Failed';	
			} else {	
				$status='1';
				$status_details='Transaction Pending';	
			}
		} else {
			$api_status = isset($json['STATUS_CODE']) && $json['STATUS_CODE']!='' ? $json['STATUS_CODE']:'';
			$api_info = isset($json['INFO']) && $json['INFO']!='' ? $json['INFO']:'';
			if($api_info!='') {
				$api_status_details = $api_info;
			}
			if($api_status=='9') {
				$status='0';
				$status_details='Transaction Successful';	
			} elseif($api_status=='8') {
				$status='1';
				$status_details='Transaction Pending';	
			} else {
				$status='2';
				$status_details='Transaction Failed';
			}		
		}
	}