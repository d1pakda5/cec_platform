<?php
if(!defined('BROWSE')) { exit('No direct access allowed'); }

$reInitiateRemittance = $sp->cpReInitiateRemittance($account,$request_txn_no,$trans_ref_no,$ben_type,$ifsc,$ben_code,$amount);	
$signInResult = $sp->cpIprivSign($reInitiateRemittance);
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
		$jsMoneyRemittance = json_encode($jsFetch['MoneyRemittance']);
		$jsBeneficiary = json_encode($jsFetch['Beneficiary']);
		
		$jsResponse = '{"ResponseCode":"0","Message":"Money Remittance transaction is successful","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","MoneyRemittance":'.$jsMoneyRemittance.',"Beneficiary":'.$jsBeneficiary.'}';		
		echo $jsResponse;
		exit();
		
	} elseif(strpos($payExplode['6'], 'ERROR=23')!==false) {
		
		$jsData = explode("=",$payExplode['10']);
		$jsFetch = json_decode($jsData['1'],true);
		
		$jsResponse = '{"ResponseCode":"1","Message":"Sender is not registered","MobileNo":"'.$account.'"}';
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