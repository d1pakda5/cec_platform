<?php
//echo "Error, Service not available";
$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.account_no = '".$msg_param."' ORDER BY rch.request_date DESC ");
if($recharge_info) {
	if($user_info->user_type == '3') {
		$sParent = " AND mdist_id = '".$user_info->uid."' ";
	} else if($user_info->user_type == '4') {
		$sParent = " AND dist_id = '".$user_info->uid."' ";
	} else if($user_info->user_type == '5') {
		$sParent = " AND uid = '".$user_info->uid."' ";
	}
	$recharge_user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$recharge_info->uid."' $sParent ");
	if($recharge_user) {
		$status = getRechargeStatusUser($recharge_info->status);
		$operator_ref_no = "";
		if($recharge_info->operator_ref_no != '') {
			$operator_ref_no = "Ref Id: ".$recharge_info->operator_ref_no;
		}
		echo $status.", Txn ".$recharge_info->recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Amount ".round($recharge_info->amount,2)." Rs. ".$operator_ref_no;	
		exit();
	} else {
		echo "Invalid Recharge Txn No";
		exit();
	}
} else {
	echo "Invalid Txn No";
	exit();
}
exit();