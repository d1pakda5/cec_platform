<?php
$api_status = "Check Manully";
$api_status_details = "Check Manully";
$operator_ref_no = "";
//$url = "http://rechargeapp.exioms.com/api/checkStatusMyTxid.php?transId=".$api_txn_no;
$url = "http://appone.exioms.com/api/v3_1/status.php/getStatusByTransNumber?strUsername=".$exioms['uname']."&strAuthKey=".$exioms['key']."&strTransNo=".$api_txn_no."&format=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);

if($output !== false) {	
	//Status
	$json_output = json_decode($output, true);
	if($json_output["success"]=='1') {
		$new_output = $json_output["StatusDetails"][0];
		$json = json_decode(json_encode($new_output), true);
		
		$api_txn_no = isset($json['trans_number']) && $json['trans_number'] != '' ? $json['trans_id'] : '';
		$api_status = isset($json['status']) ? $json['status'] : '';
		$api_status_details = isset($json['message']) ? $json['message'] : '';
		$operator_ref_no = isset($json['operator_transid']) && $json['operator_transid'] != ''  ? $json['operator_transid'] : '';	
	}
}
