<?php
/*
* Exioms API 
*/
$request_txn_no = $recharge_id;
$url = "http://rechargeapp.exioms.com/api/rechargeapi.php?cid=".$exioms['cid']."&authkey=".$exioms['key']."&type=".$operator_info->service_type_exioms."&operator=".$operator_info->code_exioms."&mobile=".$account."&amount=".$amount."&source=API&ip=".$_SERVER['REMOTE_ADDR']."&osdetail=Server";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$output = curl_exec($ch);
curl_close($ch);

if($output) {
	//Recharge Successfully Proceeded.,1109150334012757,1
	$csv = explode(",", $output);
	$api_status = $csv[2];
	$api_txn_no = $csv[1];
	$api_status_details = $csv[0];
	$operator_ref_no = '';
	
	if($api_status == '2') {	
		$status = '0';
		$status_details = 'Transaction Successful';	
	} else if($api_status == '3') {
		$status = '2';
		$status_details = 'Transaction Failed';		
	} else if ($api_status == '1') {
		$status = '1';
		$status_details = 'Transaction Pending';			
	} else {
		$status = '1';
		$status_details = 'Transaction Pending';
			
	}	
}