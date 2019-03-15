<?php
session_start();
//echo "No Man's Land";
//exit();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$recharge_id = isset($_POST['recharge_id']) && $_POST['recharge_id']!='' ? mysql_real_escape_string($_POST['recharge_id']) : 0;
$operator_ref_no = isset($_POST['operator_ref_no']) && $_POST['operator_ref_no']!='' ? mysql_real_escape_string($_POST['operator_ref_no']) : '';
$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id='".$recharge_id."' ");
if($recharge_info) {
	$db->query("UPDATE apps_recharge SET status='0', operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$recharge_id."' ");
	echo "Recharge has been saved as success!";
	exit();
} else {
	echo "ERROR, Invalid recharge ID.";
	exit();
}