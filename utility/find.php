<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$uid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : '0';
$operator_id = isset($_GET['o']) && $_GET['o']!='' ? mysql_real_escape_string($_GET['o']) : '0';
$date = isset($_GET['date']) && $_GET['date']!='' ? mysql_real_escape_string($_GET['date']) : date("Y-m-d");
//
$row = $db->queryUniqueObject("SELECT * FROM gst_monthwise WHERE uid='".$uid."' AND operator_id='".$operator_id."' AND rech_date='".$date."' ");
print_r($row);		
echo "<br><br>";

$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$row->operator_id."' ");
$amount = $row->amount;
$surcharge = $row->surcharge;
$rch_date = $row->rech_date;
//
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	if($agent_info->user_type=='1') {				
		$sCommission = getUsersCommission(trim($agent_info->uid),$row->operator_id,$amount,'api');
	}
	else {
		$sCommission = getRetCommission(trim($agent_info->dist_id),$row->operator_id,$amount,'r');
	}
	
	print_r($sCommission);		
	echo "<br><br>";
	 
	if($sCommission['surcharge']=='y') {
		$rch_comm_type = '1';
		$rch_comm_value = $surcharge;
		$rch_ds_comm_value = '0';
	} else {
		$rch_comm_type = '0';
		$rch_comm_value = $sCommission['rtCom'];
		$rch_ds_comm_value = $sCommission['dsCom'];
	}
	
	// Retailer GST Calculation
	$tax = getGstTxnsForOld($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
	if($sCommission['surcharge']=='y') {
		$debit_amount = $amount + $tax['total_debit'];
	} else {
		$debit_amount = $amount - $tax['total_debit'];
	}
	$tax_print = $operator_info->billing_type.", ".$rch_comm_value.", ".$rch_comm_type;
	print_r($tax);		
	echo "<br><br>";
	//
	//$db->execute("INSERT INTO `gst_monthlytxn`(`user_type`, `uid`, `rch_date`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$agent_info->user_type."', '".$agent_info->uid."', '".$rch_date."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."')");
	
	//
	if($agent_info->user_type!='1') {								
		//Commission Distributor
		if($sCommission['surcharge']=='n') {
			$ds_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->dist_id."' ");
			$taxDs = getGstTxnsForOld($ds_info,$operator_info->billing_type,$rch_ds_comm_value,$rch_comm_type);
			//
			print_r($taxDs);		
			//$db->execute("INSERT INTO `gst_monthlytxn`(`user_type`, `uid`, `rch_date`, `operator_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`) VALUES ('".$ds_info->user_type."', '".$ds_info->uid."', '".$rch_date."', '".$operator_info->operator_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['dsPer']."', '".$rch_ds_comm_value."', '".$operator_info->billing_type."', '".$taxDs['taxable_comm']."', '".$taxDs['gst_rate']."', '".$taxDs['net_comm']."', '".$taxDs['gst_tax']."', '".$taxDs['gst_amount']."')");
		}
	}