<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$senderValidation = $sp->cpPanAdd($account,$request_txn_no,$pan_card,$fname,$lname);	
$signInResult = $sp->cpIprivSign($senderValidation);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	
// print_r($signInResult);
// print_r($verifyResult);
$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
//$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'SENDER VALIDATE CHECK', '".$checkOutput."')");
// 	print_r($checkExplode);
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {
	
	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
	$payExplode = explode("\n",$payOutput);
		
	//$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'SENDER VALIDATE PAY', '".$payOutput."')");
// 	print_r($payExplode);
	if(strpos($payExplode['6'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['9']);
		$jsFetch = json_decode($jsData['1'],true);
		
			$jsDmtResponse = '{"ResponseCode":"0","Message":"PAN Card Updated Successfully","RequestNo":"'.$jsFetch['RequestNo'].'","Response":"'.$jsFetch['Response'].'","Code":'.$jsFetch['Code'].'}';
						
		} else {
			
			$jsDmtResponse = '{"ResponseCode":"1","Message":"PAN Card updation Failed"}';	
						
		}
		
	} 
 else {

	$jsDmtResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	
}
?>