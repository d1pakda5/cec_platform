<?php
class GST {	
	
	public function getSellerDetail($type,$user){
		if($type=='1') {
			$result = array('igst_rate'=>'18 %', 'cgst_rate'=>'9 %', 'sgst_rate'=>'9 %', 'cn'=>$user->company_name, 'address'=>$user->bill_address, 'country'=>'India', 'state'=>$user->bill_state, 'city'=>$user->bill_city, 'pin'=>$user->bill_pincode, 'phone'=>$user->mobile, 'email'=>$user->email, 'gstin'=>$user->gstin, 'pan'=>$user->panno);
		} else {
			$result = array('igst_rate'=>'18 %', 'cgst_rate'=>'9 %', 'sgst_rate'=>'9 %', 'cn'=>'Click E-Charge Services Pvt. Ltd.', 'address'=>'Office No. 6, Ganesham I Commercial Complex,<br>Pimple Saudagar', 'country'=>'India', 'state'=>'Maharashtra', 'city'=>'Pune', 'pin'=>'411027', 'phone'=>'020-228677, 8600250250', 'email'=>'ankitsales@gmail.com', 'gstin'=>'27AAGCC1604F1Z4', 'pan'=>'AWBPC1333K');
		}
		return $result;	
	}
	
	public function getItemGroupName($type){
		if($type=='1') {
			$result = "E-Recharge Value Prepaid";	
		} elseif($type=='2') {
			$result = "E-Recharge Value DTH";	
		} elseif($type=='3') {
			$result = "E-Recharge Commission";	
		} elseif($type=='4') {
			$result = "Surcharge on Bill Collection";	
		} elseif($type=='5') {
			$result = "Surcharge on DMT";	
		} else {
			$result = "E-Recharge Value";	
		}
		return $result;	
	}
	
	public function getItemGroupSac($type){
		if($type=='1') {
			$result = "998419";	
		} elseif($type=='2') {
			$result = "998419";	
		} elseif($type=='3') {
			$result = "996111";	
		} elseif($type=='4') {
			$result = "998592";	
		} elseif($type=='5') {
			$result = "997159";	
		} else {
			$result = "9984";	
		}
		return $result;	
	}
		
	public function getOperatorCommission($operatorid,$commissions,$user_type) {
		$result = 0;
		foreach($commissions as $key=>$data) {			
			if($operatorid==$data->operator_id) {
				if($data->commission_type=='p') {
					if($user_type=='1') { 
						$result = $data->comm_api;
					}
					if($user_type=='3') { 
						$result = $data->comm_mdist;
					}
					if($user_type=='4') { 
						$result = $data->comm_dist;
					}
					if($user_type=='5') { 
						$result = $data->comm_ret;
					}
				}
			}
		}
		return $result;
	}
	
	public function getOperatorCommissionAvg($operatorid,$commissions,$user_type) {
		$comms = 0;
		$my_avg_comm = 0;
		$counts = '0';
		$oprid = explode(",",$operatorid);
		foreach($commissions as $key=>$data){
			if(in_array($data->operator_id,$oprid)){ 
				if($user_type=='1') { 
					$comms = $data->comm_api;
				}
				elseif($user_type=='3') { 
					$comms = $data->comm_mdist;
				}
				elseif($user_type=='4') { 
					$comms = $data->comm_dist;
				}
				elseif($user_type=='5') { 
					$comms = $data->comm_ret;
				}
				$my_avg_comm += $comms;
				$counts++;
			}
		}
		$result = $my_avg_comm/$counts;
		return $result;
	}
	
	public function getTotalCommission($amount,$per) {
		$result = $amount*$per/100;
		return $result;
	}
	
	public function getTaxableAmount($amount) {
		$result = $amount*100/118;
		$result = round($result,4);
		return $result;
	}
	
	public function getGSTAmount($amount,$gst_type,$type) {
		if($gst_type=='1') {
			if($gst_type==$type) {
				$result = $amount*18/100;
			} else {
				$result = 0;
			}
		} elseif($gst_type=='2') {
			if($gst_type==$type) {
				$result = $amount*9/100;
			} else {
				$result = 0;
			}
		} else {
			if($gst_type==$type) {
				$result = $amount*0/100;
			} else {
				$result = 0;
			}
		}
		$result = round($result,4);
		return $result;
	}
	
	public function getTaxSummary($amount,$type){
		$result = false;
		$igst_rate = "0 %";
		$igst_amount = "0.00";
		$cgst_rate = "0 %";
		$cgst_amount = "0.00";
		if($type=='1') {
			$igst_rate = "18 %";
			$igst_net = ($amount*100)/118;
			$igst_amount = round($amount-$igst_net,4);
		} else {
			$cgst_rate = "9 %";
			$cgst_net = ($amount*100)/118;
			$cgst_amount = $amount-$cgst_net;
			$cgst_amount = round($cgst_amount/2,4);
		}
		return array('cgst_rate'=>$cgst_rate,'cgst_amount'=>$cgst_amount,'sgst_rate'=>$cgst_rate,'sgst_amount'=>$cgst_amount,'igst_rate'=>$igst_rate,'igst_amount'=>$igst_amount);
	}
	
	public function getMonthList() {
		$months = array('01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April', '05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August', '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December');
		return $months;
	}
	
	public function getRowByItemGroup($statement,$comms,$user_type) {
		global $db;
		$total_gross_amt = 0;
		$comm_per = 0;
		$total_comm_amt = 0;
		$total_net_amt = 0;
		$total_taxable_amt = 0;
		$total_gst_amt = 0;
		$query = $db->query($statement);
		if($db->numRows($query) < 1) {
			$result = array('gross'=>'0', 'per'=>'0', 'com'=>'0', 'net'=>'0', 'taxable'=>'0', 'gst'=>'0');
		}
		while($row = $db->fetchNextObject($query)) {
			$comm_per = $this->getOperatorCommission($row->operator_id,$comms,$user_type);
			$comm_amt = $this->getTotalCommission($row->amt,$comm_per);
			$total_gross_amt += $row->amt;
			$total_comm_amt += $comm_amt;
			$net_amount = $row->amt - $comm_amt;
			$total_net_amt += $net_amount;
			$taxable_amount = $this->getTaxableAmount($net_amount);
			$total_taxable_amt += $taxable_amount;
			$gst_amount = $net_amount - $taxable_amount;
			$total_gst_amt += $gst_amount;
			$rate = $taxable_amount/$row->amt;
			$result = array('gross'=>$total_gross_amt, 'per'=>$comm_per, 'com'=>$total_comm_amt, 'net'=>$total_net_amt, 'taxable'=>$total_taxable_amt, 'gst'=>$total_gst_amt);
		}		
		return $result;
	}
	
	public function getRowByItemGroupSurcharge($statement,$comms,$user_type) {
		global $db;
		$total_amt = 0;
		$total_sur_amt = 0;
		$total_taxable_amt = 0;
		$total_gst_amt = 0;
		$query = $db->query($statement);
		while($row = $db->fetchNextObject($query)) {
			$total_amt += $row->amt;
			$sur_amt = $row->sur_amt;
			$total_sur_amt += $sur_amt;
			$taxable_amount = $this->getTaxableAmount($sur_amt);
			$total_taxable_amt += $taxable_amount;
			$gst_amount = $sur_amt - $taxable_amount;
			$total_gst_amt += $gst_amount;
			
		}
		$result = array('gross'=>$total_amt, 'sur'=>$total_sur_amt, 'net'=>$total_sur_amt, 'taxable'=>$total_taxable_amt, 'gst'=>$total_gst_amt);
		return $result;
	}
}
?>