<?php
	/*
	* Arroh Recharge API 
	*/
	$request_txn_no = $recharge_id;
	
	$url = "http://arrohservices.in/web-services/httpapi/recharge-request?acc_no=".$arroh['acc_no']."&api_key=".$arroh['key']."&opr_code=".$operator_info->code_arroh."&rech_num=".$account."&amount=".$amount."&client_key=".$request_txn_no;
	
	
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($output!="") {	
	    
		
		
		$result=explode("," , $output);
		
	
		$api_status = $result[0];
		$api_txn_no = isset($result[1]) && $result[1]!='' ? $result[1] : '';
		$api_status_details = isset($result[7]) && $result[7] !='' ? $result[7] : '';
		$operator_ref_no = isset($result[6]) && $result[6]!='' ? $result[6] : '';
		
		if($api_status == 'success') {		
			$status = '0';
			$status_details = 'Transaction Successful';			
		} else if ($api_status == 'failure') {			
			$status = '2';
			$status_details = 'Transaction Failed';			
		} else if($api_status == 'received') {		
			$status = '1';
			$status_details = 'Transaction Pending';					
		}  else if($api_status == 'error') {		
			$status = '2';
			$status_details = 'Transaction Failed';					
		} else {		
			if(preg_match("/failure/i", $api_status_details)) {
				$status = '2';
				$status_details = 'Transaction Failed';	
			} else {		
				$status = '1';
				$status_details = 'Transaction Pending';
			}				
		}
	}