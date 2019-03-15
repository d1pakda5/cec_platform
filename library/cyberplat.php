<?php
	include(DIR."/system/class.cyberplat.php");
	$cp = new CyberPlat();
	
	//$request_txn_no = $recharge_id;
	//$op_cyberplat = $operator_info->operator_longcode;
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rNUMBER=".$account."\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount."\n\rCOMMENT=Recharge";
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	echo "<br>SIGN IN RESULT-----------------------------------------------------------<br>";
	print_r($signin_result);
	echo "<br><br>";
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	echo "<br>VERIFY IN RESULT-----------------------------------------------------------<br>";
	print_r($verify_result);
	echo "<br><br>";
	
	$cpurl = $cp->cyberplatUrl($op_cyberplat);
	echo "<br>URL RESULT-----------------------------------------------------------<br>";
	print_r($cpurl);
	echo "<br><br>";
	
	//Mobile Verification		
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);
	$check_error = explode("=",$check_rsp[6]);	
	$cp_verify_code = trim($check_error[1]);
	
	echo "<br>CHECK OUTPUT-----------------------------------------------------------<br>";
	echo $output;
	echo "<br><br>";
	
	if($cp_verify_code == '0') {
	
		$pay_output = cyberplatRequest($signin_result[1], $cpurl['pay']);
		$output = $pay_output;
		echo "<br>PAYMENT OUTPUT-----------------------------------------------------------<br>";
		echo $output;
		echo "<br><br>";
		exit();
		
		$pay_rsp = explode("\n",$pay_output);
		$pay_error['DATE'] = explode("=",$pay_rsp[4]);
		$pay_error['SESSION'] = explode("=",$pay_rsp[5]);
		$pay_error['ERROR'] = explode("=",$pay_rsp[6]);
		$pay_error['RESULT'] = explode("=",$pay_rsp[7]);
		$pay_error['TRANSID'] = explode("=",$pay_rsp[8]);
		$pay_error['AUTHCODE'] = explode("=",$pay_rsp[9]);
		$pay_error['TRNXSTATUS'] = explode("=",$pay_rsp[10]);
		$pay_error['ERRMSG'] = explode("=",$pay_rsp[11]);
		
		//Code For Response
		$api_status = isset($pay_error['ERROR']) ? $pay_error['ERROR'] : '';
		$api_result_status = isset($pay_error['RESULT']) ? $pay_error['RESULT'] : '';
		$api_trnxstatus = isset($pay_error['TRNXSTATUS']) ? $pay_error['TRNXSTATUS'] : '';
		$api_txn_no = isset($pay_error['TRANSID']) && $pay_error['TRANSID'] != '' ? $pay_error['TRANSID'] : '';
		$operator_ref_no = isset($pay_error['AUTHCODE']) && $pay_error['AUTHCODE'] != ''  ? $pay_error['AUTHCODE'] : '';	
		$api_status_details = isset($pay_error['ERRMSG']) ? $pay_error['ERRMSG'] : '';
		
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
	}
	
	$http_code = "200";
	
	exit();
	
?>