<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$rows = [];
$query = $db->query("SELECT * FROM gst_monthwise WHERE user_type='0' ORDER BY id ASC LIMIT 1");
while($result = $db->fetchNextObject($query)) {
	$rows[] = $result;
}
//
foreach($rows as $row) {
	$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$row->operator_id."' ");
	$recharge_id = $row->recharge_id;
	$amount = $row->amount;
	$rch_date = $row->rech_date
	//
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	if($agent_info->user_type=='1') {				
		$sCommission = getUsersCommission(trim($agent_info->uid),$row->operator_id,$amount,'api');
	}
	else {
		$sCommission = getUsersCommission(trim($agent_info->dist_id),$row->operator_id,$amount,'r');
	}
	 
	if($sCommission['surcharge']=='y') {
		$rch_comm_type = '1';
		$rch_comm_value = $sCommission['samount'];
		$rch_ds_comm_value = '0';
	} else {
		$rch_comm_type = '0';
		$rch_comm_value = $sCommission['rtCom'];
		$rch_ds_comm_value = $sCommission['dsCom'];
	}
	
	// Retailer GST Calculation
	$tax = getUserGstTxns($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
	if($sCommission['surcharge']=='y') {
		$debit_amount = $amount + $tax['total_debit'];
	} else {
		$debit_amount = $amount - $tax['total_debit'];
	}
	$tax_print = $operator_info->billing_type.", ".$rch_comm_value.", ".$rch_comm_type;
	//
	$db->execute("INSERT INTO `gst_monthlytxn`(`user_type`, `uid`, `rch_date`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$agent_info->user_type."', '".$agent_info->uid."', '".$rch_date."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."')");
	
	//
	if($agent_info->user_type!='1') {								
		//Commission Distributor
		if($sCommission['surcharge']=='n') {
			$ds_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->dist_id."' ");
			$taxDs = getUserGstTxns($ds_info,$operator_info->billing_type,$rch_ds_comm_value,$rch_comm_type);
			//		
			$db->execute("INSERT INTO `gst_monthlytxn`(`user_type`, `uid`, `rch_date`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$ds_info->user_type."', '".$ds_info->uid."', '".$rch_date."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['dsPer']."', '".$rch_ds_comm_value."', '".$operator_info->billing_type."', '".$taxDs['taxable_comm']."', '".$taxDs['gst_rate']."', '".$taxDs['net_comm']."', '".$taxDs['gst_tax']."', '".$taxDs['gst_amount']."')");
		}
	}
	
	$db->execute("UPDATE `gst_monthwise` SET `user_type`='".$agent_info->user_type."' WHERE id='".$row->id."'");
	
}