<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$requestid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$retrun_url = '';
if(isset($_GET['id']) && $_GET['id']!='' && isset($_GET['action']) && $_GET['action']!= '') {
	$action = isset($_GET['action']) && $_GET['action']!='' ? mysql_real_escape_string($_GET['action']) : '';	
	if($action=='active') {
		$db->execute("UPDATE usercommissions SET status='1' WHERE id='".$requestid."' ");
	} else if($action=='inactive') {
		$db->execute("UPDATE usercommissions SET status='0' WHERE id='".$requestid."' ");
	}
	header("location:".$_SERVER['HTTP_REFERER']);
	exit();
	
} else {
	exit ('No direct accces allowed');
}
?>