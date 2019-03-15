<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsJson = 'ADDINFO={"RequestNo":"57ecae6a78f9c","DefaultIfsc":{"IFSC":"CNRB0000404","BankName":"CANARA BANK","BranchName":"BANGALORE CANTONMENT","Address":"88, MAHATMA GANDHI ROAD,, CANTONMENT,, BANGALORE 560001","City":"BANGALORE","State":"KARNATAKA","TransferType":"IMPS"},"BankBranchDetails":[{"IFSC":"CNRB0003302","BankName":"CANARA BANK","BranchName":"VASHI NAVI MUMBAI","Address":"195B THE EMERALD NEXT TO NEEL SIDI TOWERS SECTOR 12 VASHI NAVI MUMBAI","City":"MUMBAI","State":"MAHARASHTRA"}],"Count":"1","Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsData = explode("=",$jsJson);
	$jsFetch = json_decode($jsData['1'],true);
	$jsDefaultIfsc = json_encode($jsFetch['DefaultIfsc']);	
	$jsBranchIfsc = json_encode($jsFetch['BankBranchDetails']);
	
	$jsResponse = '{"ResponseCode":"0","Message":"Sender is already registered","RequestNo":"'.$jsFetch['RequestNo'].'","DefaultIfsc":'.$jsDefaultIfsc.',"Branch":'.$jsBranchIfsc.'}';		
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