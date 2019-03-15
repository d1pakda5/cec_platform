<?php
header('Content-type: text/html; charset=utf-8');

define("BROWSE", "browse");
include("../config.php");
include(DIR."/yesbank/class.cyberplat.yesbank.php");
$yb = new CyberPlatYesBank();
$ip = $_SERVER['REMOTE_ADDR'];
$mode = "WEB";
$uid=$_GET['user_id'];
$reference_txn_no = '';
//
$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,dist_id,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE user_id='".$uid."'");
if($agent_info) {
	if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
		$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
		$request_txn_no = time();	
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
			//$rsp = $account.",".$request_txn_no.",".$ben_id.",".$ben_name.",".$ben_account.",".$ben_bank.",".$ben_ifsc.",".$kyc_status.",".$amount.",".$amount_all;
			include(DIR."/yesbank/money-remittance.php");
			//$jsDmtResponse = '{"RESP_CODE":"25","RESP_MSG":"'.$rsp.'"}';		
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
			include(DIR."/yesbank/transaction-refund.php");
			echo $jsDmtResponse;
			exit();
			
		} else {
			echo '{"RESP_CODE":"20","RESP_MSG":"Invalid request"}';		
			exit();
		}
	} else {
		echo '{"RESP_CODE":"21","RESP_MSG":"Invalid sender mobile number"}';		
		exit();
	}
} else {
	echo '{"RESP_CODE":"22","RESP_MSG":"Invalid agent/retailer detail"}';		
	exit();
}