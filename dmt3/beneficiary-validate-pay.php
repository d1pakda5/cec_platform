<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$dmPayDate = "";
$dmPaySession = "";
$dmPayError = "";
$dmPayResult = "";
$dmPayTransId = "";
$dmPayAddInfo = "";
$dmPayAuthCode = "";
$dmPayTrnxStatus = "";
$dmPayErrMsg = "";

$payOutput = $dm->cpRequest($signInResult[1], $cpUrl['pay']);
$payExplode = explode("\n",$payOutput);
// print_r($payExplode);
foreach($payExplode as $payData) {
	if(strpos($payData, 'DATE=')!==false) {
		$payDate = explode("=",$payData);
		$dmPayDate = trim($payDate[1]);
	}
	if(strpos($payData, 'SESSION=')!==false) {
		$paySession = explode("=",$payData);
		$dmPaySession = trim($paySession[1]);
	}
	if(strpos($payData, 'ERROR=')!==false) {
		$payError = explode("=",$payData);
		$dmPayError = trim($payError[1]);
	}
	if(strpos($payData, 'RESULT=')!==false) {
		$payResult = explode("=",$payData);
		$dmPayResult = trim($payResult[1]);
	}
	if(strpos($payData, 'TRANSID=')!==false) {
		$payTransId = explode("=",$payData);
		$dmPayTransId = trim($payTransId[1]);
	}
	if(strpos($payData, 'ADDINFO=')!==false) {
		$payAddInfo = explode("=",$payData);
		$dmPayAddInfo = trim($payAddInfo[1]);
	}
	if(strpos($payData, 'AUTHCODE=')!==false) {
		$payAuthCode = explode("=",$payData);
		$dmPayAuthCode = trim($payAuthCode[1]);
	}
	if(strpos($payData, 'TRNXSTATUS=')!==false) {
		$payTrnxStatus = explode("=",$payData);
		$dmPayTrnxStatus = trim($payTrnxStatus[1]);
	}
	if(strpos($payData, 'ERRMSG=')!==false) {
		$payErrMsg = explode("=",$payData);
		$dmPayErrMsg = trim($payErrMsg[1]);
	}
}
	
if($dmPayError=='0' && $dmPayResult=='0') {

	$jsFetch = urldecode($dmPayAddInfo);
	$jsDmtResponse = $jsFetch;
	
	$status = "0";
	$status_details = 'Transaction Successful';
	
	$api_status = $jsFetch['RESPONSE'];
	$api_status_details = $jsFetch['RESP_MSG'];

	$api_txn_no = isset($dmPayTransId) ? $dmPayTransId : '';
	$operator_ref_no = isset($dmPayAuthCode) ? $dmPayAuthCode : '';
	
	$output = $payOutput;	
	$http_code = "200";
	
} else {

	$jsDmtResponse = '{"RESP_CODE":"'.$dmPayError.'","RESP_MSG":"Invalid response ('.$dmPayError.' | '.$dmPayResult.') '.$dmPayErrMsg.'"}';	
	$status = "2";
	$status_details = 'Transaction Failed';
	
	$api_status = $dmPayError;
	$api_status_details = isset($dmPayErrMsg) ? $dmPayErrMsg : '';
	$api_txn_no = isset($dmPayTransId) ? $dmPayTransId : '';
	$operator_ref_no = isset($dmPayAuthCode) ? $dmPayAuthCode : '';
}