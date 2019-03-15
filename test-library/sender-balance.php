<?php
//if(!defined('BROWSE')) { exit('No direct access allowed'); }
$test = isset($_GET['test']) && $_GET['test']!='' ? mysql_real_escape_string($_GET['test']) : '0';
		
if($test=='0') {
	$jsData = '{"RequestNo":"57eca0f3f2218","MobileNo":"9870228544","Balance":"1","Response":"SUCCESS","Message":"REQUEST SUCCESSFULLY COMPLETED.","Code":"300"}';
	
	$jsFetch = json_decode($jsData,true);	
	$jsResponse = '{"ResponseCode":"0","Message":"Request process successful","RequestNo":"'.$jsFetch['RequestNo'].'","MobileNo":"'.$jsFetch['MobileNo'].'","Balance":"'.$jsFetch['Balance'].'"}';		
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