<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if($_POST['recharge_id'] == '' || $_POST['status'] == '') {
	echo "ERROR,Some Parameters are missing, Try again";
	exit();
} else {
	$recharge_id = htmlentities(addslashes($_POST['recharge_id']),ENT_QUOTES);
	$status = htmlentities(addslashes($_POST['status']),ENT_QUOTES);	
	$operator_ref_no = htmlentities(addslashes($_POST['operator_ref_no']),ENT_QUOTES);		
	$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
	$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id = '".$recharge_id."' ");
	if($recharge_info) {
		if($status == '0') {
			$status_details = "Transaction Successful";
		} else if($status == '1') {
			$status_details = "Transaction Pending";
		} else if($status == '2') {
			$status_details = "Transaction Failed";
		} else if($status == '3') {
			$status_details = "Transaction Refunded";
		} else if($status == '4') {
			$status_details = "Transaction Reverted";
		} else if($status == '5') {
			$status_details = "Transaction Dispute";
		} else if($status == '6') {
			$status_details = "Transaction Cancelled";
		} else if($status == '7') {
			$status_details = "Transaction Processed";
		} else if($status == '8') {
			$status_details = "Transaction Submitted";			
		}
		$new_api_status_details = $recharge_info->api_status_details." : ".$status." by ".$_SESSION['admin_name']." on ".date("d/m/Y H:i:s")." : ".$remark;
		$db->query("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', api_status_details = '".$new_api_status_details."', operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_id."' ");
		echo "SUCCESS,Updated Successfully.";		
		
	} else {
		echo "ERROR,Recharge Txn is Invalid";
		exit();
	}
}
?>