<?php
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
		$api_status = "0";
		$api_status_details = "Transaction Successful";
		$operator_ref_no = $cp_status_authcode;
	}
