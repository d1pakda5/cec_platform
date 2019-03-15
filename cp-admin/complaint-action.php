<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(!isset($_POST['act']) && $_POST['act'] == '') {
	echo "4";
	exit();	
} else {
	$action = htmlentities(addslashes($_POST['act']),ENT_QUOTES);			
	if($action == '2') {
		if(isset($_POST['items'])) {
			foreach($_POST['items'] as $value) {
				$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE complaint_id = '".$value."' ");
				if($comp_info) {
					$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.recharge_id = '".$comp_info->txn_no."' ");
					if($recharge_info) {
						$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), remark = 'Manully Updated' WHERE complaint_id = '".$comp_info->complaint_id."' ");
						$db->query("UPDATE apps_recharge SET status = '0', is_complaint = 'c' WHERE recharge_id = '".$recharge_info->recharge_id."' ");
						$message = "Recharge already successful, Txn: ".$recharge_info->recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ref. Id: ".$recharge_info->operator_ref_no;
						$user_info = $db->queryUniqueObject("SELECT mobile FROM apps_user WHERE uid = '".$recharge_info->uid."'");
						if($user_info) {
							smsSendSingle($user_info->mobile, $message, 'complaint_refund');
						}
					}
				}
			} 
			echo "1";
			exit();
		} else {
			echo "5";
			exit();
		}	
			
	} else if($action == '4') {
		if(isset($_POST['items'])) {
			foreach($_POST['items'] as $value) {
				$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE complaint_id = '".$value."' ");
				if($comp_info) {
					$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.recharge_id = '".$comp_info->txn_no."' ");
					if($recharge_info) {
						$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), remark = 'Manully Updated' WHERE complaint_id = '".$comp_info->complaint_id."' ");
						$db->query("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_info->recharge_id."' ");
						$message = "Recharge already refunded, Txn: ".$recharge_info->recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount;
						$user_info = $db->queryUniqueObject("SELECT mobile FROM apps_user WHERE uid = '".$recharge_info->uid."'");
						if($user_info) {
							smsSendSingle($user_info->mobile, $message, 'complaint_refund');
						}
					}
				}
			}
			echo "2";
			exit();
		} else {
			echo "5";
			exit();
		}	
		
	} else {
		echo "3";
		exit();
		
	}
}
?>