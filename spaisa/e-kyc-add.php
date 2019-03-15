<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$beneficiaryAdd = $sp->cpEkycAdd($account,$request_txn_no,$ben_name,$bank_account,$ifsc);	
$signInResult = $sp->cpIprivSign($beneficiaryAdd);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	
// 	print_r($verifyResult);	
$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);
$checkExplode = explode("\n",$checkOutput);
	
$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BEN ADD PARA', '".$signInResult[1]."')");	
$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BEN ADD CHECK', '".$checkOutput."')");	
	
if(strpos($checkExplode['6'], 'ERROR=0')!==false) {
    
        $htmldata = explode("=",$checkExplode['9']);
        $datahtml=$htmldata['1'];
        $html="data:text/html;charset=utf-8,".$datahtml;
        $jsDmtResponse1=array("html"=>$html);
    	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
    	$payExplode = explode("\n",$payOutput);
//  	print_r($payOutput);
	if(strpos($payExplode['6'], 'ERROR=0')!==false) {
		$jsData = explode("=",$payExplode['9']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsDmtResponse2 = array("response"=>'{"ResponseCode":"0","Message":"Verification code for beneficary add is sent on requested mobile.","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'"}');
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsDmtResponse2 = array("response"=>'{"ResponseCode":"1","Message":"Sender is not registered","MobileNo":"'.$account.'"}');
		
	} elseif(strpos($payExplode['6'], 'ERROR=224')!==false) {
		
			$jsDmtResponse2 = array("response"=>'{"ResponseCode":"17","Message":"Operator server is down, Try Later"}');
		
	} else {
		
			$jsDmtResponse2 = array("response"=>'{"ResponseCode":"18","Message":"Response cannot be fetch"}');
		
	}
	
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'BEN ADD PAY', '".$payOutput."')");	
	
} else {

		$jsDmtResponse2 = array("response"=>'{"ResponseCode":"19","Message":"Invalid request parameters"}');
}
// print_r($jsDmtResponse);
$jsDmtResponse=(array_merge($jsDmtResponse1,$jsDmtResponse2));
?>