<?php
include("../config.php");
	header("Access-Control-Allow-Origin: *");
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;

if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*,recharge.recharge_id as rid, ar.*, opr.operator_name, api.api_name, user.user_id, user.uid, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid LEFT JOIN apps_recharge_details ar ON ar.recharge_id='".$request_id."' WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		
		echo json_encode($recharge_info);
		
		
	
	} else {
		echo "ERROR,Invalid Recharge ID";
		exit();		
	}
	
} else {
	echo "ERROR,Invalid Transaction ID";
	exit();
}
?>
