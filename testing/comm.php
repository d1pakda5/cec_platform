<?php
include('../config.php');
$uid = isset($_GET["u"]) && $_GET["u"]!='' ? mysql_real_escape_string($_GET["u"]) : '0';
$opr = isset($_GET["o"]) && $_GET["o"]!='' ? mysql_real_escape_string($_GET["o"]) : '0';
$amount = isset($_GET['a']) && $_GET['a']!='' ? mysql_real_escape_string($_GET['a']) : '0';
if($opr=='102') {
	$opr_code = 'DMTV';
} else if($opr=='103') {
	$opr_code = 'DMTS1';
} else if($opr=='104') {
	$opr_code = 'DMTS2';
} else if($opr=='105') {
	$opr_code = 'DMTS2';
} else {
	$opr_code = 'INVALIDOPERATORCODE';
}

$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".trim($uid)."' AND status='1' ");
if($agent_info) {
	$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_code='".$opr_code."' ");
	if($operator_info) {
		if($agent_info->user_type=='1') {				
			$sCommission = getUserCommissionNew(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
		} else {
			$sCommission = getUserCommissionNew(trim($agent_info->mdist_id), $operator_info->operator_id, $amount, 'r');
		}
		print_r($sCommission);
		echo "<br>";
	
	} else {
		echo '{"ResponseCode":"23","Message":"Invalid DMT operation"}';		
		exit();
	}
} else {
	echo '{"ResponseCode":"24","Message":"Agent not found"}';		
	exit();
}