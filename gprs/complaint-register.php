<?php
//echo "Error, Service not available";
//exit();
$prev_date = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") .' -72 HOURS'));
$exp_msg = explode("A", $msg_param);
$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.recharge_id = '".$msg_param."' ");
if($recharge_info) {
	if($recharge_info->is_refunded == 'y') {
		echo "Recharge has been already refunded";
		exit();
	} else {
		if($user_info->user_type == '3') {
			$sParent = " AND mdist_id = '".$user_info->uid."' ";
		} else if($user_info->user_type == '4') {
			$sParent = " AND dist_id = '".$user_info->uid."' ";
		} else if($user_info->user_type == '5') {
			$sParent = " AND uid = '".$user_info->uid."' ";
		}
		$recharge_user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$recharge_info->uid."' $sParent ");
		if($recharge_user) {
			$operator_ref_no = $recharge_info->operator_ref_no;
			if($operator_ref_no !='' && strlen($operator_ref_no) >= '4' && preg_match('/[0-9]/', $operator_ref_no)) {
				echo "Success, Txn No ".$msg_param.", Mob/Acc. " .$recharge_info->account_no. ", " .$recharge_info->operator_name. ", " .round($recharge_info->amount,2). " Rs, Ref. ID " .$operator_ref_no;
				exit();
			} else {
				$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$msg_param."' ORDER BY transaction_id DESC");
				if($trans_info) {
					if($trans_info->transaction_term == 'RECHARGE') {						
						$complaint = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$msg_param."' ");
						if($complaint) {
							if($complaint->status == '1') {				
								if($complaint->refund_status == '0') {
									echo "Complaint pending, resolved soon";
									exit();
								} else if($complaint->refund_status == '1') {
									echo "Complaint resolved, Recharge amount Refunded";
									exit();
								} else if($complaint->refund_status == '2') {
									echo "Complaint resolved, Recharge Success";
									exit();
								} else if($complaint->refund_status == '3') {
									echo "Complaint resolved, Transaction Invalid";
									exit();
								}
							} else {
								echo "Complaint pending, resolved soon";
								exit();
							}
						} else {
							if($recharge_info->request_date > $prev_date) {
								$db->execute("INSERT INTO `complaints`(`complaint_id`, `complaint_by`, `txn_no`, `uid`, `complaint_date`, `status`, `refund_status`) VALUES ('', '".$user_info->uid."', '".$recharge_info->recharge_id."', '".$recharge_info->uid."', NOW(), '0', '0')");
								$db->execute("UPDATE `apps_recharge` SET `is_complaint`='y' WHERE recharge_id = '".$recharge_info->recharge_id."' ");
								echo "Complaint Registered successfully";
								exit();
							} else {
								echo "Transaction is to old, only registred previous 3 days.";
								exit();
							}								
						}
					} else {
						echo "Recharge has been already refunded";
						exit();
					}
				} else {
					echo "Transaction not found";
					exit();
				}
			}
		} else {
			echo "Invalid Recharge details";
			exit();
		}
	}
} else {
	echo "Invalid Txn No";
	exit();
}
