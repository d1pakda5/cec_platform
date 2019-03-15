<?php
session_start();
define("BROWSE", "browse");
include("../config.php");
include(DIR."/system/class.cyberplat.yesbank.php");
$yb = new CyberPlatYesBank();
$ip = $_SERVER['REMOTE_ADDR'];
$mode = "WEB";
$reference_txn_no = '';
//
$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,dist_id,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE user_id='".$_SESSION['retailer']."'");
if($agent_info) {
	if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
		$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
		$request_txn_no = time();	
		if(isset($_GET['request']) && $_GET['request']=='searchCustomer') {
			include(DIR."/yesbank/search-customer.php");
			echo $jsDmtResponse;
			exit();			
		}
		else if(isset($_GET['request']) && $_GET['request']=='panAdd')
		{   $account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
			$pan_card=isset($_GET['pan_card']) && $_GET['pan_card'] ? mysql_real_escape_string($_GET['pan_card']) : '0';
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			include(DIR."/yesbank/pan_add.php");
			echo $jsDmtResponse;
			exit();
		} else if(isset($_GET['request']) && $_GET['request']=='customerRegistration') {
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';		
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			include(DIR."/yesbank/customer-registration.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/yesbank/beneficiary-add.php");
			echo $jsDmtResponse;
			exit();
			
		}  else if(isset($_GET['request']) && $_GET['request']=='ekycAdd') {
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/yesbank/e-kyc-add.php");
			echo json_encode($jsDmtResponse);
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';			
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '0';			
			include(DIR."/yesbank/beneficiary-validate.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
			include(DIR."/yesbank/beneficiary-delete.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='remittance') {
			$ben_type = isset($_GET['mr_ben_type']) && $_GET['mr_ben_type']!='' ? mysql_real_escape_string($_GET['mr_ben_type']) : '';
			$ifsc = isset($_GET['mr_ben_ifsc']) && $_GET['mr_ben_ifsc']!='' ? mysql_real_escape_string($_GET['mr_ben_ifsc']) : '';
			$ben_code = isset($_GET['mr_ben_code']) && $_GET['mr_ben_code']!='' ? mysql_real_escape_string($_GET['mr_ben_code']) : '';
			$amount = isset($_GET['mr_amount']) && $_GET['mr_amount']!='' ? mysql_real_escape_string($_GET['mr_amount']) : '0';			
			include(DIR."/yesbank/remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otpVerify') {
			$otc = isset($_GET['otc']) && $_GET['otc']!='' ? mysql_real_escape_string($_GET['otc']) : '';
			$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
			include(DIR."/yesbank/otp-verify.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='generateOtp') {
			$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
			include(DIR."/yesbank/generate-otp.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='senderBalance') {
			include(DIR."/yesbank/sender-balance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionHistory') {
			include(DIR."/yesbank/transaction-histroy.php");
			echo $jsDmtResponse;
			exit();
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionRefund') {
			include(DIR."/yesbank/transaction-refund.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='bankIfsc') {
			$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
			$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
			include(DIR."/yesbank/bank-ifsc.php");
			echo $jsDmtResponse;
			exit();
			
		} else {
			echo '{"ResponseCode":"20","Message":"Invalid request"}';		
			exit();
		}
	} else {
		echo '{"ResponseCode":"21","Message":"Invalid sender mobile number"}';		
		exit();
	}
} else {
	echo '{"ResponseCode":"22","Message":"Invalid agent/retailer detail"}';		
	exit();
}