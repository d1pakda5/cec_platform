<?php
define("BROWSE", "browse");
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];

include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();
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
		if($request_txn_no) {
			$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$user_api_info->user_id."' ");
			if($agent_info) {	
				if($request=='senderValidation') {
					include(DIR."/spaisa/sender-validation.php");
					echo $jsDmtResponse;
					exit();
					
				} else if($request=='senderRegistration') {
					$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
					$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
					include(DIR."/spaisa/sender-registration.php");
					echo $jsDmtResponse;
					exit();
					
				} else if($request=='beneficiaryAdd') {
					$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
					$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
					$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
					include(DIR."/spaisa/beneficiary-add.php");
					echo $jsDmtResponse;
					exit();
					
				} else if($request=='beneficiaryValidate') {
					$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
					$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
					$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
					$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
					include(DIR."/spaisa/beneficiary-validate.php");
					echo $jsDmtResponse;
					exit();
					
				} else if($request=='beneficiaryDelete') {
					$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
					$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
					include(DIR."/spaisa/beneficiary-delete.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='moneyRemittance') {
					$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
					$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
					$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
					$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
					include(DIR."/spaisa/money-remittance.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='reInitiateRemittance') {
					$trans_ref_no = isset($_GET['trans_ref_no']) && $_GET['trans_ref_no']!='' ? mysql_real_escape_string($_GET['trans_ref_no']) : '';
					$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
					$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
					$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
					$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
					include(DIR."/spaisa/reinitiate-remittance.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='otcConfirm') {
					$otc = isset($_GET['otc']) && $_GET['otc']!='' ? mysql_real_escape_string($_GET['otc']) : '';
					$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
					include(DIR."/spaisa/otc-confirm.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='otcResend') {
					$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
					include(DIR."/spaisa/otc-resend.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='senderBalance') {
					include(DIR."/spaisa/sender-balance.php");
					echo $jsDmtResponse;
					exit();
					
				} elseif($request=='senderTransaction') {
					include(DIR."/spaisa/sender-transaction-test.php");
					print_r( $jsDmtResponse );
					exit();
					
				} elseif($request=='bankIfsc') {
					$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
					$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
					include(DIR."/spaisa/bank-ifsc.php");
					echo $jsDmtResponse;
					exit();
					
				} else {
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
