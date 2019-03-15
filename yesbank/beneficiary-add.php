<?php
if(!defined('BROWSE')) {
	exit('No direct access allowed');
}
$beneficiaryAdd = $yb->cpBeneficiaryAdd($account,$request_txn_no,$ben_mobile,$ben_name,$bank_account,$ifsc);	
$signInResult = $yb->cpIprivSign($beneficiaryAdd);
$verifyResult = $yb->cpIprivVerify($signInResult[1]);	
$cpUrl = $yb->cpUrl();	
// 

$checkOutput = $yb->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);
//

if(strpos($checkExplode['6'], 'ERROR=0')!==false) {	
	$payOutput = $yb->cpRequest($signInResult[1], $cpUrl['pay']);
	$payExplode = explode("\n",$payOutput);	
	
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