<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$reInitiateRemittance = $sp->cpReInitiateRemittance($account,$request_txn_no,$trans_ref_no,$ben_type,$ifsc,$ben_code,$amount);	
$signInResult = $sp->cpIprivSign($reInitiateRemittance);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	

$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'REINITATE RSP CHECK', '".mysql_real_escape_string($checkOutput)."')");
$checkExplode = explode("\n",$checkOutput);	

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

	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'REINITATE RSP PAY', '".mysql_real_escape_string($payOutput)."')");
	$payExplode = explode("\n",$payOutput);
	
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
		
		$jsDmtResponse = '{"ResponseCode":"0","Message":"Remittance Re-Initate for beneficary is successful.","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","MoneyRemittance":'.$jsMoneyRemittance.',"Beneficiary":'.$jsBeneficiary.',"Recharge":'.$jsRecharge.'}';	
		
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
	

} else {
	if($dmt_check_errmsg=='') {
		$dmt_check_errmsg = "Invalid request details";
	}
	$jsDmtResponse = '{"ResponseCode":"19","Message":"('.$dmt_check_error.'|'.$dmt_check_result.') '.$dmt_check_errmsg.'"}';
	$dmt_check_status = "1";	
}
?>