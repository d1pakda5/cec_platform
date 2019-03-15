<?php
define("BROWSE", "browse");
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];

include(DIR."/yesbank/class.cyberplat.yesbank.php");

$yb = new CyberPlatYesBank();
$mode = "API";
if(isset($_GET["userid"]) && $_GET["userid"]!='' && isset($_GET["key"]) && $_GET["key"]!='' && isset($_GET["request"]) && $_GET["request"]!='' && isset($_GET["mobile"])  && $_GET["mobile"]!='') {
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$account = isset($_GET['mobile']) && $_GET['mobile']!='' ? mysql_real_escape_string($_GET['mobile']) : '0';
	$request = isset($_GET['request']) && $_GET['request']!='' ? mysql_real_escape_string($_GET['request']) : 'INVALIDREQUEST';
	$request_txn_no = time();	
	$reference_txn_no = isset($_GET['usertxn']) && $_GET['usertxn']!='' ? mysql_real_escape_string($_GET['usertxn']) : '';
	
	$user_api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid='".$uid."' AND user_key='".$userkey."' ");
	if($user_api_info) {
		if($user_api_info->ip1==$ip || $user_api_info->ip2==$ip || $user_api_info->ip3==$ip || $user_api_info->ip4==$ip) {
			$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$user_api_info->user_id."' ");
			if($agent_info) {	
			    if(isset($_GET['request']) && $_GET['request']=='searchCustomer') {
			include(DIR."/yesbank/customer-validate.php");
			echo $jsDmtResponse;
			exit();	
					
		} else if(isset($_GET['request']) && $_GET['request']=='customerRegistration') {
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';		
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			$dob = isset($_GET['dob']) && $_GET['dob']!='' ? mysql_real_escape_string($_GET['dob']) : '';
			$city = isset($_GET['city']) && $_GET['city']!='' ? mysql_real_escape_string($_GET['city']) : '';
			$state = isset($_GET['state']) && $_GET['state']!='' ? mysql_real_escape_string($_GET['state']) : '';
			$pincode = isset($_GET['pincode']) && $_GET['pincode']!='' ? mysql_real_escape_string($_GET['pincode']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			include(DIR."/yesbank/customer-registration.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/yesbank/beneficiary-add.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';			
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '0';
			$ben_bank = isset($_GET['ben_bank']) && $_GET['ben_bank']!='' ? mysql_real_escape_string($_GET['ben_bank']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			$kyc_status = isset($_GET['kyc_status']) && $_GET['kyc_status']!='' ? mysql_real_escape_string($_GET['kyc_status']) : '';			
			include(DIR."/yesbank/beneficiary-validate.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_ref_code = isset($_GET['ben_ref_code']) && $_GET['ben_ref_code']!='' ? mysql_real_escape_string($_GET['ben_ref_code']) : '';
			$otp = isset($_GET['otp']) && $_GET['otp']!='' ? mysql_real_escape_string($_GET['otp']) : '';
			include(DIR."/yesbank/beneficiary-delete.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='moneyRemittance') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';			
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '0';
			$ben_bank = isset($_GET['ben_bank']) && $_GET['ben_bank']!='' ? mysql_real_escape_string($_GET['ben_bank']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : 'IMPS';
			$kyc_status = isset($_GET['kyc_status']) && $_GET['kyc_status']!='' ? mysql_real_escape_string($_GET['kyc_status']) : '';
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '0';	
			include(DIR."/yesbank/money-remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otpVerify') {
			$otp_ref_code = isset($_GET['otp_ref_code']) && $_GET['otp_ref_code']!='' ? mysql_real_escape_string($_GET['otp_ref_code']) : '';
			$otp = isset($_GET['otp']) && $_GET['otp']!='' ? mysql_real_escape_string($_GET['otp']) : '';
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$request_for = isset($_GET['request_for']) && $_GET['request_for']!='' ? mysql_real_escape_string($_GET['request_for']) : '';
			include(DIR."/yesbank/otp-verify.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otpGenerate') {
			$otp_request_for = isset($_GET['request_for']) && $_GET['request_for']!='' ? mysql_real_escape_string($_GET['request_for']) : '';
			include(DIR."/yesbank/otp-generate.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionHistory') {
			$from = isset($_GET['from']) && $_GET['from']!='' ? mysql_real_escape_string($_GET['from']) : date("d-m-Y");
			$to = isset($_GET['to']) && $_GET['to']!='' ? mysql_real_escape_string($_GET['to']) : date("d-m-Y");
			include(DIR."/yesbank/transaction-history.php");
			echo $jsDmtResponse;
			exit();
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionRefund') {
			$otp_ref_code = isset($_GET['otp_ref_code']) && $_GET['otp_ref_code']!='' ? mysql_real_escape_string($_GET['otp_ref_code']) : '';
			$otp = isset($_GET['otp']) && $_GET['otp']!='' ? mysql_real_escape_string($_GET['otp']) : '';
			$txn_id = isset($_GET['txn_id']) && $_GET['txn_id']!='' ? mysql_real_escape_string($_GET['txn_id']) : '';
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
			include(DIR."/yesbank/transaction-refund.php");
			echo $jsDmtResponse;
			exit();
			
		}else {
					echo '{"ResponseCode":"20","Message":"Invalid DMT request"}';		
					exit();
				}
			} else {
				echo '{"ResponseCode":"22","Message":"Invalid user detail"}';		
				exit();
			}
		} else {
			echo '{"ResponseCode":"31","Message":"Invalid request IP"}';		
			exit();
		}
	} else {
		echo '{"ResponseCode":"32","Message":"Invalid API Key or User ID"}';		
		exit();
	}
} else {
	echo '{"ResponseCode":"33","Message":"Parameters are missing"}';		
	exit();
}
