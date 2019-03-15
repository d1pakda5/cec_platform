<?php
session_start();
include("../config.php");
include("common.php");
if(!isset($_SESSION['mdistributor'])) header("location:index.php");
if(!isset($_GET['token']) || $_GET['token'] != $token) { exit("Token not match"); }
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$retrun_url = '';
if(isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['status']) && $_GET['status'] != '') {
	$action = isset($_GET['status']) && $_GET['status'] != '' ? mysql_real_escape_string($_GET['status']) : '';	
	if($action == '1') {
		$db->execute("UPDATE apps_commission SET status = '1' WHERE id = '".$request_id."' ");
	} else if($action == '0') {
		$db->execute("UPDATE apps_commission SET status = '0' WHERE id = '".$request_id."' ");
	}	
	header("location:".$_SERVER['HTTP_REFERER']);
	
} else {
	exit ('No direct accces allowed');
}
?>