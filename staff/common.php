<?php
$permission = $db->queryUniqueObject("SELECT * FROM apps_admin_permission WHERE admin_id = '".$_SESSION['staff']."' ");
if($permission) {
	$sP['api_user'] = unserialize($permission->api_user);
	$sP['md_user'] = unserialize($permission->md_user);
	$sP['ds_user'] = unserialize($permission->ds_user);
	$sP['rt_user'] = unserialize($permission->rt_user);
	$sP['kyc'] = unserialize($permission->userkyc);
	$sP['move'] = unserialize($permission->moveuser);
	$sP['fund'] = unserialize($permission->fund);
	$sP['complaint'] = unserialize($permission->complaint);
	$sP['is_support'] = $permission->is_support;
	$sP['is_notification'] = $permission->is_notification;
	$sP['operator'] = unserialize($permission->operators);
	$sP['is_mobile'] = $permission->is_mobile;
	$sP['sms'] = unserialize($permission->sms);
	$sP['reports'] = unserialize($permission->reports);
	$sP['operator_active'] = $permission->is_operator_active;
	$sP['is_dir_ret_move'] = $permission->is_dir_ret_move;
	$sP['dir_ret_commission'] = $permission->dir_ret_commission;
	$sP['assign_manager'] = $permission->assign_manager;
	$sP['is_close_api_complaint'] = $permission->is_close_api_complaint;
	$sP['is_express'] = $permission->is_express;
	$sP['offline_recharge'] = $permission->offline_recharge;

} else {
	$sP = array();
}
?>