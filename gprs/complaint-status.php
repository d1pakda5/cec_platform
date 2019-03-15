<?php
$complaint = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$msg_param."' AND ( complaint_by = '".$user_info->uid."' OR uid = '".$user_info->uid."' ) ");
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
	echo "Not Found";
}