<?php
/*
* Exioms API 
*/

//$url = "http://rechargeapp.exioms.com/api/rechargeapi.php?cid=".$exioms['cid']."&authkey=".$exioms['key']."&type=".$operator_info->service_type_exioms."&operator=".$operator_info->code_exioms."&mobile=".$account."&amount=".$amount."&source=API&ip=".$_SERVER['REMOTE_ADDR']."&osdetail=Server";

$request_txn_no = $recharge_id;
$url = "http://appone.exioms.com/api/v3_1/rechargeV3.php/MakeMobileRecharge";
$post_fields = "strUsername=".$exioms['uname']."&strAuthKey=".$exioms['key']."&intOperatorType=".$operator_info->service_type_exioms."&intOperatorID=".$operator_info->code_exioms."&strMobile=".$account."&dblAmount=".$amount."&strIPAddress=166.62.16.208&strMyTxID=".$request_txn_no."&intCircleID=0&format=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$output = curl_exec($ch);
curl_close($ch);

if($output) {
	//Recharge Successfully Proceeded.,1109150334012757,1
	$json_output = json_decode($output, true);
	
	if($json_output["success"]=='1') {
		$new_output = $json_output["MobileRechargeDetails"][0];
		$json = json_decode(json_encode($new_output), true);
		
		$api_txn_no = isset($json['trans_id']) && $json['trans_id'] != '' ? $json['trans_id'] : '';
		$api_status = isset($json['recharge_status']) ? $json['recharge_status'] : '';
		$api_status_details = isset($json['message']) ? $json['message'] : '';
		$operator_ref_no = isset($json['opr_trans_id']) && $json['opr_trans_id'] != ''  ? $json['opr_trans_id'] : '';	
		
		if($api_status == 'Success') {	
			$status = '0';
			$status_details = 'Transaction Successful';	
		} else if($api_status == 'Failed') {
			$status = '2';
			$status_details = 'Transaction Failed';		
		} else if ($api_status == 'Pending') {
			$status = '1';
			$status_details = 'Transaction Pending';			
		} else {
			$status = '1';
			$status_details = 'Transaction Pending';			
		}
	} else if($json_output["success"]=='0') {
		$status = '2';
		$status_details = 'Transaction Failed';	
	} else {
		$status = '1';
		$status_details = 'Transaction Pending';
	}	
}