<?php
if(!defined('BROWSE')) { 
	exit('No direct access allowed');
}
$customerRegistration = $yb->cpCustomerRegistration($account,$request_txn_no,$fname,$lname,$dob,$city,$state,$pincode,$ben_mobile,$ben_name,$ben_account,$ben_ifsc);	
$signInResult = $yb->cpIprivSign($customerRegistration);
$verifyResult = $yb->cpIprivVerify($signInResult[1]);	
$cpUrl = $yb->cpUrl();	

$checkOutput = $yb->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
// $db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: CUSTOMER REGISTRATION CHECK', '".$checkOutput."')");
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {	
	$payOutput = $yb->cpRequest($signInResult[1], $cpUrl['pay']);
	$payExplode = explode("\n",$payOutput);
// 	print_r($payExplode);
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