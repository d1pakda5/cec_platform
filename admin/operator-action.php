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
	if($action == 'activate') {
		$db->execute("UPDATE operators SET status = '1' WHERE operator_id = '".$request_id."' ");
	} else if($action == 'suspend') {
		$db->execute("UPDATE operators SET status = '0' WHERE operator_id = '".$request_id."' ");
	}	
	header("location:".$_SERVER['HTTP_REFERER']);
	
} else {
	exit ('No direct accces allowed');
}
?>