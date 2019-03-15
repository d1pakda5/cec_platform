<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$ybPayDate = "";
$ybPaySession = "";
$ybPayError = "";
$ybPayResult = "";
$ybPayTransId = "";
$ybPayAddInfo = "";
$ybPayAuthCode = "";
$ybPayTrnxStatus = "";
$ybPayErrMsg = "";

$payOutput = $yb->cpRequest($signInResult[1], $cpUrl['pay']);
$payExplode = explode("\n",$payOutput);
foreach($payExplode as $payData) {
	if(strpos($payData, 'DATE=')!==false) {
		$payDate = explode("=",$payData);
		$ybPayDate = trim($payDate[1]);
	}
	if(strpos($payData, 'SESSION=')!==false) {
		$paySession = explode("=",$payData);
		$ybPaySession = trim($paySession[1]);
	}
	if(strpos($payData, 'ERROR=')!==false) {
		$payError = explode("=",$payData);
		$ybPayError = trim($payError[1]);
	}
	if(strpos($payData, 'RESULT=')!==false) {
		$payResult = explode("=",$payData);
		$ybPayResult = trim($payResult[1]);
	}
	if(strpos($payData, 'TRANSID=')!==false) {
		$payTransId = explode("=",$payData);
		$ybPayTransId = trim($payTransId[1]);
	}
	if(strpos($payData, 'ADDINFO=')!==false) {
		$payAddInfo = explode("=",$payData);
		$ybPayAddInfo = trim($payAddInfo[1]);
	}
	if(strpos($payData, 'AUTHCODE=')!==false) {
		$payAuthCode = explode("=",$payData);
		$ybPayAuthCode = trim($payAuthCode[1]);
	}
	if(strpos($payData, 'TRNXSTATUS=')!==false) {
		$payTrnxStatus = explode("=",$payData);
		$ybPayTrnxStatus = trim($payTrnxStatus[1]);
	}
	if(strpos($payData, 'ERRMSG=')!==false) {
		$payErrMsg = explode("=",$payData);
		$ybPayErrMsg = trim($payErrMsg[1]);
	}
}
	
if($ybPayError=='0' && $ybPayResult=='0') {

	$jsFetch = urldecode($ybPayAddInfo);
	$jsDmtResponse = $jsFetch;
	
	$status = "0";
	$status_details = 'Transaction Successful';
	
	$api_status = $jsFetch['RESPONSE'];
	$api_status_details = $jsFetch['RESP_MSG'];

	$api_txn_no = isset($ybPayTransId) ? $ybPayTransId : '';
	$operator_ref_no = isset($ybPayAuthCode) ? $ybPayAuthCode : '';
	
	$output = $payOutput;	
	$http_code = "200";
	
} else {

	$jsDmtResponse = '{"RESP_CODE":"'.$ybPayError.'","RESP_MSG":"Invalid response ('.$ybPayError.' | '.$ybPayResult.') '.$ybPayErrMsg.'"}';	
	$status = "2";
	$status_details = 'Transaction Failed';
	
	$api_status = $ybPayError;
	$api_status_details = isset($ybPayErrMsg) ? $ybPayErrMsg : '';
	$api_txn_no = isset($ybPayTransId) ? $ybPayTransId : '';
	$operator_ref_no = isset($ybPayAuthCode) ? $ybPayAuthCode : '';
}