<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsJson = 'ADDINFO={"RequestNo":"57ec9b6a1ed8f","MoneyRemittance":{"Amount":1,"Charges":"0.00","Recharge":1,"Description":"VALIDATEBENFACCOUNT","selected_transfertype":"IMPS","FundTransno":"G9TA82538913684","PaymentId":"IG9TA83764574590","PaymentStatus":"SUCCESS","IMPSREFNO":"627310390484","BankTransId":"309958","TransferStatus":"SUCCESS","Message":"You\'re A\/C is debited for Rs. 1.00 on 29-09-2016 and A\/C XXXX8860 credited (IMPS Ref no 627310390484).","BenefName":"Mr ASHISH RANJAN","Remarks":"NB~Remittance transaction is successful"},"Beneficiary":{"BeneficiaryCode":"XUCaZ","BeneficiaryName":"Aashish","AccountNo":"11111268860","AccountType":"Savings","IFSC":"SBIN0006379"},"BankDetail":{"BankName":"STATE BANK OF INDIA","BranchName":"BAILEY ROAD","Address":"DIST PATNA, BIHAR 800015 PATNA","State":"BIHAR","City":"PATNA"},"Recharge":{"AgentId":"4","PaycTransId":"G9TA7973186745809580","TransDate":"2016-09-29 10:11:15","Amount":"1","TopupCharge":"2.5","Product":"","Status":"SUCCESS"},"MobileNo":"9870228544","AgentTransId":"1000990746208","Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsData = explode("=",$jsJson);
	$jsFetch = json_decode($jsData['1'],true);
	$jsResponse = '{"ResponseCode":"0","Message":"'.$jsFetch['Message'].'","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$account.'"}';	
	echo $jsResponse;
	exit();
	
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