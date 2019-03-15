<?php
session_start();
define("BROWSE", "browse");
include("../config.php");
include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();
if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
	$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
	$request_txn_no = time();	
	if(isset($_GET['request']) && $_GET['request']=='senderValidation') {
		include(DIR."/test-library/sender-validation.php");
		
	} else if(isset($_GET['request']) && $_GET['request']=='senderRegistration') {
		$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
		$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
		include(DIR."/test-library/sender-registration.php");
		
	} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
		$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
		$bank_account = isset($_GET['bank_account']) && $_GET['bank_account']!='' ? mysql_real_escape_string($_GET['bank_account']) : '';
		$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
		include(DIR."/test-library/beneficiary-add.php");
		
	} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
		$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
		$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
		$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
		$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
		
		include(DIR."/test-library/beneficiary-validate.php");
		
	} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
		$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
		$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
		include(DIR."/test-library/beneficiary-delete.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='moneyRemittance') {
		$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
		$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
		$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
		$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
		$amount_all = isset($_GET['totalamount']) && $_GET['totalamount']!='' ? mysql_real_escape_string($_GET['totalamount']) : '';
		include(DIR."/test-library/money-remittance.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='reInitiateRemittance') {
		$trans_ref_no = isset($_GET['trans_ref_no']) && $_GET['trans_ref_no']!='' ? mysql_real_escape_string($_GET['trans_ref_no']) : '';
		$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
		$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
		$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
		$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
		include(DIR."/test-library/reinitiate-remittance.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='otcConfirm') {
		$otc = isset($_GET['otc']) && $_GET['otc']!='' ? mysql_real_escape_string($_GET['otc']) : '';
		$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
		include(DIR."/test-library/otc-confirm.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='otcResend') {
		$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
		include(DIR."/test-library/otc-resend.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='senderBalance') {
		include(DIR."/test-library/sender-balance.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='senderTransaction') {
		include(DIR."/test-library/sender-transaction.php");
		
	} elseif(isset($_GET['request']) && $_GET['request']=='bankIfsc') {
		$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
		$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
		include(DIR."/test-library/bank-ifsc.php");
		
	} else {
		echo "Error, Please select a valid service operation";
		exit();
	}
} else {
	echo "Error, Please enter a valid mobile number";
	exit();
}