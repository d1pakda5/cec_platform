<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$otcConfirm = $sp->cpOtcConfirm($account,$request_txn_no,$otc,$otc_ref_no);	
$signInResult = $sp->cpIprivSign($otcConfirm);
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
		
		if($jsFetch['OtpVerify']=='Y') {
			
			$jsDmtResponse = '{"ResponseCode":"0","Message":"OTC verified succesfully","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","OtpVerify":"'.$jsFetch['OtpVerify'].'"}';	
			
		} else {
			
			$jsDmtResponse = '{"ResponseCode":"1","Message":"'.$jsFetch['Message'].'","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","OtpVerify":"'.$jsFetch['OtpVerify'].'"}';
			
		}
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsDmtResponse = '{"ResponseCode":"1","Message":"OTC not verified","MobileNo":"'.$account.'"}';
		
	} elseif(strpos($payExplode['6'], 'ERROR=224')!==false) {
		
		$jsDmtResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try later"}';		
		
	} else {
		
		$jsDmtResponse = '{"ResponseCode":"18","Message":"Response cannot be fetch"}';
		
	}
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'OTC CONFIRM REF', '".$otc_ref_no."')");	
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'OTC CONFIRM PAY', '".$payOutput."')");	
	
} else {

	$jsDmtResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	
}
?>