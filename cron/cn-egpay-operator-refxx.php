<?php
include("../config.php");
$rows = [];		
$query = $db->query("SELECT * FROM apps_recharge WHERE api_id = '1' AND status IN (0,1) AND operator_ref_no = '' ORDER BY request_date DESC LIMIT 10");
while($result = $db->fetchNextObject($query)) {	
	$rows[] = $result;
}//end of while loop

foreach($rows as $row) {
	
	$operator_ref_no = $row->operator_ref_no;
	
	$url = "http://api.egpay.in/GetUpdatedStatus.aspx?UID=".$eg_pay['uid']."&UPASS=".$eg_pay['pass']."&SUBUID=".$eg_pay['subuid']."&SUBUPASS=".$eg_pay['subupass']."&TXNNO=".$row->request_txn_no;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$output = curl_exec($ch);
	curl_close($ch);
	
	if($output !== false) {
		libxml_use_internal_errors(true);
		$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);		
		$api_txn_no = isset($xml['API-Corporate-TXN-ID']) && $xml['API-Corporate-TXN-ID'] != '' ? $xml['API-Corporate-TXN-ID'] : '';
		$api_status = isset($xml['Response_code']) ? $xml['Response_code'] : '';
		$api_status_details = isset($xml['Response-status']) ? $xml['Response-status'] : '';
		$operator_ref_no = isset($xml['Operater-ID']) && $xml['Operater-ID'] != ''  ? $xml['Operater-ID'] : '';
	}
	
	if($api_status == '0' && $operator_ref_no != '') {
		$status = '0';
		$status_details = 'Transaction Successful';
		$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$row->recharge_id."' ");
	}
	
} //End of for loop
?>