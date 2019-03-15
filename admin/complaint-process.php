<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if($_POST['recharge_id'] == '' || $_POST['action'] == '') {
	if($_POST['recharge_id'] == '') {
		echo "ERROR, Enter a valid recharge txn no";
	} else if($_POST['action'] == '') {
		echo "ERROR, Please select a valid action to resolve complaint";
	} else if($_POST['complaint_id'] == '') {
		echo "ERROR, Enter a valid complaint txn no";
	} else {
		echo "ERROR, Some Parameters are missing, Try again";
	}
	exit();
} else {
	$recharge_id = htmlentities(addslashes($_POST['recharge_id']),ENT_QUOTES);	
	$complaint_id = htmlentities(addslashes($_POST['complaint_id']),ENT_QUOTES);		
	$status = htmlentities(addslashes($_POST['action']),ENT_QUOTES);			
	$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
	$complaint_info = $db->queryUniqueObject("SELECT comp.*, rch.recharge_id, rch.uid, rch.account_no, rch.amount, rch.operator_id, rch.is_refunded FROM complaints comp LEFT JOIN apps_recharge rch ON comp.txn_no = rch.recharge_id WHERE comp.complaint_id = '".$complaint_id."' AND comp.txn_no = '".$recharge_id."' AND rch.recharge_id = '".$recharge_id."' ");
	if($complaint_info) {		
		$operator_info = $db->queryUniqueObject("SELECT operator_name FROM operators WHERE operator_id = '".$complaint_info->operator_id."' ");
		if($status == '1') {			
			if($complaint_info->is_refunded == 'y') {
				echo "ERROR, Complaint is already refunded.";
				exit();
			} else {
				$uid = $complaint_info->uid;
				$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND account_id = '".$uid."' ORDER BY transaction_date DESC");
				if($trans_info) {
					if($trans_info->type == 'dr') {
						$amount = $trans_info->amount;
						$db->query("START TRANSACTION");
						$wallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$uid."' ");
						$closing_balance = $wallet->balance + $amount;					
						$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE uid = '".$uid."' ");
						$ts1 = mysql_affected_rows();
						$remark = "REFUND: $complaint_id, $amount, $remark";
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$uid."', '".$uid."', 'cr', '".$amount."', '".$closing_balance."', 'REFUND', '', '".$remark."', '0', '".$_SESSION['admin']."')");
						$ts_reference_id = $db->lastInsertedId();						
						$db->query("UPDATE complaints SET status = '1', refund_status = '".$status."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), refund_ref_no = '".$ts_reference_id."', remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
						$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
						if($ts1) {
							$commit = $db->query("COMMIT");	
							if($commit) {								
								$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
								$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$closing_balance."' WHERE uid = '".$uid."' AND recharge_id = '".$recharge_id."' ");			
								$mdCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user->mdist_id."'  AND recharge_id = '".$recharge_id."' ");
								if($mdCom && $mdCom->amount > 0) {
									$db->query("START TRANSACTION");
									$mdWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$mdCom->uid."' ");
									$md_closing_balance = $mdWallet->balance - $mdCom->amount;
									$db->query("UPDATE apps_wallet SET balance = '".$md_closing_balance."' WHERE uid = '".$mdCom->uid."' ");									
									$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$md_closing_balance."' WHERE commission_id = '".$mdCom->commission_id."' ");
									$mdCommit = $db->query("COMMIT");	
								}
								
								$dsCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user->dist_id."' AND recharge_id = '".$recharge_id."' ");
								if($dsCom && $dsCom->amount > 0) {
									$db->query("START TRANSACTION");
									$dsWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$dsCom->uid."' ");
									$ds_closing_balance = $dsWallet->balance - $dsCom->amount;
									$db->query("UPDATE apps_wallet SET balance = '".$ds_closing_balance."' WHERE uid = '".$dsCom->uid."' ");
									$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$ds_closing_balance."' WHERE commission_id = '".$dsCom->commission_id."' ");
									$dsCommit = $db->query("COMMIT");
								}
							}
							$user_info = $db->queryUniqueObject("SELECT mobile FROM apps_user WHERE uid = '".$uid."' ");
							
							$message = "Complaint Refund Successful, Txn: ".$recharge_id.", ".$operator_info->operator_name.", ".$complaint_info->account_no.", Rs.".$complaint_info->amount." Ur Bal: ".$close_balance;
							smsSendSingle($user_info->mobile, $message, 'complaint_refund');
							
						} else {
							$db->query("ROLLBACK");
							echo "ERROR,Internal database error. Try again.";
							exit();
						}
						
					} else {
						echo "ERROR,Amount for Recharge has been Already Refunded";
						exit();
					}
				} else {
					echo "ERROR,Transaction Not Found";
					exit();
				}
			}
			
		} else if ($status == '2') {
			$db->query("UPDATE complaints SET status = '1', refund_status = '".$status."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
			$db->query("UPDATE apps_recharge SET status = '0' WHERE recharge_id = '".$recharge_id."' ");
			echo "SUCCESS,Complaint has been closed successfully.";
			exit();
		} else if ($status == '3') {			
			$db->query("UPDATE complaints SET status = '1', refund_status = '".$status."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
			echo "SUCCESS,Complaint has been closed successfully.";
			exit();
		} else {
			echo "ERROR,Complaint not closed, Try again";
			exit();
		}
		
	} else {
		echo "ERROR,Recharge or Complaint Txn is Invalid";
		exit();
	}
}
?>