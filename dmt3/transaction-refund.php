<?php
if(!defined('BROWSE')) { 
	exit('No direct access allowed');
}
$refundTransaction = $yb->cpRefundTransaction($account,$request_txn_no,$otp_ref_code,$otp,$txn_id,$amount);	
$signInResult = $yb->cpIprivSign($refundTransaction);
$verifyResult = $yb->cpIprivVerify($signInResult[1]);	
$cpUrl = $yb->cpUrl();	
//

$checkOutput = $yb->cpRequest($signInResult[1], $cpUrl['check']);
$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: REFUND CHECK', '".$checkOutput."')");
$checkExplode = explode("\n",$checkOutput);	
//

if(strpos($checkExplode['6'], 'ERROR=0')!==false) {	
	$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: REFUND PAY', 'IS ERROR 0')");
	$payOutput = $yb->cpRequest($signInResult[1], $cpUrl['pay']);
	$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: REFUND PAY', '".$payOutput."')");
	$payExplode = explode("\n",$payOutput);	

	if(strpos($payExplode['6'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['9']);	
		$jsDmtResponse = urldecode($jsData['1']);
		$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: REFUND RESPONSE', '".$jsDmtResponse."')");
	} else if(strpos($payExplode['6'], 'ERROR=224')!==false) {		
		$jsData = explode("=",$payExplode['7']);
		$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"'.trim($jsData['1']).'","RQST_MOBILE":"'.$account.'"}';	
	} else {	
		$jsData = explode("=",$payExplode['6']);	
		$jsDmtResponse = '{"RESP_CODE":"'.$jsData['1'].'","RESP_MSG":"Response cannot be fetch"}';		
	}
} else {
	$db->execute("INSERT INTO `yb_logs`(`log_date`, `log_type`, `log_content`) VALUES (NOW(), 'YB: REFUND PAY', '".$checkExplode['6']."')");
	$jsData = explode("=",trim($checkExplode['6']));
	$jsDmtResponse = '{"RESP_CODE":"'.trim($jsData['1']).'","RESP_MSG":"Invalid request parameters"}';	
	//$jsDmtResponse = '{"RESP_CODE":"7","RESP_MSG":"Invalid request parameters"}';
}
?>