<?php
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
	
	$param_account = "";
	if($op_cyberplat=='TD' || $op_cyberplat=='RK') {
		//Docomo Special, Uninor Special
		$param_account = "ACCOUNT=2\n\r";
	}
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rNUMBER=".$account."\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount."\n\r".$param_account."COMMENT=Recharge";
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//Mobile Verification	
	$cpurl = $cp->cyberplatUrl($op_cyberplat);
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);
	$check_error = explode("=",$check_rsp[6]);	
	$cp_verify_code = trim($check_error[1]);
	
	if($cp_verify_code == '0') {
	
		$pay_output = $cp->cyberplatRequest($signin_result[1], $cpurl['pay']);
		
		$pay_rsp = explode("\n",$pay_output);
		
		$pay_rslt_date = explode("=",$pay_rsp[4]);
		$pay_rslt_session = explode("=",$pay_rsp[5]);
		$pay_rslt_error = explode("=",$pay_rsp[6]);
		$pay_rslt_result = explode("=",$pay_rsp[7]);
		$pay_rslt_transid = explode("=",$pay_rsp[8]);
		$pay_rslt_authcode = explode("=",$pay_rsp[9]);
		$pay_rslt_trnxstatus = explode("=",$pay_rsp[10]);
		$pay_rslt_errmsg = explode("=",$pay_rsp[11]);
		
		//Code For Response
		$api_status = isset($pay_rslt_error[1]) ? trim($pay_rslt_error[1]) : '';
		$api_result_status = isset($pay_rslt_result[1]) ? trim($pay_rslt_error[1]) : '';
		$api_trnxstatus = isset($pay_rslt_trnxstatus[1]) ? trim($pay_rslt_trnxstatus[1]) : '';
		$api_txn_no = isset($pay_rslt_transid[1]) && $pay_rslt_transid[1] != '' ? trim($pay_rslt_transid[1]) : '';
		$operator_ref_no = isset($pay_rslt_authcode[1]) && $pay_rslt_authcode[1] != ''  ? trim($pay_rslt_authcode[1]) : '';	
		$api_status_details = isset($pay_rslt_errmsg[1]) ? trim($pay_rslt_errmsg[1]) : '';
		$output = $pay_output;
		if($api_status == '0') {
			if($api_result_status == '0' || $api_result_status == '7') {	
				$status = '0';
				$status_details = 'Transaction Successful';	
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
		$check_rslt_errmsg = explode("=",$check_rsp[9]);
		$api_status = $cp_verify_code;
		$api_status_details = $cp->cyberplatError($api_status);	
	}
	
	$http_code = "200";
	
?>
