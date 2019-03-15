<?php
session_start();
header("Access-Control-Allow-Origin: *");
include("../config.php");
if($_REQUEST['type'] != '') {
	$number = "";
	$sWhere = "WHERE status = '1' ";
	if($_REQUEST['type'] == "1") {
		$sWhere .= " AND user_type = '1' AND status = '1' ";
	} else if($_REQUEST['type'] == "3") {
		$sWhere .= " AND user_type = '3' AND status = '1' ";
	} else if($_REQUEST['type'] == "4") {
		$sWhere .= " AND user_type = '4' AND status = '1' ";
	} else if($_REQUEST['type'] == "5") {
		$sWhere .= " AND user_type = '5' AND status = '1' ";
	}else if($_REQUEST['type'] == "6") {
		$sWhere .= " AND user_type = '6' AND status = '1' ";
	}
	echo "<option value=''></option>";
	$query = $db->query("SELECT company_name,mobile,uid FROM apps_user $sWhere ORDER BY company_name ASC");
	if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
	while($result = $db->fetchNextObject($query)) {
		 http_response_code(200);
		echo "<option value='".$result->mobile."'>".$result->company_name." (".$result->uid.")</option>";
	}
} else {
	 http_response_code(200);
	echo "<option value=''></option>";
}
?>