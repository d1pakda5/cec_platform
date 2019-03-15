<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0 ;
$admin = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet");
if($admin) {
	$current_balance = $admin->balance;
} else {
	$current_balance = "0";
}

$meta['title'] = "Dashboard";
include("header.php");

$permission = $db->queryUniqueObject("SELECT * FROM apps_admin_permission WHERE admin_id = '".$_SESSION['staff']."' ");
if($permission) {
	$sP['api_user'] = unserialize($permission->api_user);
	$sP['md_user'] = unserialize($permission->md_user);
	$sP['ds_user'] = unserialize($permission->ds_user);
	$sP['rt_user'] = unserialize($permission->rt_user);
	$sP['fund'] = unserialize($permission->fund);
	$sP['complaint'] = unserialize($permission->complaint);
	$sP['is_support'] = $permission->is_support;
	$sP['is_notification'] = $permission->is_notification;
	$sP['is_operator'] = $permission->is_operator;
	$sP['is_mobile'] = $permission->is_mobile;
	$sP['sms'] = unserialize($permission->sms);
	$sP['reports'] = unserialize($permission->reports);
} else {
	$sP = array();
}

foreach($sP['reports'] as $key=>$data) {
	echo $key.":".$data."<br>";
}
?>