<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$scnt = 0;
//
$rows = [];
$query = $db->query("SELECT * FROM gst_monthwise WHERE bill_type='2' ORDER BY id ASC LIMIT 50");
while($result = $db->fetchNextObject($query)) {
	$rows[] = $result;
}
//
foreach($rows as $row) {
	$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$row->operator_id."' ");
	$amount = $row->amount;
	$surcharge = $row->surcharge;
	$rch_date = $row->rech_date;
	$operator_id = $operator_info->operator_id;
	$billing_type = $operator_info->billing_type;
	$item_group = $operator_info->item_group;
	//
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	if($agent_info->user_type=='1') {				
		$sCommission = getApiCommission(trim($agent_info->uid),$row->operator_id,$amount,$billing_type);
	}
	else {
		$sCommission = getRetCommission(trim($agent_info->dist_id),$row->operator_id,$amount,'r');
	}
	$sCommission['amount']=$amount;
	print_r($sCommission);
	echo "<br>";
	if($billing_type=='3') {
		$rch_comm_type = '1';
		$rch_comm_value = $surcharge;
		$rch_ds_comm_value = '0';
	} else {
		$rch_comm_type = '0';
		$rch_comm_value = $sCommission['rtCom'];
		$rch_ds_comm_value = $sCommission['dsCom'];
	}
	
	// Retailer GST Calculation
	$tax = getGstTxnsForOld($agent_info,$billing_type,$rch_comm_value,$rch_comm_type);
	print_r($tax);
	echo "<br>";
	//
	if($agent_info->user_type=='5') {								
		//Commission Distributor
		$ds_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->dist_id."' ");
		$taxDs = getGstTxnsForOld($ds_info,$billing_type,$rch_ds_comm_value,$rch_comm_type);
		print_r($taxDs);
		echo "<br>";
	}
	//$db->execute("UPDATE `gst_monthwise` SET `is_update`='1' WHERE id='".$row->id."'");	
	echo $scnt++;
	echo "<hr>";
}