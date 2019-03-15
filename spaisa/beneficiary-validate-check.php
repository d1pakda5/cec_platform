<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }
$ben_type = "NEFT";

$beneficiaryValidate = $sp->cpBeneficiaryValidate($account,$request_txn_no,$ben_type,$ifsc,$ben_code,$amount);	
$signInResult = $sp->cpIprivSign($beneficiaryValidate);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();
// print_r($signInResult[1]);
$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BENE VALIDATE RSP CHECK', '".$checkOutput."')");
$checkExplode = explode("\n",$checkOutput);
// print_r($checkExplode);
$dmt_check_status = "2";
$dmt_check_error = '';
$dmt_check_result = '';
$dmt_check_transid = '';
$dmt_check_addinfo = '';
$dmt_check_errmsg = '';

foreach($checkExplode as $chk_data) {
	if(strpos($chk_data, 'ERROR=') !== false) {
		$chk_data_error = explode("=",$chk_data);
		$dmt_check_error = trim($chk_data_error[1]);
	}
	if(strpos($chk_data, 'RESULT=') !== false) {
		$chk_data_result = explode("=",$chk_data);
		$dmt_check_result = trim($chk_data_result[1]);
	}
	if(strpos($chk_data, 'TRANSID=') !== false) {
		$chk_data_transid = explode("=",$chk_data);
		$dmt_check_transid = trim($chk_data_transid[1]);
	}
	if(strpos($chk_data, 'ADDINFO=') !== false) {
		$chk_data_addinfo = explode("=",$chk_data);
		$dmt_check_addinfo = trim($chk_data_addinfo[1]);
	}
	if(strpos($chk_data, 'ERRMSG=') !== false) {
		$chk_data_err = explode("=",$chk_data);
		$dmt_check_errmsg = trim($chk_data_err[1]);
	}
}
		
if($dmt_check_error=='0' && $dmt_check_result=='0') {
	$dmt_check_status = "0";
	$api_txn_no = $dmt_check_transid;
	
	$output = $checkOutput;	
	$http_code = "200";
	
} else {
	if($dmt_check_errmsg=='') {
		$dmt_check_errmsg = "Invalid request details";
	}
	$jsDmtResponse = '{"ResponseCode":"19","Message":"('.$dmt_check_error.'|'.$dmt_check_result.') '.$dmt_check_errmsg.'"}';
	$dmt_check_status = "1";	
}
?>