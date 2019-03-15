<?php
$api_status = "Check Manully";
$api_status_details = "Check Manully";
$operator_ref_no = "";
$url = "http://rechargeapp.exioms.com/api/checkStatusMyTxid.php?transId=".$api_txn_no;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);

if($output !== false) {	
	//Status
	$exp = explode(",", $output);
	$exp1 = explode(":", $exp[0]);							
	$api_status = ucwords($exp1[1]);		
	$exp2 = explode("*", $exp[1]);
	$account = $exp2[0];		
	$operator_name = $exp2[1];		
	$amount = $exp2[2];		
	$operator_ref_no = isset($exp2[3]) && $exp2[3] != '' ? $exp2[3] : '';	
}
