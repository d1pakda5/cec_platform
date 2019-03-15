<?php
session_start();
define("BROWSE", "browse");
include("../config.php");
include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();
$ip = $_SERVER['REMOTE_ADDR'];
$mode = "WEB";
$reference_txn_no = '';
$uid = isset($_GET["u"]) && $_GET["u"]!='' ? mysql_real_escape_string($_GET["u"]) : '0';

$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,mdist_id,dist_id,pin FROM apps_user WHERE uid='".$uid."' ");
if($agent_info) {
	if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
		$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
		$request_txn_no = time();	
		if(isset($_GET['request']) && $_GET['request']=='senderValidation') {
			include(DIR."/spaisa/sender-validation.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='senderRegistration') {
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			include(DIR."/spaisa/sender-registration.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['bank_account']) && $_GET['bank_account']!='' ? mysql_real_escape_string($_GET['bank_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/spaisa/beneficiary-add.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';			
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';			
			include(DIR."/spaisa/beneficiary-validate.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
			include(DIR."/spaisa/beneficiary-delete.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='moneyRemittance') {
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '0';			
			include(DIR."/spaisa/money-remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='reInitiateRemittance') {
			$trans_ref_no = isset($_GET['trans_ref_no']) && $_GET['trans_ref_no']!='' ? mysql_real_escape_string($_GET['trans_ref_no']) : '';
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
			include(DIR."/spaisa/reinitiate-remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otcConfirm') {
			$otc = isset($_GET['otc']) && $_GET['otc']!='' ? mysql_real_escape_string($_GET['otc']) : '';
			$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
			include(DIR."/spaisa/otc-confirm.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otcResend') {
			$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
			include(DIR."/spaisa/otc-resend.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='senderBalance') {
			include(DIR."/spaisa/sender-balance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='senderTransaction') {
			include(DIR."/spaisa/sender-transaction.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='bankIfsc') {
			$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
			$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
			include(DIR."/spaisa/bank-ifsc.php");
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