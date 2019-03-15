<?php
header("Access-Control-Allow-Origin: *");
include("../config.php");
$user_type=$_GET['user_type'];
if($_GET['type'] != '') {
	$number = "";
	$sWhere = "WHERE status = '1' ";
	if($_GET['type'] == "1") {
		$sWhere .= " AND user_type = '1' AND status = '1' ";
	} else if($_GET['type'] == "3") {
		$sWhere .= " AND user_type = '3' AND status = '1' ";
	} else if($_GET['type'] == "4") {
		$sWhere .= " AND user_type = '4' AND status = '1' ";
	} else if($_GET['type'] == "5") {
		$sWhere .= " AND user_type = '5' AND status = '1' ";
	}else if($_GET['type'] == "6") {
		$sWhere .= " AND user_type = '6' AND status = '1' ";
	}
	
	if($user_type == "3" ) {
	    if($_GET['dist_id'] != "" || $_GET['dist_id'] != null) {
		$sWhere .= "AND mdist_id='".$_GET['dist_id']."' ";
	}
	}
	if($user_type == "4" ) {
    	if($_GET['dist_id'] != "" || $_GET['dist_id'] != null) {
		$sWhere .= "AND dist_id='".$_GET['dist_id']."' ";
	}
	}
	echo "<option value=''></option>";
	$query = $db->query("SELECT company_name,mobile,uid FROM apps_user $sWhere ORDER BY company_name ASC");
// 	echo "SELECT company_name,mobile,uid FROM apps_user $sWhere ORDER BY company_name ASC";die;
	if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
	while($result = $db->fetchNextObject($query)) {
		echo "<option value='".$result->mobile."'>".$result->company_name." (".$result->uid.")</option>";
	}
} else {
	echo "<option value=''></option>";
}
?>