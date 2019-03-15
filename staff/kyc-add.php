<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
if(empty($sP['kyc']['add'])) { 
	include('permission.php');
	exit(); 
}
$requestid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$action = isset($_GET['action']) && $_GET['action']!='' ? mysql_real_escape_string($_GET['action']) : '';
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$requestid."' ");
if(!$kyc) {
	if($action=='add') {
		$db->execute("INSERT INTO `userskyc`(`uid`, `submitdate`, `submitedby`, `status`) VALUES ('".$requestid."', NOW(), '".$_SESSION['staff']."', '0')");
		$kycid = $db->lastInsertedId();
		header("location:kyc-edit.php?id=".$kycid);	
		exit();
	} else {
		header("location:view-user-profile.php?uid=".$requestid);	
		exit();
	}
}
?>