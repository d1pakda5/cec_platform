<?php
$sw_operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_name FROM operators opr LEFT JOIN api_list api ON opr.api_id = api.api_id WHERE opr.operator_id = '".$operator_info->operator_id."' ");
if($sw_operator_info->is_auto_switch == 'y') {
	$count = '0';
	$query = $db->query("SELECT * FROM apps_recharge WHERE operator_id = '".$sw_operator_info->operator_id."' AND api_id = '".$sw_operator_info->api_id."' ORDER BY recharge_id DESC LIMIT 5");
	while($result = $db->fetchNextObject($query)) {
		if($result->status == '2') {
			$count++;
		}
	}
	if($count == '5') {
		if($sw_operator_info->api_id == '10') {
			$db->execute("UPDATE `operators` SET `api_id`='1' WHERE operator_id = '".$sw_operator_info->operator_id."'");
			$sw_remark = $sw_operator_info->api_name." to EGPAY API";
			$db->execute("INSERT INTO `api_switch_notification`(`switch_id`, `operator_name`, `switch_info`, `switch_date`, `is_read`) VALUES ('', '".$sw_operator_info->operator_name."', '".$sw_remark."', NOW(), '0') ");				
			
		} else {
			$db->execute("UPDATE `operators` SET `api_id`='10' WHERE operator_id = '".$sw_operator_info->operator_id."'");
			$sw_remark = $sw_operator_info->api_name." to Cyberplat API";
			$db->execute("INSERT INTO `api_switch_notification`(`switch_id`, `operator_name`, `switch_info`, `switch_date`, `is_read`) VALUES ('', '".$sw_operator_info->operator_name."', '".$sw_remark."', NOW(), '0') ");	
		}
	}
}
?>