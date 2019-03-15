<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']):0;

if((!isset($_POST['recharge_id']) && $_POST['recharge_id']=='') || (!isset($_POST['action']) && $_POST['action']=='')) {
	if($_POST['recharge_id']=='') {
		echo "ERROR, Enter a valid recharge txn no";
		exit();
	} else if($_POST['action']=='') {
		echo "ERROR, Please select a valid action to process";
		exit();
	} else {
		echo "ERROR, Some Parameters are missing, Try again";
		exit();
	}
	exit();
	
} else {

	$recharge_id = htmlentities(addslashes($_POST['recharge_id']),ENT_QUOTES);	
	$post_complaint_id = htmlentities(addslashes($_POST['complaint_id']),ENT_QUOTES);	
	$action = htmlentities(addslashes($_POST['action']),ENT_QUOTES);			
	$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
	$operator_ref_no = htmlentities(addslashes($_POST['operator_ref_no']),ENT_QUOTES);
	$is_complaint = false;
	$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_id, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id WHERE rch.recharge_id='".$recharge_id."' ");
	if($recharge_info) {
		$uid = $recharge_info->uid;
		$complaint_id='';
		$user_info=$db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' ");	
		if($post_complaint_id!='') {
			$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE complaint_id='".$post_complaint_id."' AND txn_no='".$recharge_id."' ");
			if($comp_info) {
				$is_complaint = true;
				$complaint_id = $comp_info->complaint_id;
			}
		} else {
			$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no='".$recharge_id."' ");
			if($comp_info) {
				$is_complaint = true;
				$complaint_id = $comp_info->complaint_id;
			}
		}
		
		if($action=='1') {			
			$trans_info=$db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND account_id='".$uid."' ORDER BY transaction_date DESC");
			if($trans_info) {
				
				if($trans_info->type=='dr') {
				
					$amount = $trans_info->amount;
					
					$db->query("START TRANSACTION");
					$wallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid='".$uid."' ");
					$closing_balance = $wallet->balance+$amount;					
					$db->query("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE uid='".$uid."' ");
					$ts1 = mysql_affected_rows();
					
					$remark_msg = "REFUND: $recharge_id, $recharge_info->operator_name, $amount, $complaint_id, $remark";
					$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$uid."', '".$uid."', 'cr', '".$amount."', '".$closing_balance."', 'REFUND', '".$recharge_id."', '".$remark_msg."', '0', '".$_SESSION['admin']."')");
					$ts_reference_id = $db->lastInsertedId();
					
					if($is_complaint == true) {						
						$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), refund_ref_no = '".$ts_reference_id."', remark = '".$remark_msg."' WHERE complaint_id = '".$complaint_id."' ");
						$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y', is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
					} else {
						$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
					}
					
					if($ts1) {
						$commit = $db->query("COMMIT");	
						if($commit) {	
							
							$db->query("UPDATE commission_details SET amount='0', closing_balance='".$closing_balance."' WHERE uid='".$uid."' AND recharge_id='".$recharge_id."' ");			
							
							$mdCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user_info->mdist_id."'  AND recharge_id='".$recharge_id."' ");
							if($mdCom && $mdCom->amount > 0) {
								$db->query("START TRANSACTION");
								$mdWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$mdCom->uid."' ");
								$md_closing_balance = $mdWallet->balance - $mdCom->amount;
								$db->query("UPDATE apps_wallet SET balance = '".$md_closing_balance."' WHERE uid = '".$mdCom->uid."' ");									
								$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$md_closing_balance."' WHERE commission_id = '".$mdCom->commission_id."' ");
								$mdCommit = $db->query("COMMIT");	
							}
							
							$dsCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user_info->dist_id."' AND recharge_id = '".$recharge_id."' ");
							if($dsCom && $dsCom->amount > 0) {
								$db->query("START TRANSACTION");
								$dsWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$dsCom->uid."' ");
								$ds_closing_balance = $dsWallet->balance - $dsCom->amount;
								$db->query("UPDATE apps_wallet SET balance = '".$ds_closing_balance."' WHERE uid = '".$dsCom->uid."' ");
								$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$ds_closing_balance."' WHERE commission_id = '".$dsCom->commission_id."' ");
								$dsCommit = $db->query("COMMIT");
							}
						}
						
						$message = "Complaint Refund Successful, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ur Bal: ".$closing_balance;
						smsSendSingle($user_info->mobile, $message, 'complaint_refund');
						
						if($is_complaint == true) {
							echo "SUCCESS, Amount Refunded and Complaint has been closed successfully.";
						} else {
							echo "SUCCESS, Amount has been Refunded Succesfully.";
						}
						exit();
						
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
			//End of Refund Amount
			
		} else if ($action=='2') {
			
			if($is_complaint==true) {
				$db->query("UPDATE complaints SET status='1', refund_status='".$action."', refund_by='".$_SESSION['admin']."', refund_date=NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET status='0', is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
				$message = "Recharge already successful, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ref. Id: ".$operator_ref_no;
				smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			} else {
				$db->query("UPDATE apps_recharge SET status='0' WHERE recharge_id='".$recharge_id."' ");
			}
			
			if($is_complaint == true) {
				echo "SUCCESS, Status updated and Complaint has been closed successfully.";
			} else {
				echo "SUCCESS, Status updated successfully.";
			}
			exit();
			
		} elseif($action=='3') {		
			
			if($is_complaint==true) {
				$db->query("UPDATE complaints SET status='1', refund_status='".$action."', refund_by='".$_SESSION['admin']."', refund_date = NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
			}			
			$message = "Invalid Transaction, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." ".$remark;
			smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			echo "SUCCESS, Complaint has been closed successfully.";
			exit();
			
		} else if ($action=='4') {
		
			if($is_complaint==true) {
				$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['admin']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
			}			
			$message = "Recharge already refunded, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." ".$remark;
			smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			echo "SUCCESS, Complaint has been closed successfully.";
			exit();
			
		} else if ($action == '5') {
			//Refund amount without refund amount
			if($is_complaint == true) {
				$db->query("UPDATE complaints SET status='1', refund_status='1', refund_by='".$_SESSION['admin']."', refund_date=NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET status='3', is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
			} else {
				$db->query("UPDATE apps_recharge SET status='3' WHERE recharge_id='".$recharge_id."' ");
			}
			
			$new_remark = "Set Status to refunded";
			$db->execute("INSERT INTO `recharge_change_log`(`id`, `recharge_id`, `remark`, `updated_by`, `updated_date`) VALUES ('', '".$recharge_id."', '".$new_remark."', '".$_SESSION['admin']."', NOW())");
			
			if($is_complaint == true) {
				echo "SUCCESS, Status updated and Complaint has been closed successfully.";
			} else {
				echo "SUCCESS, Status updated successfully.";
			}
			exit();
		} else if ($action == '6') {
			//Set Recharge to succcess without user sms
			if($is_complaint == true) {
				$db->query("UPDATE complaints SET status='1', refund_status='2', refund_by='".$_SESSION['admin']."', refund_date=NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET status='0', is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
			} else {
				$db->query("UPDATE apps_recharge SET status='0' WHERE recharge_id='".$recharge_id."' ");
			}
			
			$new_remark = "Set Status to Success";
			$db->execute("INSERT INTO `recharge_change_log`(`id`, `recharge_id`, `remark`, `updated_by`, `updated_date`) VALUES ('', '".$recharge_id."', '".$new_remark."', '".$_SESSION['admin']."', NOW())");
			
			if($is_complaint==true) {
				echo "SUCCESS, Status updated and Complaint has been closed successfully.";
			} else {
				echo "SUCCESS, Status updated successfully.";
			}
			exit();
			
		} else if($action=='7') {
			//Revert Amount for success recharge
			if($recharge_info->is_refunded == 'y') {
				$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND account_id = '".$uid."' ORDER BY transaction_date DESC");
				if($trans_info) {
					if($trans_info->type == 'cr') {					
						$amount = $trans_info->amount;
						$db->query("START TRANSACTION");
						$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$uid."' ");
						if($wallet->balance > $amount) {
							$closing_balance = $wallet->balance - $amount;
							$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE uid = '".$uid."' ");
							$ts1 = mysql_affected_rows();
							
							$remark_msg = "REVERT: $recharge_id, $recharge_info->operator_name, $amount, $complaint_id, $remark";
							$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$uid."', '".$uid."', 'cr', '".$amount."', '".$closing_balance."', 'REVERT', '".$recharge_id."', '".$remark_msg."', '0', '".$_SESSION['admin']."')");
							$ts_reference_id = $db->lastInsertedId();
							
							$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
							
							if($ts1) {
								$commit = $db->query("COMMIT");	
								if($commit) {	
									
									if($user_info->user_type == '1') {				
										$sCommission = getUserCommission(trim($user_info->uid), $recharge_info->operator_id, $recharge_info->amount);
									} else {
										$sCommission = getUserCommission(trim($user_info->mdist_id), $recharge_info->operator_id, $recharge_info->amount);
									}
									
									if($sCommission['rtCom'] > '0') {
										$rtCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user_info->uid."'  AND recharge_id = '".$recharge_id."' ");
										if($rtCom) {
											$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$closing_balance."' WHERE uid = '".$uid."' AND recharge_id = '".$recharge_id."' ");	
										} else {
											$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$user_info->uid."', '".$sCommission['rtCom']."', '".$closing_balance."', NOW())");
										}
									}//End of Retailer Commsssion
									
									if($user_info->user_type != '1') {				
										if($sCommission['mdCom'] > '0') {
											$db->query("START TRANSACTION");
											$mdWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$mdCom->uid."' ");
											$md_closing_balance = $mdWallet->balance + $sCommission['rtCom'];
											$db->query("UPDATE apps_wallet SET balance = '".$md_closing_balance."' WHERE uid = '".$mdCom->uid."' ");
											$mdCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user_info->mdist_id."'  AND recharge_id = '".$recharge_id."' ");
											if($mdCom) {																					
												$db->query("UPDATE commission_details SET amount = '".$sCommission['rtCom']."', closing_balance = '".$md_closing_balance."' WHERE commission_id = '".$mdCom->commission_id."' ");
											} else {
												$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$mdCom->uid."', '".$sCommission['mdCom']."', '".$md_closing_balance."', NOW())");
											}
											$mdCommit = $db->query("COMMIT");	
										}//End of Master Distributor comission
										
										if($sCommission['dsCom'] > '0') {	
											$db->query("START TRANSACTION");								
											$dsCom = $db->queryUniqueObject("SELECT * FROM commission_details WHERE uid = '".$user_info->dist_id."' AND recharge_id = '".$recharge_id."' ");
											$dsWallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$dsCom->uid."' ");
											$ds_closing_balance = $dsWallet->balance + $sCommission['dsCom'];
											$db->query("UPDATE apps_wallet SET balance = '".$ds_closing_balance."' WHERE uid = '".$dsCom->uid."' ");
											if($dsCom) {
												$db->query("UPDATE commission_details SET amount = '".$sCommission['dsCom']."', closing_balance = '".$ds_closing_balance."' WHERE commission_id = '".$dsCom->commission_id."' ");
											} else {
												$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$dsCom->uid."', '".$sCommission['dsCom']."', '".$ds_closing_balance."', NOW())");
											}
											$dsCommit = $db->query("COMMIT");
										}//End of Distributor Comission
									}//End of comiision user type
								}
								
								$message = "Complaint Revert Successful, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ur Bal: ".$closing_balance;
								//smsSendSingle($user_info->mobile, $message, 'complaint_refund');								
								echo "SUCCESS, Amount has been Reverted Succesfully.";
								exit();
								
							} else {							
								$db->query("ROLLBACK");
								echo "ERROR,Internal database error. Try again.";
								exit();								
							}
						}
						
					} else {
						echo "ERROR,Amount for Recharge has been not Refunded";
						exit();
					}
				} else {
					echo "ERROR, Transaction Not Found";
					exit();
				}
			} else {
				//End of Refund Amount
				echo "ERROR, Complaint is not refunded.";
				exit();
			}
			
		} else if ($action == '8') {
			//Revert Amount for refunded recharge without user notification
			if($recharge_info->is_refunded=='y') {
				$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND account_id = '".$uid."' ORDER BY transaction_date DESC");
				if($trans_info) {
					if($trans_info->type == 'cr') {					
						$amount = $trans_info->amount;
						$db->query("START TRANSACTION");
						$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$uid."' ");
						if($wallet->balance > $amount) {
							$closing_balance = $wallet->balance - $amount;
							$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE uid = '".$uid."' ");
							$ts1 = mysql_affected_rows();
							
							$remark_msg = "REVERT: $recharge_id, $recharge_info->operator_name, $amount, $complaint_id, $remark";
							$db->execute("INSERT INTO `trans_deduct`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$uid."', '0', 'dr', '".$amount."', '".$closing_balance."', 'REVERT', '".$recharge_id."', '".$remark_msg."', '0', '".$_SESSION['admin']."')");
							$ts_reference_id = $db->lastInsertedId();
							
							if($ts1) {
								$commit = $db->query("COMMIT");											
								echo "SUCCESS, Amount has been Reverted Succesfully.";
								exit();
								
							} else {							
								$db->query("ROLLBACK");
								echo "ERROR,Internal database error. Try again.";
								exit();								
							}
						}
						
					} else {
						echo "ERROR,Amount for Recharge has been not Refunded";
						exit();
					}
				} else {
					echo "ERROR, Transaction Not Found";
					exit();
				}
				
			} else {
				//End of Refund Amount
				echo "ERROR, Complaint is not refunded.";
				exit();
			}
			
		} else {
			echo "Error, No Action";
			exit();
		}
	
	} else {	
		echo "ERROR, Recharge or Complaint Txn is Invalid";
		exit();
	}	
}
?>