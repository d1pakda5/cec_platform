<?php
	$api_status = "";
	$api_status_details = "";
	$operator_ref_no = "";
	if($api_txn_no!=""|| $api_txn_no!=null)
	{
	 
	$url = "http://payment2all.com/multirecharge/statusapi/run";    
	    
	$fields = array(
        'username' =>$paymentall['username'] ,
        'password' => $paymentall['password'],
        'rechargeid' => $api_txn_no
    );
    
    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $output = curl_exec($ch);
	curl_close($ch);

	if($output) {
			$result=explode("#",$output);
	    
		if($result[0]=='Success') {			
    		$api_status = isset($result[1]) && $result[1] != '' ? $result[1] : '';
    		
    			if(preg_match("/Failure/i", $api_status)) {
				$status = '2';
				$status_details = 'Transaction Failed';	
				
			} 
    		$operator_ref_no = isset($result[2]) && $result[2] != '' ? $result[2] : '';
		}
		
		if($result[0]=='Error') {	
		        
		        $api_status_details= isset($result[2]) && $result[2] != '' ? $result[2] : '';
		}
		
		
		
	}
	}