<?php
if(!defined('BROWSE')) {
	exit('No direct access allowed');
}
//
$ybCheckStatus = "2";
$ybCheckError = '';
$ybCheckResult = '';
$ybCheckTransId = '';
$ybCheckAddInfo = '';
$ybCheckErrMsg = '';
//
$beneficiaryValidate = $yb->cpBeneficiaryValidate($account,$request_txn_no,$ben_id,$ben_mobile,$ben_name,$ben_account,$ben_bank,$ben_ifsc,$kyc_status);	
$signInResult = $yb->cpIprivSign($beneficiaryValidate);
$verifyResult = $yb->cpIprivVerify($signInResult[1]);	
$cpUrl = $yb->cpUrl();

$checkOutput = $yb->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);
foreach($checkExplode as $chk_data) {
	if(strpos($chk_data, 'ERROR=') !== false) {
		$chk_data_error = explode("=",$chk_data);
		$ybCheckError = trim($chk_data_error[1]);
	}
	if(strpos($chk_data, 'RESULT=') !== false) {
		$chk_data_result = explode("=",$chk_data);
		$ybCheckResult = trim($chk_data_result[1]);
	}
	if(strpos($chk_data, 'TRANSID=') !== false) {
		$chk_data_transid = explode("=",$chk_data);
		$ybCheckTransId = trim($chk_data_transid[1]);
	}
	if(strpos($chk_data, 'ADDINFO=') !== false) {
		$chk_data_addinfo = explode("=",$chk_data);
		$ybCheckAddInfo = trim($chk_data_addinfo[1]);
	}
	if(strpos($chk_data, 'ERRMSG=') !== false) {
		$chk_data_err = explode("=",$chk_data);
		$ybCheckErrMsg = trim($chk_data_err[1]);
	}
}
		
if($ybCheckError=='0' && $ybCheckResult=='0') {
	$ybCheckStatus = "0";
	$api_txn_no = $ybCheckTransId;
	
	$output = $checkOutput;	
	$http_code = "200";
	
} else {
	if($ybCheckErrMsg=='') {
		$ybCheckErrMsg = "Invalid request details";
	}
	$jsDmtResponse = '{"RESP_CODE":"19","RESP_MSG":"('.$ybCheckError.'|'.$ybCheckResult.') '.$ybCheckErrMsg.'"}';
	$ybCheckStatus = "1";	
}
?>