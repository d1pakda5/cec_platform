<?php
if(!defined('BROWSE')) { 
	exit('No direct access allowed');
}
$customerRegistration = $dm->cpCustomerRegistration($account,$request_txn_no,$fname,$lname,$pin);	
$signInResult = $dm->cpIprivSign($customerRegistration);
$verifyResult = $dm->cpIprivVerify($signInResult[1]);	
$cpUrl = $dm->cpUrl();	
// print_r($verifyResult);
$checkOutput = $dm->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
// print_r($checkExplode);
// $db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: CUSTOMER REGISTRATION CHECK', '".$checkOutput."')");
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {	
	$payOutput = $dm->cpRequest($signInResult[1], $cpUrl['pay']);
	$payExplode = explode("\n",$payOutput);
//  	print_r($payExplode);
// 	$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: CUSTOMER REGISTRATION PAY', '".$payOutput."')");
	if(strpos($payExplode['4'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['6']);		
		$jsDmtResponse = urldecode($jsData['1']);	
	} else {
		$jsData = explode("=",$payExplode['4']);				
		$jsDmtResponse = '{"RESP_CODE":"'.trim($jsData['1']).'","RESP_MSG":"Response cannot be fetch"}';		
	}
} else {
	$jsData = explode("=",$checkExplode['6']);
	$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Invalid request parameters"}';		
}
?>