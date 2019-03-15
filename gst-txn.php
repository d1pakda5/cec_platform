<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$fromDate = "2017-06-30 23:59:59";
$rows = [];
$query = $db->query("SELECT * FROM apps_recharge WHERE status IN (0,1) AND request_date > '".$fromDate."' ORDER BY recharge_id ASC LIMIT 1000");
while($result = $db->fetchNextObject($query)) {
	$rows[] = $result;
}
//
foreach($rows as $row) {
	$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$row->operator_id."' ");
	$recharge_id = $row->recharge_id;
	$amount = $row->amount;
	//
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	if($agent_info->user_type=='1') {				
		$sCommission = getUsersCommission(trim($agent_info->uid),$row->operator_id,$amount,'api');
	}
	else {
		$sCommission = getUsersCommission(trim($agent_info->dist_id),$row->operator_id,$amount,'r');
	}
	print_r($sCommission);
	echo "<br><br>";
	 
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
	print_r($tax_print);
	echo "<br>";
	print_r($tax);
	echo "<br><br>";
	//
	//$db->execute("INSERT INTO `gst_trans`(`user_type`, `uid`, `recharge_id`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$agent_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."')");
	print_r("INSERT INTO `gst_trans`(`user_type`, `uid`, `recharge_id`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$agent_info->user_type."', '".$agent_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."')");
	echo "<br><br>";
	
	//
	if($agent_info->user_type!='1') {								
		//Commission Distributor
		if($sCommission['surcharge']=='n') {
			$ds_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->dist_id."' ");
			$taxDs = getUserGstTxns($ds_info,$operator_info->billing_type,$rch_ds_comm_value,$rch_comm_type);				//			
			print_r($taxDs);
			echo "<br><br>";
			//$db->execute("INSERT INTO `gst_trans`(`uid`, `recharge_id`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$ds_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['dsPer']."', '".$rch_ds_comm_value."', '".$operator_info->billing_type."', '".$taxDs['taxable_comm']."', '".$taxDs['gst_rate']."', '".$taxDs['net_comm']."', '".$taxDs['gst_tax']."', '".$taxDs['gst_amount']."')");
			print_r("INSERT INTO `gst_trans`(`user_type`, `uid`, `recharge_id`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$ds_info->user_type."', '".$ds_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['dsPer']."', '".$rch_ds_comm_value."', '".$operator_info->billing_type."', '".$taxDs['taxable_comm']."', '".$taxDs['gst_rate']."', '".$taxDs['net_comm']."', '".$taxDs['gst_tax']."', '".$taxDs['gst_amount']."')");
		}
	}
}