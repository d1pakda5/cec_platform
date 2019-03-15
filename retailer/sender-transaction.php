<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$senderTransaction = $sp->cpSenderTransaction($account,$request_txn_no);	
$signInResult = $sp->cpIprivSign($senderTransaction);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	

$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);	
	
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {
	
	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'TRANS PAY', '".mysql_real_escape_string($payOutput)."')");
	$payExplode = explode("\n",$payOutput);
		
	if(strpos($payExplode['6'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['9']);
		$jsFetch = json_decode($jsData['1'],true);
		$jsTransaction = json_encode($jsFetch['TransDetails']);
		
		$jsDmtResponse = '{"ResponseCode":"0","Message":"Request process successful","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","Transaction":'.$jsTransaction.'}';	
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsDmtResponse = '{"ResponseCode":"1","Message":"Sender is not registered","MobileNo":"'.$account.'"}';
		
	} elseif(strpos($payExplode['6'], 'ERROR=224')!==false) {
		
		$jsDmtResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try Later"}';
		
	} else {
		
		$jsDmtResponse = '{"ResponseCode":"18","Message":"Response cannot be fetch"}';
		
	}
} else {

	$jsDmtResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	
}
?>