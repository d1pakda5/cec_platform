<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$senderRegistration = $sp->cpSenderRegistration($account,$request_txn_no,$fname,$lname);	
$signInResult = $sp->cpIprivSign($senderRegistration);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	

$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
	
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {
	
	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
	$payExplode = explode("\n",$payOutput);
		
	if(strpos($payExplode['6'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['9']);
		$jsFetch = json_decode($jsData['1'],true);
		
		if($jsFetch['CardExists']=='N') {
			
			$jsResponse = '{"ResponseCode":"0","Message":"Verification Code for your wallet, send on requested mobile Number","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","SenderRegistered":"'.$jsFetch['CardExists'].'"}';		
			echo $jsResponse;
			exit();
			
		} else {
			
			$jsResponse = '{"ResponseCode":"1","Message":"Sender is already registered","MobileNo":"'.$account.'"}';
			echo json_encode(json_decode($jsResponse));
			exit();
			
		}
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsResponse = '{"ResponseCode":"1","Message":"Sender is already registered","MobileNo":"'.$account.'"}';
		echo json_encode(json_decode($jsResponse));
		exit();
		
	} elseif(strpos($payExplode['6'], 'ERROR=224')!==false) {
		
		$jsResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try Later"}';
		echo $jsResponse;
		exit();
		
	} else {
		
		$jsResponse = '{"ResponseCode":"18","Message":"Response cannot be fetch"}';
		echo $jsResponse;
		exit();
		
	}
} else {

	$jsResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	echo $jsResponse;
	exit();
}
?>