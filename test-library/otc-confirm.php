<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsJson = 'ADDINFO={"RequestNo":"57eb4ee9a9229","MobileNo":"9870228544","OtpVerify":"Y","Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsData = explode("=",$jsJson);
	$jsFetch = json_decode($jsData['1'],true);
	if($jsFetch['OtpVerify']=='Y') {
		$jsResponse = '{"ResponseCode":"0","Message":"OTC verified successfully","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'"}';		
		echo $jsResponse;
		exit();	
	} else {
		$jsResponse = '{"ResponseCode":"2","Message":"OTC is not verified","MobileNo":"'.$account.'"}';			
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