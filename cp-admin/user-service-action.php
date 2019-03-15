<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$retrun_url = '';
if(isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['action']) && $_GET['action'] != '') {
	$action = isset($_GET['action']) && $_GET['action'] != '' ? mysql_real_escape_string($_GET['action']) : '';	
	$service = isset($_GET['service']) && $_GET['service'] != '' ? mysql_real_escape_string($_GET['service']) : '';	
	if($service != '') {
		if($action == 'a') {
			$db->execute("UPDATE apps_user SET $service = 'a' WHERE user_id = '".$request_id."' ");
		} else if($action == 'i') {
			$db->execute("UPDATE apps_user SET $service = 'i' WHERE user_id = '".$request_id."' ");
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
	
} else {
	exit ('No direct accces allowed');
}
?>