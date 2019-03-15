<?php
if(!defined('BROWSE')) {
	exit('No direct access allowed');
}
$moneyRemittance = $dm->cpBeneficiaryRemittance($account,$request_txn_no,$benId,$amount,$amount_all);	
$signInResult = $dm->cpIprivSign($moneyRemittance);
$verifyResult = $dm->cpIprivVerify($signInResult[1]);	
$cpUrl = $dm->cpUrl();
// print_r($verifyResult);
$checkOutput = $dm->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);
// print_r($checkOutput);
$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: CHECK REMITTANCE', '".$checkOutput."')");
$dmCheckStatus = "2";
$dmCheckError = '';
$dmCheckResult = '';
$dmCheckTransId = '';
$dmCheckAddInfo = '';
$dmCheckErrMsg = '';

foreach($checkExplode as $chk_data) {
	if(strpos($chk_data, 'ERROR=') !== false) {
		$chk_data_error = explode("=",$chk_data);
		$dmCheckError = trim($chk_data_error[1]);
	}
	if(strpos($chk_data, 'RESULT=') !== false) {
		$chk_data_result = explode("=",$chk_data);
		$dmCheckResult = trim($chk_data_result[1]);
	}
	if(strpos($chk_data, 'TRANSID=') !== false) {
		$chk_data_transid = explode("=",$chk_data);
		$dmCheckTransId = trim($chk_data_transid[1]);
	}
	if(strpos($chk_data, 'ADDINFO=') !== false) {
		$chk_data_addinfo = explode("=",$chk_data);
		$dmCheckAddInfo = trim($chk_data_addinfo[1]);
	}
	if(strpos($chk_data, 'ERRMSG=') !== false) {
		$chk_data_err = explode("=",$chk_data);
		$dmCheckErrMsg = trim($chk_data_err[1]);
	}
}
		
if($dmCheckError=='0' && $dmCheckResult=='0') {
	$dmCheckStatus = "0";
	$api_txn_no = $dmCheckTransId;
	
	$output = $checkOutput;	
	$http_code = "200";
	
} else {
	if($dmCheckErrMsg=='') {
		$dmCheckErrMsg = "Invalid request details";
	}
	$jsDmtResponse = '{"RESP_CODE":"19","RESP_MSG":"('.$dmCheckError.'|'.$dmCheckResult.') '.$dmCheckErrMsg.'"}';
	$dmCheckStatus = "1";	
}	
?>