<?php
if(!defined('BROWSE')) {
	exit('No direct access allowed');
}
$beneficiaryAdd = $dm->cpBeneficiaryAdd($account,$request_txn_no,$ben_mobile,$ben_fname,$ben_lname,$remId,$bank_account,$ifsc);	
$signInResult = $dm->cpIprivSign($beneficiaryAdd);
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
	$payExplode = explode("\n",$payOutput);	
	//print_r($payExplode);
	if(strpos($payExplode['4'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['6']);
		$jsDmtResponse = urldecode($jsData['1']);		
	} else {
		$jsData = explode("=",$payExplode['4']);		
		$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Response cannot be fetch"}';		
	}	
} else {
	$jsData = explode("=",$checkExplode['6']);
	$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Invalid request parameters"}';	
}
?>