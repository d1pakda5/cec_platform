<?php
	/*
	* Ambika Multiservices Recharge API 
	*/
	$request_txn_no = $recharge_id;
	$url="http://payment2all.com/multirecharge/rechargeapi/run";
		
	$fields = array(
        'username' => $paymentall['username'] ,
        'password' => $paymentall['password'],
        'number' => $account,
        'operator' => $operator_info->code_paymentall,
        'amount' => $amount,
        'yourId' => $recharge_id
    );
    
    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $output = curl_exec($ch);
	curl_close($ch);
	
	if($output) {	
	   
		
		$result=explode("#",$output);
		if($result[0]=='Success')
		{
		
		$api_status = $result[2];
		$api_txn_no = isset($result[1]) && $result[1] !='' ? $result[1] : '';
		$operator_ref_no = isset($result[3]) && $result[3] !='' ? $result[3] : '';
		
		if($api_status == 'Success') {		
			$status = '0';
			$status_details = 'Transaction Successful';			
		} else if ($api_status == 'Failure') {			
			$status = '2';
			$status_details = 'Transaction Failed';			
		} else if($api_status == 'Pending') {		
			$status = '1';
			$status_details = 'Transaction Pending';					
		} else {		
			if(preg_match("/Failure/i", $api_status)) {
				$status = '2';
				$status_details = 'Transaction Failed';	
			} else {		
				$status = '1';
				$status_details = 'Transaction Pending';
			}				
		}
	}
		if($result[0]=='Error')
		{
		    $api_status_details = isset($result[2]) && $result[2] !='' ? $result[2] : '';
		    
		}
		
	}