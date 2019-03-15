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
	$bank = isset($_GET['bank_name']) && $_GET['bank_name'] ? mysql_real_escape_string($_GET['bank_name']) : '';
	$branch = isset($_GET['branch_name']) && $_GET['branch_name'] ? mysql_real_escape_string($_GET['branch_name']) : '';
	
	$sp_cvalidation = $sp->cpGetBank($account,$request_txn_no,$bank,$branch);
	
	$signin_result = ipriv_sign($sp_cvalidation, $secret_key, $passwd);
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	$cpurl = $sp->cyberplatUrl();
	
	$check_output = $sp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$check_explode = explode("\n",$check_output);	
	
	if(strpos($check_explode['6'], 'ERROR=0')!==false) {
		$pay_output = $sp->cyberplatRequest($signin_result[1], $cpurl['pay']);
		print_r($pay_output);
		echo "<br><br>";
		//include();
		$pay_explode = explode("\n",$pay_output);
		if(strpos($pay_explode['6'], 'ERROR=0')!==false) {
			$js_data = explode("=",$pay_explode['9']);
			print_r($js_data);
		} else {
			echo "3";
		}
	} else {
		echo "4";
	}
	 
} else {
	echo "Invalid Mobile Number";
}
?>