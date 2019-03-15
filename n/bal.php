<?php
	//include("../config.php");
	
	$request_txn_no = time();
	
	include("class.cyberplat.php");
	$cp = new CyberPlat();	
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents("secret.key");
	$public_key = file_get_contents("pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no;
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//Mobile Verification	
	$cpurl = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);
	
	$cp_val_error = '';
	$cp_val_result = '';
	$cp_val_transid = '';
	$cp_val_msg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'REST=') !== false) {
			$_data = explode("=",$data);
			$cp_balance = trim($_data[1]);
		}
	}
	
	echo $cp_balance;
	
?>