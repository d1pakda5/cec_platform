<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }
$ben_type = "IMPS";
$amount = "5.00";
$dump = $account."|".$request_txn_no."|".$ben_type."|".$ifsc."|".$ben_code."|".$amount;
$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BENE VALIDATE PRE CHECK', '".$dump."')");

$beneficiaryValidate = $sp->cpBeneficiaryValidate($account,$request_txn_no,$ben_type,$ifsc,$ben_code,$amount);	
$signInResult = $sp->cpIprivSign($beneficiaryValidate);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();

$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);

$dmt_check_status = "2";
$dmt_value_error = '';
$dmt_value_result = '';
$dmt_value_transid = '';
$dmt_value_addinfo = '';
$dmt_value_errmsg = '';

foreach($checkExplode as $chk_data) {
	if(strpos($chk_data, 'ERROR=') !== false) {
		$chk_data_error = explode("=",$chk_data);
		$dmt_value_error = trim($chk_data_error[1]);
	}
	if(strpos($chk_data, 'RESULT=') !== false) {
		$chk_data_result = explode("=",$chk_data);
		$dmt_value_result = trim($chk_data_result[1]);
	}
	if(strpos($chk_data, 'TRANSID=') !== false) {
		$chk_data_transid = explode("=",$chk_data);
		$dmt_value_transid = trim($chk_data_transid[1]);
	}
	if(strpos($chk_data, 'ADDINFO=') !== false) {
		$chk_data_addinfo = explode("=",$chk_data);
		$dmt_value_addinfo = trim($chk_data_addinfo[1]);
	}
	if(strpos($chk_data, 'ERRMSG=') !== false) {
		$chk_data_err = explode("=",$chk_data);
		$dmt_value_errmsg = trim($chk_data_err[1]);
	}
}
		
if($dmt_value_error=='0' && $dmt_value_result=='0') {
	$dmt_check_status = "0";
	$api_txn_no = $dmt_value_transid;
	
	$output = $checkOutput;	
	$http_code = "200";
	
} else {
	if($dmt_value_errmsg=='') {
		$dmt_value_errmsg = "Invalid request details";
	}
	$jsDmtResponse = '{"ResponseCode":"19","Message":"('.$dmt_value_error.'|'.$dmt_value_result.') '.$dmt_value_errmsg.'"}';
	$dmt_check_status = "1";	
}

$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BENE VALIDATE CHECK', '".$checkOutput."')");	