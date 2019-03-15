<?php
	
	if($service_type==' ') {
	
	$api_status = "Unknown";
	$api_status_details = "Check DMT status to api";
	$operator_ref_no = "";
	
	} else {
	
	$api_status = "Check Manully";
	$api_status_details = "Check Manully";
	$operator_ref_no = "";
	include(DIR."/system/class.cyberplat.php");
	$cp = new CyberPlat();
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$cp_string = "SESSION=".$request_txn_no;
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	//Mobile Verification	
	$cpurl = $cp->cyberplatUrl($op_cyberplat);
	
	//Status
	$status_output = $cp->cyberplatRequest($signin_result[1], $cpurl['status']);
	$output = $status_output;
	
	$status_rsp = explode("\n",$status_output);
	
	$cp_status_error = '';
	$cp_status_result = '';
	$cp_status_authcode = '';
	$cp_status_transid = '';
	$cp_status_addinfo = '';
	$cp_status_price = '';
	$cp_status_errmsg = '';
	
	foreach($status_rsp as $data) {
	    
		if(strpos($data, 'ERROR=') !== false) {
			$_data = explode("=",$data);
			$cp_status_error = trim($_data[1]);
		}
		if(strpos($data, 'RESULT=') !== false) {
			$_data = explode("=",$data);
			$cp_status_result = trim($_data[1]);
		}
		if(strpos($data, 'AUTHCODE=') !== false) {
			$_data = explode("=",$data);
			$cp_status_authcode = trim($_data[1]);
		}
		if(strpos($data, 'TRANSID=') !== false) {
			$_data = explode("=",$data);
			$cp_status_transid = trim($_data[1]);
		}
		if(strpos($data, 'ADDINFO=') !== false) {
			$_data = explode("=",$data);
			$cp_status_addinfo = trim($_data[1]);
		}
		if(strpos($data, 'PRICE=') !== false) {
			$_data = explode("=",$data);
			$cp_status_price = trim($_data[1]);
		}
		if(strpos($data, 'ERRMSG=') !== false) {
			$_data = explode("=",$data);
			$cp_status_errmsg = trim($_data[1]);
		}
	}

	if($cp_status_error == '0' && $cp_status_result == '7') {		
		$api_status = "Success";
		$api_status_details = "Transaction Successful";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '0' && $cp_status_result == '3') {		
		$api_status = "Pending";
		$api_status_details = "Transaction Pending ".$cp_status_errmsg;
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '0' && $cp_status_result == '1') {		
		$api_status = "Failed";
		$api_status_details = "Transaction Failed (code: ".$cp_status_error.") (result: ".$cp_status_result.")";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '23') {		
		$api_status = "Failed";
		$api_status_details = "Invalid Mobile Number.";
		$operator_ref_no = $cp_status_authcode;
	}
		else if($cp_status_error == '24') {		
		$api_status = "Failed";
		$api_status_details = "Error of connection with the provider’s
server or a routine break in CyberPlat®.";
		$operator_ref_no = $cp_status_authcode;
	}

	else if($cp_status_error == '8') {		
		$api_status = "Failed";
		$api_status_details = "Invalid phone number format.";
		$operator_ref_no = $cp_status_authcode;
	}
	
	else if($cp_status_error == '15') {		
		$api_status = "Failed";
		$api_status_details = "Operator is not supported Failed.";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '20') {		
		$api_status = "Failed";
		$api_status_details = "The payment is being completed. Failed.";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '21') {		
		$api_status = "Failed";
		$api_status_details = "Not enough funds for effecting the
payment.";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '22') {		
		$api_status = "Failed";
		$api_status_details = "Not enough wallet balance, blocked wallet
etc. Failed";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '45') {		
		$api_status = "Failed";
		$api_status_details = "Specific operator not activated for dealer Failed";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '88') {		
		$api_status = "Failed";
		$api_status_details = "Duplicate Transaction (Same Mobile
Number)";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error == '224') {		
		$api_status = "Failed";
		$api_status_details = "Operator system is down. Please try
again later.";
		$operator_ref_no = $cp_status_authcode;
	}else if($cp_status_error == '333') {		
		$api_status = "Failed";
		$api_status_details = "Unknown error Unknown error from Provider side Failed";
		$operator_ref_no = $cp_status_authcode;
	}else if($cp_status_error == '171') {		
		$api_status = "Failed";
		$api_status_details = "Manually Cancelled Arises in case of only Call-Back responses. Failed";
		$operator_ref_no = $cp_status_authcode;
	}
	else if($cp_status_error != '0') {		
		$api_status = "Failed";
		$api_status_details = "Transaction Failed (code: ".$cp_status_error.") (result: ".$cp_status_result.$cp_status_errmsg.")";
		$operator_ref_no = $cp_status_authcode;
	}
	
	}
