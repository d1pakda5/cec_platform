<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$uid = isset($_GET['uid']) && $_GET['uid'] != '' ? mysql_real_escape_string($_GET['uid']) : 0;
$token = isset($_GET['token']) && $_GET['token'] != '' ? mysql_real_escape_string($_GET['token']) : 0;
$hashToken = hashToken($uid);
if($token == $hashToken) {
	$row = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
	$_SESSION['token'] = $hashToken;
	if($row->user_type == '1') {					
		$_SESSION['apiuser'] = $row->user_id;
		$_SESSION['apiuser_uid'] = $row->uid;
		$_SESSION['apiuser_name'] = $row->fullname;
		header("location:../apiuser/index.php");
	} else if($row->user_type == '3') {					
		$_SESSION['mdistributor'] = $row->user_id;
		$_SESSION['mdistributor_uid'] = $row->uid;
		$_SESSION['mdistributor_name'] = $row->fullname;
		header("location:../master-distributor/index.php");
	} else if($row->user_type == '4') {					
		$_SESSION['distributor'] = $row->user_id;
		$_SESSION['distributor_uid'] = $row->uid;
		$_SESSION['distributor_name'] = $row->fullname;
		header("location:../distributor/index.php");
	} else if($row->user_type == '5') {					
		$_SESSION['retailer'] = $row->user_id;
		$_SESSION['retailer_uid'] = $row->uid;
		$_SESSION['retailer_name'] = $row->fullname;
		header("location:../retailer/index.php");
	}
}
