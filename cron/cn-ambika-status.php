<?php
include("../config.php");
$rows = [];		
$query = $db->query("SELECT * FROM apps_recharge WHERE api_id='9' AND status='1' AND (request_date < DATE_SUB(NOW(), INTERVAL 30 MINUTE)) ORDER BY request_date DESC LIMIT 100");
while($result = $db->fetchNextObject($query)) {	
	$rows[] = $result;
}//end of while loop

foreach($rows as $row) {
	
	$operator_ref_no = $row->operator_ref_no;
	$url = "http://ambikamultiservices.in/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&csagentid=".$row->recharge_id."&fmt=Json";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output !== false) {
		
		if(isJson($output)) {	
			$json = json_decode($output, true);			
		} else {
			libxml_use_internal_errors(true);
			$json = json_decode(json_encode((array) simplexml_load_string($output)), 1);
		}
					
		$api_status = isset($json['STATUS']) && $json['STATUS'] != '' ? $json['STATUS'] : '';
		$account = isset($json['MOBILE']) && $json['MOBILE'] != '' ? $json['MOBILE'] : '';
		$amount = isset($json['AMOUNT']) && $json['AMOUNT'] != '' ? $json['AMOUNT'] : '';
		$api_txn_no = isset($json['RPID']) && $json['RPID'] != '' ? $json['RPID'] : '';
		$operator_ref_no = isset($json['OPID']) && $json['OPID'] != '' ? $json['OPID'] : '';
		$api_status_details = isset($json['MSG']) && $json['MSG'] != '' ? $json['MSG'] : '';	
	}
	
	if($api_status=='SUCCESS') {
		$status = '0';
		$status_details = 'Transaction Successful';	
		
		/*
		* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
		*/
		$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', api_status='', api_status_details='', response_date=NOW(), operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$row->recharge_id."' ");	
	} elseif($api_status=='FAILED') {
		$status = '2';
		$status_details = 'Transaction Failed';	
		
		/*
		* Update recharge response and additional ref params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
		*/
		$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', api_status='', api_status_details='', response_date=NOW(), operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$row->recharge_id."' ");	
	}
	
	echo $row->recharge_id.", Time: ".$row->request_date.", Status: ".$status.", Status Details: ".$status_details.", Operator Ref No: ".$operator_ref_no."<br>";
	
} //End of for loop
?>