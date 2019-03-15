<?php
	include("../config.php");

	$account = isset($_GET['m']) && $_GET['m'] ? mysql_real_escape_string($_GET['m']) : '0000000000';
	$request_txn_no = time();
	
	include(DIR."/system/class.cyberplat.spaisa.php");
	$sp = new CyberPlatSPaisa();
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;	
	
	$sp_cvalidation = $sp->cpCustomerValidation($account,$request_txn_no);
	
	$signin_result = ipriv_sign($sp_cvalidation, $secret_key, $passwd);
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	$cpurl = $sp->cyberplatUrl();
	$check_output = $sp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$valoutput = $check_output;
	
	print_r($sp_cvalidation);
	echo "<br>--------------------Customer Validation----------------------<br>";	
	print_r($valoutput);
	
	echo "<br>";
	echo "<br>--------------------Customer Payment----------------------<br>";	
	$pay_output = $sp->cyberplatRequest($signin_result[1], $cpurl['pay']);
	$payoutput = $pay_output;	
	print_r($payoutput);