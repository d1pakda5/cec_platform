<?php
	/*
	* Ajira (2) Jio 
	*/
	$request_txn_no = $recharge_id;
	$url ="http://ajira.online/Jio.asmx/Recharge?macID=c4:0b:cb:64:60:7d&posID=101&userID=0682203093&cName=Aniketsales&password=".$operator_info->password."&number=".$account."&amount=".$amount;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch); 
	
	//{"OpeningBalance":"5298.00","TranactionID":"BR00004UB392","CustomerNumber":"8770789403","Amount":"309","Name":"LOKESH_THADANI","Status":"SUCCESS","Message":"Dear Partner Order BR00004UB392 successfully Processed on 8770789403 of Lokesh_Thadani with amount 309.00 Balance 4989.00"}
	if($output) {
		$json = json_decode($output, true);
		$api_txn_no = $request_txn_no;
		$api_status = isset($json['Status']) ? $json['Status'] : '';
		$api_status_details = isset($json['Message']) ? $json['Message'] : '';
		$operator_ref_no = isset($json['TranactionID']) && $json['TranactionID']!=''  ? $json['TranactionID'] : '';		
		if($api_status == 'SUCCESS') {		
			$status = '0';
			$status_details = 'Transaction Successful';	
		} else if ($api_status == 'PENDING') {	
			if($api_status_details=="Recharge plan not available" || $api_status_details=="Customer not found, please check number") {
				$status = '2';
				$status_details = 'Transaction Failed';
			}	 else {	
				$status = '8';
				$status_details = 'Transaction Submitted';
			}	
		} else if ($api_status=='PROCESS') {
			if($api_status_details=="Unable to login") {
				if($amount=='309' || $amount=='408') {
					$status = '8';
					$status_details = 'Transaction Submitted';
				} else {
					$status = '2';
					$status_details = 'Transaction Failed';
				}	
			} else {
				$status = '8';
				$status_details = 'Transaction Submitted';
			}	
		} else {		
			$status = '1';
			$status_details = 'Transaction Pending';	
		}	
	}