<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsData = '{"RequestNo":"57ec981ee8816","CardExists":"Y","CardDetail":{"MobileNo":"9870228544","Balance":"0","RemitLimitAvailable":9980},"Beneficiary":{"TAG0":{"BeneficiaryCode":"FIcEW","BeneficiaryName":"SamirK","BeneficiaryType":"NEFT","AccountNumber":"100703130020262","AccountType":"Savings","IFSC":"SVCB0007007","Active":"ACTIVE"},"TAG1":{"BeneficiaryCode":"kEdTR","BeneficiaryName":"MANOJ","BeneficiaryType":"NEFT\/IMPS","AccountNumber":"121000","AccountType":"Savings","IFSC":"BKID0001236","Active":"ACTIVE"},"TAG2":{"BeneficiaryCode":"lQ6Yq","BeneficiaryName":"PRASAD RAVINDRA BHOI","BeneficiaryType":"NEFT\/IMPS","AccountNumber":"123610110001416","AccountType":"Savings","IFSC":"BKID0001236","Active":"ACTIVE"}},"Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsFetch = json_decode($jsData,true);
	$jsCardDetail = json_encode($jsFetch['CardDetail']);	
	$jsBeneficary = json_encode($jsFetch['Beneficiary']);		
	$jsMobile = json_decode($jsCardDetail,true);
	
	$jsResponse = '{"ResponseCode":"0","Message":"Sender is already registered","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsMobile['MobileNo'].'","SenderRegistered":"'.$jsFetch['CardExists'].'","SenderDetail":'.$jsCardDetail.',"Beneficiary":'.$jsBeneficary.'}';		
	echo $jsResponse;
	exit();			
	
} else if($test=='23') {
	
	$jsResponse = '{"ResponseCode":"1","Message":"Sender is not registered","MobileNo":"'.$account.'"}';			
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