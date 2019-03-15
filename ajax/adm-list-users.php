<?php
session_start();
include("../config.php");
if($_POST['type'] != '') {
	$number = "";
	$sWhere = "WHERE status='1' ";
	if($_POST['type']=="1") {
		$sWhere .= " AND user_type='1' ";
	} else if($_POST['type']=="3") {
		$sWhere .= " AND user_type = '3' ";
	} else if($_POST['type']=="4") {
		$sWhere .= " AND user_type = '4' ";
	} else if($_POST['type']=="5") {
		$sWhere .= " AND user_type = '5' ";
	} else if($_POST['type']=="6") {
		$sWhere .= " AND user_type = '6' ";
	}
	echo "<option value=''>---Select---</option>";
	$query = $db->query("SELECT company_name,mobile,uid FROM apps_user $sWhere ORDER BY company_name ASC");
	if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
	while($result = $db->fetchNextObject($query)) {
		echo "<option value='".$result->uid."'>".ucwords($result->company_name)." (".$result->uid.")</option>";
	}
} else {
	echo "<option value=''>---Select---</option>";
}
?>