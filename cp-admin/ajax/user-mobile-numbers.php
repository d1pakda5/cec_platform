<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include("../../config.php");
if($_POST['type'] != '') {
	$number = "";
	$sWhere = "WHERE uid != '' ";
	if($_POST['type'] == "0") {
		$sWhere .= " AND user_type IN (1,3,4,5) ";
	} else if($_POST['type'] == "1") {
		$sWhere .= " AND user_type = '1' ";
	} else if($_POST['type'] == "3") {
		$sWhere .= " AND user_type = '3' ";
	} else if($_POST['type'] == "4") {
		$sWhere .= " AND user_type = '4' ";
	} else if($_POST['type'] == "5") {
		$sWhere .= " AND user_type = '5' ";
	}else if($_POST['type'] == "6") {
		$sWhere .= " AND user_type = '6' ";
	}
	else if($_POST['type'] == "") {
		$sWhere .= " AND user_type = '' ";
	}
	
	if($_POST['status'] == "0") {
		$sWhere .= " AND status = '0' ";
	} else if($_POST['status'] == "9") {
		$sWhere .= " AND status = '9' ";
	} else {
		$sWhere .= " AND status = '1' ";
	}
	
	$query = $db->query("SELECT mobile FROM apps_user $sWhere ");
	if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
	while($result = $db->fetchNextObject($query)) {
		$number .= $result->mobile.",";
	}
	echo rtrim($number, ",");
} else {
	echo "No Mobile Number Found";
}
?>