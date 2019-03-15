<?php

include("../config.php");
echo "hi";
$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,dist_id,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE user_id=10255");
print_r($agent_info);

// getUserGstTxns($agent_info,'1','19.34','0');




$bill_type=1;$amount=0.42;$is_surcharge='0';
	$net_comm = 0;
	$tax_comm = 0;
	$tot_deduct_com = $amount;
	$gst_amount = 0;
	if($user && $user->gst_deduct=='1') {	
	$result = $amount*100/118;
	$result = round($result,4);
	 
		$gst_net = $result;
		$tot_deduct_com = $amount;
		//P2P
		if($bill_type=='1') {	
			$net_comm = $gst_net;
			$tax_comm = $amount - $gst_net;
			if($user->has_gst=='1') {
				$tot_deduct_com = $amount;
				$gst_amount = '0';
			} else {			
				$tot_deduct_com = $net_comm;
				$gst_amount = $tax_comm;
			}
		} //P2A
		elseif($bill_type=='2') {	
			$net_comm = $gst_net;
			$tax_comm = $amount - $gst_net;
			$tot_deduct_com = $net_comm;
			$gst_amount = $tax_comm;
		} //SURCHARGE
		elseif($bill_type=='3') {
			if($is_surcharge=='1'){	
				$net_comm = $amount;
				$tax_comm = $amount*18/100;
				$tot_deduct_com = $net_comm + $tax_comm;
				$gst_amount = $tax_comm;
			} else {
				$net_comm = $gst_net;
				$tax_comm = $amount - $gst_net;
				$tot_deduct_com = $amount;
				$gst_amount = $tax_comm;
			}
		}
	}
	
	//TDS Calculation
	$tds_value = $net_comm;
	$tds_rate = 0;
	$tds_amount = 0;
	if($user && $user->tds_deduct=='1') {
		$tds_rate = $user->tds_per;
		if($tds_rate!='' || $tds_rate!='0') {
			$tds_amount = $tds_value*$tds_rate/100;
		}		
	}
	echo "tds".$tds_amount;
		echo "tott".$tot_deduct_com;
	$total_debit = $tot_deduct_com - $tds_amount;
	
	$result = array('taxable_comm'=>$amount,'gst_rate'=>'18.00','net_comm'=>$net_comm,'gst_tax'=>$tax_comm,'gst_amount'=>$gst_amount,'tds_value'=>$tds_value,'tds_rate'=>$tds_rate,'tds_amount'=>$tds_amount,'total_debit'=>$total_debit);
	print_r($result);


?>