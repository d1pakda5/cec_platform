<?php
session_start();
include("../../config.php");
include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();

if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {		
	
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
			$js_data = explode("=",$pay_explode['9']);
			$rst = json_decode($js_data['1']);
			//include(DIR."/dmt/list-beneficiary.php");
			//echo "<br>";
			//print_r($rst->Beneficiary);
			print_r($js_data);
		} elseif(strpos($pay_explode['6'], 'ERROR=23')!==false) {
			include(DIR."/dmt/sender-registration.php");	
			echo "2"; 
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