<?php
	$http_response_header = "200 M+";
	include(DIR."/system/class.cyberplat.php");
	$cp = new CyberPlat();
	
	$request_txn_no = $recharge_id;
	$op_cyberplat = $operator_info->operator_longcode;
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$addl_param = "";
	if($op_cyberplat=='TD' || $op_cyberplat=='XO' || $op_cyberplat=='RK' || $op_cyberplat=='YU') {
		//Docomo Special, Uninor Special
		$addl_param = "ACCOUNT=2\n\r";
	}
	
	if($op_cyberplat=='LB') {
		$addl_param = "ACCOUNT=".$customer_account."\n\rAuthenticator3=".$bsnl_service_type."\n\r";		
	}
	
	if($op_cyberplat == 'EMS') {
		$addl_param = "ACCOUNT=".$billing_unit."\n\rAuthenticator3=".$pc_number."\n\r";		
	}
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rNUMBER=".trim($account)."\n\rAMOUNT=".trim($amount)."\n\rAMOUNT_ALL=".trim($amount)."\n\r".$addl_param."COMMENT=Recharge";
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//Mobile Verification	
	$cpurl = $cp->cyberplatUrl($op_cyberplat);
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);	
	
	$cp_val_error = '';
	$cp_val_result = '';
	$cp_val_transid = '';
	$cp_val_addinfo = '';
	$cp_val_price = '';
	$cp_val_errmsg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'ERROR=') !== false) {
			$_data = explode("=",$data);
			$cp_val_error = trim($_data[1]);
		}
		if(strpos($data, 'RESULT=') !== false) {
			$_data = explode("=",$data);
			$cp_val_result = trim($_data[1]);
		}
		if(strpos($data, 'TRANSID=') !== false) {
			$_data = explode("=",$data);
			$cp_val_transid = trim($_data[1]);
		}
		if(strpos($data, 'ADDINFO=') !== false) {
			$_data = explode("=",$data);
			$cp_val_addinfo = trim($_data[1]);
		}
		if(strpos($data, 'PRICE=') !== false) {
			$_data = explode("=",$data);
			$cp_val_price = trim($_data[1]);
		}
		if(strpos($data, 'ERRMSG=') !== false) {
			$_data = explode("=",$data);
			$cp_val_errmsg = trim($_data[1]);
		}
	}
	
	if($cp_val_error == '0' && $cp_val_result == '0') {
	
		$pay_output = $cp->cyberplatRequest($signin_result[1], $cpurl['pay']);
		
		$pay_rsp = explode("\n",$pay_output);
		
		foreach($pay_rsp as $data) {
			if(strpos($data, 'DATE=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_date = trim($_data[1]);
			}
			if(strpos($data, 'SESSION=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_session = trim($_data[1]);
			}
			if(strpos($data, 'ERROR=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_error = trim($_data[1]);
			}
			if(strpos($data, 'RESULT=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_result = trim($_data[1]);
			}
			if(strpos($data, 'TRANSID=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_transid = trim($_data[1]);
			}
			if(strpos($data, 'TRNXSTATUS=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_trnxstatus = trim($_data[1]);
			}
			if(strpos($data, 'AUTHCODE=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_authcode = trim($_data[1]);
			}
			if(strpos($data, 'ERRMSG=') !== false) {
				$_data = explode("=",$data);
				$cp_pay_errmsg = trim($_data[1]);
			}
		}
		
		//Code For Response
		$api_status = isset($cp_pay_error) ? trim($cp_pay_error) : '';
		$api_result_status = isset($cp_pay_result) ? trim($cp_pay_result) : '';
		$api_trnxstatus = isset($cp_pay_trnxstatus) ? trim($cp_pay_trnxstatus) : '';
		$api_txn_no = isset($cp_pay_transid) && $cp_pay_transid != '' ? trim($cp_pay_transid) : '';
		$operator_ref_no = isset($cp_pay_authcode) && $cp_pay_authcode != ''  ? trim($cp_pay_authcode) : '';	
		$api_status_details = isset($cp_pay_errmsg) ? trim($cp_pay_errmsg) : '';
		$output = $pay_output;
		if($api_status == '0') {
			if($api_result_status == '0' || $api_result_status == '7') {	
				$status = '0';
				$status_details = 'Transaction Successful';
				if($api_status_details == '') {
					$api_status_details = $cp->cyberplatError($api_status);	
				}
			} else {	
				$status = '1';
				$status_details = 'Transaction Pending';	
			}
		} else {		
			$status = '2';
			$status_details = 'Transaction Failed';	
		}
		
	} else {
		$status = '2';
		$status_details = 'Transaction Failed';
		$api_status = $cp_val_error;
		if($cp_val_errmsg != '') {
			$api_status_details = $cp_val_errmsg;	
		} else {
			$api_status_details = $cp->cyberplatError($api_status);	
		}
	}
	
	$http_code = $http_response_header;
	
?>