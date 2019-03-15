<?php
$complaint = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$msg_param."' AND ( complaint_by = '".$user_info->uid."' OR uid = '".$user_info->uid."' ) ");
if($complaint) {
	if($complaint->status == '1') {				
		if($complaint->refund_status == '0') {
			$message = "Txn ".$msg_param.", Complaint pending, resolved soon";
			smsSendSingle($user_info->mobile, $message, 'account_status');
			exit();
		} else if($complaint->refund_status == '1') {
			$message = "Txn ".$msg_param.", Complaint resolved, Recharge amount Refunded";
			smsSendSingle($user_info->mobile, $message, 'account_status');
			exit();
		} else if($complaint->refund_status == '2') {
			$message = "Txn ".$msg_param.", Complaint resolved, Recharge Success";
			smsSendSingle($user_info->mobile, $message, 'account_status');
			exit();
		} else if($complaint->refund_status == '3') {
			$message = "Txn ".$msg_param.", Complaint resolved, Transaction Invalid";
			smsSendSingle($user_info->mobile, $message, 'account_status');
			exit();
		}
	} else {
		$message = "Txn ".$msg_param.", Complaint pending, resolved soon";
		smsSendSingle($user_info->mobile, $message, 'account_status');
		exit();
	}
} else {
	echo "Not Found";
}