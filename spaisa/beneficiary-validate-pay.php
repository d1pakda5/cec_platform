<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }
// print_r($signInResult[1]);
$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
$payExplode = explode("\n",$payOutput);
// print_r($payExplode);
foreach($payExplode as $payData) {
	if(strpos($payData, 'DATE=')!==false) {
		$payDate = explode("=",$payData);
		$dmt_pay_date = trim($payDate[1]);
	}
	if(strpos($payData, 'SESSION=')!==false) {
		$paySession = explode("=",$payData);
		$dmt_pay_session = trim($paySession[1]);
	}
	if(strpos($payData, 'ERROR=')!==false) {
		$payError = explode("=",$payData);
		$dmt_pay_error = trim($payError[1]);
	}
	if(strpos($payData, 'RESULT=')!==false) {
		$payResult = explode("=",$payData);
		$dmt_pay_result = trim($payResult[1]);
	}
	if(strpos($payData, 'TRANSID=')!==false) {
		$payTransId = explode("=",$payData);
		$dmt_pay_transid = trim($payTransId[1]);
	}
	if(strpos($payData, 'ADDINFO=')!==false) {
		$payAddInfo = explode("=",$payData);
		$dmt_pay_addinfo = trim($payAddInfo[1]);
	}
	if(strpos($payData, 'TRNXSTATUS=')!==false) {
		$payTrnxStatus = explode("=",$payData);
		$dmt_pay_trnxstatus = trim($payTrnxStatus[1]);
	}
	if(strpos($payData, 'AUTHCODE=')!==false) {
		$payAuthCode = explode("=",$payData);
		$dmt_pay_authcode = trim($payAuthCode[1]);
	}
	if(strpos($payData, 'ERRMSG=')!==false) {
		$payErrMsg = explode("=",$payData);
		$dmt_pay_errmsg = trim($payErrMsg[1]);
	}
}
	
if($dmt_pay_error=='0' && $dmt_pay_result=='0') {

	$jsFetch = json_decode($dmt_pay_addinfo,true);	
		
	$jsMoneyRemittance = json_encode($jsFetch['MoneyRemittance']);
	$jsBeneficiary = json_encode($jsFetch['Beneficiary']);
	$jsRecharge = json_encode($jsFetch['Recharge']);	
	
	$jsDmtResponse = '{"ResponseCode":"0","Message":"Validation for beneficary is successful.","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","MoneyRemittance":'.$jsMoneyRemittance.',"Beneficiary":'.$jsBeneficiary.',"Recharge":'.$jsRecharge.'}';	
	
	$status = "0";
	$status_details = 'Transaction Successful';
	
	$api_status = $jsFetch['MoneyRemittance']['TransferStatus'];
	$api_status_details = $jsFetch['MoneyRemittance']['Remarks'];

	$api_txn_no = isset($dmt_pay_transid) ? $dmt_pay_transid : '';
	$operator_ref_no = isset($dmt_pay_authcode) ? $dmt_pay_authcode : '';

	$output = $payOutput;	
	$http_code = "200";

} elseif($dmt_pay_error=='23') {
	
	$jsDmtResponse = '{"ResponseCode":"1","Message":"Sender is not registered","MobileNo":"'.$account.'"}';
	
	$status = "2";
	$status_details = 'Transaction Failed';
	
	$api_status = $dmt_pay_error;
	$api_status_details = isset($dmt_value_errmsg) ? $dmt_value_errmsg : '';
	$api_txn_no = isset($dmt_pay_transid) ? $dmt_pay_transid : '';
	$operator_ref_no = isset($dmt_pay_authcode) ? $dmt_pay_authcode : '';

} else if ($dmt_pay_error=='224') {

	$jsDmtResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try Later"}';	
	
	$status = "2";
	$status_details = 'Transaction Failed';
	
	$api_status = $dmt_pay_error;
	$api_status_details = isset($dmt_pay_errmsg) ? $dmt_pay_errmsg : '';
	$api_txn_no = isset($dmt_pay_transid) ? $dmt_pay_transid : '';
	$operator_ref_no = isset($dmt_pay_authcode) ? $dmt_pay_authcode : '';
	
} else {

	$jsDmtResponse = '{"ResponseCode":"18","Message":"Invalid response ('.$dmt_pay_error.' | '.$dmt_pay_result.') '.$dmt_pay_errmsg.'"}';	
	$status = "2";
	$status_details = 'Transaction Failed';
	
	$api_status = $dmt_pay_error;
	$api_status_details = isset($dmt_pay_errmsg) ? $dmt_pay_errmsg : '';
	$api_txn_no = isset($dmt_pay_transid) ? $dmt_pay_transid : '';
	$operator_ref_no = isset($dmt_pay_authcode) ? $dmt_pay_authcode : '';
}