<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$otcResend = $sp->cpOtcResend($account,$request_txn_no,$otc_ref_no);	
$signInResult = $sp->cpIprivSign($otcResend);
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
		
		$jsDmtResponse = '{"ResponseCode":"0","Message":"OTC send on request mobile number","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'"}';
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsDmtResponse = '{"ResponseCode":"1","Message":"OTC not send","MobileNo":"'.$account.'"}';
		
	} elseif(strpos($payExplode['6'], 'ERROR=224')!==false) {
		
		$jsDmtResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try Later"}';
		
	} else {
		
		$jsDmtResponse = '{"ResponseCode":"18","Message":"Response cannot be fetch"}';
		
	}
} else {

	$jsDmtResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	
}
?>