<?php
session_start();
include("../config.php");
include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();

if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;	
	
	$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0000000000';
	$request_txn_no = time();
	
	$sp_cvalidation = $sp->cpCustomerValidation($account,$request_txn_no);
	
	$signin_result = ipriv_sign($sp_cvalidation, $secret_key, $passwd);
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	$cpurl = $sp->cyberplatUrl();
	
	$check_output = $sp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$check_explode = explode("\n",$check_output);	
	
	if(strpos($check_explode['6'], 'ERROR=0')!==false) {
		$pay_output = $sp->cyberplatRequest($signin_result[1], $cpurl['pay']);
		$pay_explode = explode("\n",$pay_output);
		if(strpos($pay_explode['6'], 'ERROR=0')!==false) {	
			echo "1"; 
		} elseif(strpos($pay_explode['6'], 'ERROR=23')!==false) {	
			echo "2"; 
		} else {
			echo "3";
		}
		echo "<br>";
		print_r($pay_output);
		echo "<br>";
		print_r($pay_explode);
	} else {
		echo "4";
	}
	 
} else {
	echo "Invalid Mobile Number";
}
?>