<?php
session_start();
define("BROWSE", "browse");
include("../config.php");
include(DIR."/dmt3/class.cyberplat.dmt3.php");
$dm = new CyberPlatdmt3();
$ip = $_SERVER['REMOTE_ADDR'];
$mode = "WEB";
$reference_txn_no = '';

$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$_SESSION['retailer']."' ");
if($agent_info) {
	if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
		$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
		$request_txn_no = time();	
		if(isset($_GET['request']) && $_GET['request']=='senderValidation') {
			include(DIR."/dmt3/sender-validation.php");
			echo $jsDmtResponse;
			exit();
			
		} 
		else if(isset($_GET['request']) && $_GET['request']=='panAdd')
		{   $account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
			$pan_card=isset($_GET['pan_card']) && $_GET['pan_card'] ? mysql_real_escape_string($_GET['pan_card']) : '0';
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			include(DIR."/dmt3/pan_add.php");
			echo $jsDmtResponse;
			exit();
		}
		else if(isset($_GET['request']) && $_GET['request']=='senderRegistration') {
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';
		
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			$pin = isset($_GET['pin']) && $_GET['pin']!='' ? mysql_real_escape_string($_GET['pin']) : '';
			include(DIR."/dmt3/sender-registration.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
			$ben_fname = isset($_GET['ben_fname']) && $_GET['ben_fname']!='' ? mysql_real_escape_string($_GET['ben_fname']) : '';
			$ben_lname = isset($_GET['ben_lname']) && $_GET['ben_lname']!='' ? mysql_real_escape_string($_GET['ben_lname']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$remId = isset($_GET['remId']) && $_GET['remId']!='' ? mysql_real_escape_string($_GET['remId']) : '';
			include(DIR."/dmt3/beneficiary-add.php");
			echo $jsDmtResponse;
			exit();
			
		}  else if(isset($_GET['request']) && $_GET['request']=='ekycAdd') {
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/dmt3/e-kyc-add.php");
			echo json_encode($jsDmtResponse);
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$benId = isset($_GET['benId']) && $_GET['benId']!='' ? mysql_real_escape_string($_GET['benId']) : '';
           
            $uid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : '';
            
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '0';			
			include(DIR."/dmt3/beneficiary-validate.php");
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
			$remId = isset($_GET['remId']) && $_GET['remId']!='' ? mysql_real_escape_string($_GET['remId']) : '';
			$benId = isset($_GET['benId']) && $_GET['benId']!='' ? mysql_real_escape_string($_GET['benId']) : '';
			include(DIR."/dmt3/beneficiary-delete.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='moneyRemittance') {
			
			$ifsc = isset($_GET['mr_ben_ifsc']) && $_GET['mr_ben_ifsc']!='' ? mysql_real_escape_string($_GET['mr_ben_ifsc']) : '';
			$benId = isset($_GET['mr_ben_code']) && $_GET['mr_ben_code']!='' ? mysql_real_escape_string($_GET['mr_ben_code']) : '';
			$amount = isset($_GET['mr_amount']) && $_GET['mr_amount']!='' ? mysql_real_escape_string($_GET['mr_amount']) : '0';			
			include(DIR."/dmt3/money-remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='reInitiateRemittance') {
			$trans_ref_no = isset($_GET['trans_ref_no']) && $_GET['trans_ref_no']!='' ? mysql_real_escape_string($_GET['trans_ref_no']) : '';
			$ben_type = isset($_GET['ben_type']) && $_GET['ben_type']!='' ? mysql_real_escape_string($_GET['ben_type']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			$ben_code = isset($_GET['ben_code']) && $_GET['ben_code']!='' ? mysql_real_escape_string($_GET['ben_code']) : '';
			$amount = isset($_GET['amount']) && $_GET['amount']!='' ? mysql_real_escape_string($_GET['amount']) : '';
			include(DIR."/dmt3/reinitiate-remittance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otcConfirm') {
			$otc = isset($_GET['otc']) && $_GET['otc']!='' ? mysql_real_escape_string($_GET['otc']) : '';
			$otc_for = isset($_GET['otc_for']) && $_GET['otc_for']!='' ? mysql_real_escape_string($_GET['otc_for']) : '';
			$benId = isset($_GET['benId']) && $_GET['benId']!='' ? mysql_real_escape_string($_GET['benId']) : '';
			$remId = isset($_GET['remId']) && $_GET['remId']!='' ? mysql_real_escape_string($_GET['remId']) : '';
			include(DIR."/dmt3/otc-confirm.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otcResend') {
			$otc_ref_no = isset($_GET['otc_ref_no']) && $_GET['otc_ref_no']!='' ? mysql_real_escape_string($_GET['otc_ref_no']) : '';
			include(DIR."/dmt3/otc-resend.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='senderBalance') {
			include(DIR."/dmt3/sender-balance.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='senderTransaction') {
			include(DIR."/dmt3/sender-transaction.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='bankIfsc') {
			$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
			$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
			include(DIR."/dmt3/bank-ifsc.php");
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