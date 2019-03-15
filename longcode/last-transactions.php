<?php
if($user_info->user_type == '3') {
	$sParent = " WHERE mdist_id = '".$user_info->uid."' AND user_type = '5' AND status != '9' ";
} else if($user_info->user_type == '4') {
	$sParent = " WHERE dist_id = '".$user_info->uid."' AND user_type = '5' AND status != '9' ";
} else if($user_info->user_type == '5') {
	$sParent = " WHERE uid = '".$user_info->uid."' AND user_type = '5' AND status != '9' ";
}
$agents = "";
$qry = $db->query("SELECT uid FROM apps_user $sParent ");
while($rslt = $db->fetchNextObject($qry)) {
	$agents .= $rslt->uid.", ";
}
$agents .= "0";
$scnt = 1;
$query = $db->query("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE uid IN ('".$agents."') ORDER BY rch.request_date DESC LIMIT 1 ");
if($db->numRows($query) < 1) {
	echo "No recharge found";
	exit();
} else {
	while($result = $db->fetchNextObject($query)) {
		$status = getRechargeStatusUser($result->status);
		$operator_ref_no = "";
		if($result->operator_ref_no != '') {
			$operator_ref_no = "Ref Id: ".$result->operator_ref_no;
		}
		$message = $status.", Txn ".$result->recharge_id.", ".$result->operator_name.", ".$result->account_no.", Amount ".round($result->amount,2)." Rs. ".$operator_ref_no;	
		smsSendSingle($user_info->mobile, $message, 'account_status');
		exit();
	}
	exit();
}
exit();