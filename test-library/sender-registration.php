<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsJson = 'ADDINFO={"RequestNo":"57eb4cb8a1350","MobileNo":"9870228544","CardExists":"N","Response":"SUCCESS","Message":"Verification code for your wallet, send on requested mobile Number.","Code":"300"}';
	
	$jsData = explode("=",$jsJson);
	$jsFetch = json_decode($jsData['1'],true);
	
	if($jsFetch['CardExists']=='N') {
		$jsResponse = '{"ResponseCode":"0","Message":"'.$jsFetch['Message'].'","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","SenderRegistered":"'.$jsFetch['CardExists'].'"}';		
		echo $jsResponse;
		exit();		
	} else {
		$jsResponse = '{"ResponseCode":"1","Message":"Sender is already registered","MobileNo":"'.$account.'"}';			
		echo $jsResponse;
		exit();
	}	
	
} else if($test=='23') {
	
	$jsResponse = '{"ResponseCode":"1","Message":"Sender is already registered","MobileNo":"'.$account.'"}';			
	echo $jsResponse;
	exit();

} else if($test=='224') {
	
	$jsResponse = '{"ResponseCode":"17","Message":"Operator server is down, Try Later"}';			
	echo $jsResponse;
	exit();

} else {

	$jsResponse = '{"ResponseCode":"19","Message":"Invalid request parameters"}';
	echo $jsResponse;
	exit();
}
?>