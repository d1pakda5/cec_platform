<?php
	$http_response_header = "200 M+";
	include(DIR."/system/class.bbps.php");
	$bb = new CyberPlat();
	
	$request_txn_no = $recharge_id;
	$op_cyberplat = $operator_info->bbps_code;
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$addl_param = "";
	if($op_cyberplat=='MTNLDELOB' || $op_cyberplat=='RELENGOB' || $op_cyberplat=='TPCOB' || $op_cyberplat=='YU') {
		//Docomo Special, Uninor Special
		$addl_param = "ACCOUNT=2\n\r";
	}
	
	if($op_cyberplat=='BSNLOB') {
		$addl_param = "ACCOUNT=".$customer_account."\n\rAuthenticator3=".$bsnl_service_type."\n\r";		
	}
	
	if($op_cyberplat == 'MSEBOB') {
		$addl_param = "ACCOUNT=".$billing_unit."\n\rAuthenticator3=".$pc_number."\n\r";		
	}


	$bb_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAgentId=BD01BD02MBB000000001\n\rChannel=AGT\n\rfName=Ninad\n\rlName=Bhoi\n\rPanCardNo=NA\n\rAadhar=NA\n\rCardType=NA\n\rEmail=support@clickecharge.com\n\rbenMobile=8600000355\n\rGeoCode=28.6139,78.5555\n\rPin=401101\n\rTERMINAL_ID=123456\n\rNUMBER=".trim($account)."\n\rAMOUNT=".trim($amount)."\n\rAMOUNT_ALL=".trim($amount)."\n\rTERM_ID=".CP_AP."\n\r".$addl_param."COMMENT=Recharge";
	
	$signin_result = ipriv_sign($bb_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//Mobile Verification	
	$bburl = $bb->cyberplatUrl($op_cyberplat);
	$check_output = $bb->cyberplatRequest($signin_result[1], $bburl['check']);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);	
	
	$bb_val_error = '';
	$bb_val_result = '';
	$bb_val_transid = '';
	$bb_val_addinfo = '';
	$bb_val_price = '';
	$bb_val_errmsg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'ERROR=') !== false) {
			$_data = explode("=",$data);
			$bb_val_error = trim($_data[1]);
		}
		if(strpos($data, 'RESULT=') !== false) {
			$_data = explode("=",$data);
			$bb_val_result = trim($_data[1]);
		}
		if(strpos($data, 'TRANSID=') !== false) {
			$_data = explode("=",$data);
			$bb_val_transid = trim($_data[1]);
		}
		if(strpos($data, 'ADDINFO=') !== false) {
			$_data = explode("=",$data);
			$bb_val_addinfo = trim($_data[1]);
		}
		if(strpos($data, 'PRICE=') !== false) {
			$_data = explode("=",$data);
			$bb_val_price = trim($_data[1]);
		}
		if(strpos($data, 'ERRMSG=') !== false) {
			$_data = explode("=",$data);
			$bb_val_errmsg = trim($_data[1]);
		}
	}
	
	if($bb_val_error == '0' && $bb_val_result == '0') {
	
		$pay_output = $bb->cyberplatRequest($signin_result[1], $bburl['pay']);
		
		$pay_rsp = explode("\n",$pay_output);
		
		foreach($pay_rsp as $data) {
			if(strpos($data, 'DATE=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_date = trim($_data[1]);
			}
			if(strpos($data, 'SESSION=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_session = trim($_data[1]);
			}
			if(strpos($data, 'ERROR=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_error = trim($_data[1]);
			}
			if(strpos($data, 'RESULT=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_result = trim($_data[1]);
			}
			if(strpos($data, 'TRANSID=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_transid = trim($_data[1]);
			}
			if(strpos($data, 'TRNXSTATUS=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_trnxstatus = trim($_data[1]);
			}
			if(strpos($data, 'AUTHCODE=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_authcode = trim($_data[1]);
			}
			if(strpos($data, 'ERRMSG=') !== false) {
				$_data = explode("=",$data);
				$bb_pay_errmsg = trim($_data[1]);
			}
		}
		
		//Code For Response
		$api_status = isset($bb_pay_error) ? trim($bb_pay_error) : '';
		$api_result_status = isset($bb_pay_result) ? trim($bb_pay_result) : '';
		$api_trnxstatus = isset($bb_pay_trnxstatus) ? trim($bb_pay_trnxstatus) : '';
		$api_txn_no = isset($bb_pay_transid) && $bb_pay_transid != '' ? trim($bb_pay_transid) : '';
		$operator_ref_no = isset($bb_pay_authcode) && $bb_pay_authcode != ''  ? trim($bb_pay_authcode) : '';	
		$api_status_details = isset($bb_pay_errmsg) ? trim($bb_pay_errmsg) : '';
		$output = $pay_output;
		if($api_status == '0') {
			if($api_result_status == '0' || $api_result_status == '7') {	
				$status = '0';
				$status_details = 'Transaction Successful';
				if($api_status_details == '') {
					$api_status_details = $bb->cyberplatError($api_status);	
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
		$api_status = $bb_val_error;
		if($bb_val_errmsg != '') {
			$api_status_details = $bb_val_errmsg;	
		} else {
			$api_status_details = $bb->cyberplatError($api_status);	
		}
	}
	
	$http_code = $http_response_header;
	
?>