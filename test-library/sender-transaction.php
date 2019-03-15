<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';

if($test=='0') {
	$jsJson = 'ADDINFO={"RequestNo":"57f38859cfdb0","MobileNo":"9870228544","Count":6,"TransDetails":[{"AgentTransId":"1000990146783","MrTransId":"G9SFM1321842793","TopupTransId":"G9SFL859245750408513","TransDateTime":"28 Sep 2016 15:55 pm","Amount":"10","Status":"SUCCESS","Reinitiate":"FALSE","BenefAccNo":"123610110001416","OriginalTransId":"","Remark":"NB~Remittance transaction is successful"},{"AgentTransId":"1000990256415","MrTransId":"G9SHN2368820531","TopupTransId":"G9SHM982066159220751","TransDateTime":"28 Sep 2016 17:58 pm","Amount":"10","Status":"FAILED","Reinitiate":"FALSE","BenefAccNo":"121000","OriginalTransId":"","Remark":"NB~Invalid OR Unverified beneficiary credentials"},{"AgentTransId":"7465460","MrTransId":"G9SIE7244777011","TopupTransId":"","TransDateTime":"28 Sep 2016 18:07 pm","Amount":"10","Status":"SUCCESS","Reinitiate":"TRUE","BenefAccNo":"123610110001416","OriginalTransId":"1000990256415","Remark":"NB~Remittance transaction is successful"},{"AgentTransId":"1000990746208","MrTransId":"G9TA82538913684","TopupTransId":"G9TA7973186745809580","TransDateTime":"29 Sep 2016 10:11 am","Amount":"1","Status":"SUCCESS","Reinitiate":"FALSE","BenefAccNo":"11111268860","OriginalTransId":"","Remark":"NB~Remittance transaction is successful"},{"AgentTransId":"1000990755970","MrTransId":"G9TA82026513391","TopupTransId":"G9TA7885258454957836","TransDateTime":"29 Sep 2016 10:21 am","Amount":"1","Status":"FAILED","Reinitiate":"FALSE","BenefAccNo":"121000","OriginalTransId":"","Remark":"NB~Invalid OR Unverified beneficiary credentials"},{"AgentTransId":"1000991173149","MrTransId":"G9TIK3201350822","TopupTransId":"G9TIJ970172062133345","TransDateTime":"29 Sep 2016 18:16 pm","Amount":"10","Status":"SUCCESS","Reinitiate":"FALSE","BenefAccNo":"100703130020262","OriginalTransId":"","Remark":""}],"Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsData = explode("=",$jsJson);
	$jsFetch = json_decode($jsData['1'],true);
	$jsTransaction = json_encode($jsFetch['TransDetails']);
	
	$jsResponse = '{"ResponseCode":"0","Message":"Request process successful","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","Transaction":'.$jsTransaction.'}';		
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