<?php
if(!defined('BROWSE')) { 
	exit('No direct access allowed');
}
$customerValidation = $dm->cpCustomerValidation($account,$request_txn_no);	
$signInResult = $dm->cpIprivSign($customerValidation);
$verifyResult = $dm->cpIprivVerify($signInResult[1]);	
$cpUrl = $dm->cpUrl();	
//
//print_r($verifyResult);
$checkOutput = $dm->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
//
//print_r($checkExplode);
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {	
	$payOutput = $dm->cpRequest($signInResult[1], $cpUrl['pay']);
//	print_r($signInResult[1]);
	$payExplode = explode("\n",$payOutput);	
//	print_r($payExplode);
	if(strpos($payExplode['4'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['6']);	
		$jsDmtResponse = urldecode($jsData['1']);
		
// 		$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: CUSTOMER VALIDATE PAY', '".$jsDmtResponse."')");
	} else if(strpos($payExplode['4'], 'ERROR=224')!==false) {		
		$jsData = explode("=",$payExplode['7']);
		$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"'.trim($jsData['1']).'","RQST_MOBILE":"'.$account.'"}';	
	} else {	
		$jsData = explode("=",$payExplode['4']);	
		$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Response cannot be fetch"}';		
	}
} else {
	$jsData = explode("=",$checkExplode['6']);
	$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Invalid request parameters"}';	
}
?>