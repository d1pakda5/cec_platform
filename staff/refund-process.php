<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['complaint']['refund'])) { 
	echo "You do not have permission to access, please refer to your system administrator.";
	exit(); 
}
if(empty($sP['complaint']['limit'])) { 
	echo "You do not have permission to access, please refer to your system administrator.";
	exit(); 
}

$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if((!isset($_POST['recharge_id']) && $_POST['recharge_id'] == '') || (!isset($_POST['action']) && $_POST['action'] == '')) {
	if($_POST['recharge_id'] == '') {
		echo "ERROR, Enter a valid recharge txn no";
	} else if($_POST['action'] == '') {
		echo "ERROR, Please select a valid action to process";
	} else {
		echo "ERROR, Some Parameters are missing, Try again";
	}
	exit();
	
} else {

	$recharge_id = htmlentities(addslashes($_POST['recharge_id']),ENT_QUOTES);	
	$post_complaint_id = htmlentities(addslashes($_POST['complaint_id']),ENT_QUOTES);	
	$action = htmlentities(addslashes($_POST['action']),ENT_QUOTES);			
	$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
	$operator_ref_no = htmlentities(addslashes($_POST['operator_ref_no']),ENT_QUOTES);
		$user_type = isset($_POST['user_type']) && $_POST['user_type']!='' ? mysql_real_escape_string($_POST['user_type']) : 0;
    $user_id= isset($_POST['user_id']) && $_POST['user_id']!='' ? mysql_real_escape_string($_POST['user_id']) : 0;
    $uid= isset($_POST['uid']) && $_POST['uid']!='' ? mysql_real_escape_string($_POST['uid']) : 0;
	$is_complaint = false;
	$recharge_info = $db->queryUniqueObject("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.recharge_id = '".$recharge_id."' ");
		$setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id='".$user_id."' ");
	if($recharge_info) {
		
		if($sP['complaint']['limit'] < $recharge_info->amount) { 
			echo "You do not have enough limit, please refer to your system administrator.";
			exit(); 
		}
		
		$uid = $recharge_info->uid;
		$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");	
		if($post_complaint_id != '') {
			$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE complaint_id = '".$post_complaint_id."' AND txn_no = '".$recharge_id."' ");
			if($comp_info) {
				$is_complaint = true;
				$complaint_id = $comp_info->complaint_id;
			}
		} else {
			$comp_info = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."' ");
			if($comp_info) {
				$is_complaint = true;
				$complaint_id = $comp_info->complaint_id;
			}
		}
		
		if($action == '1') {			
			
			if($recharge_info->is_refunded == 'y') {
			
				echo "ERROR, Complaint is already refunded.";
				exit();
				
			} else {
			
				$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' AND account_id = '".$uid."' ORDER BY transaction_date DESC");
				if($trans_info) {
					if($trans_info->type == 'dr') {
					
						$amount = $trans_info->amount;
						
						$db->query("START TRANSACTION");
						$wallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$uid."' ");
						$closing_balance = $wallet->balance + $amount;					
						$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE uid = '".$uid."' ");
						$ts1 = mysql_affected_rows();
						
						$remark_msg = "REFUND: $recharge_id, $recharge_info->operator_name, $amount, $complaint_id, $remark";
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$uid."', '".$uid."', 'cr', '".$amount."', '".$closing_balance."', 'REFUND', '".$recharge_id."', '".$remark_msg."', '0', '".$_SESSION['staff']."')");
						$ts_reference_id = $db->lastInsertedId();
						
						if($is_complaint == true) {						
							$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['staff']."', refund_date = NOW(), refund_ref_no = '".$ts_reference_id."', remark = '".$remark_msg."' WHERE complaint_id = '".$complaint_id."' ");
							$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y', is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
						} else {
							$db->query("UPDATE apps_recharge SET status = '3', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
						}
						
						if($ts1) {
							$commit = $db->query("COMMIT");	
							if($commit) {	
								
								$db->query("UPDATE commission_details SET amount = '0', closing_balance = '".$closing_balance."' WHERE uid = '".$uid."' AND recharge_id = '".$recharge_id."' ");			
								
								//								
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
							if($user_type=='1') {
                			
                				if($setting_info->reverse_url!='') {
                				
                					$url_txid = $recharge_id;
                				    $url_status = "REFUNDED";
                				    $status_details="Amount has been Refunded Succesfully";
                					$explodUrl = explode('?',$setting_info->reverse_url);				
                					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$recharge_info->reference_txn_no));
                					
                					$ch = curl_init();
                					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
                					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                					curl_exec ($ch);
                					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                					curl_close($ch);				
                					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$recharge_info->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                					
                				}
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
			}
			//End of Refund Amount
		} else if ($action == '2') {
			
			if($is_complaint == true) {
				$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['staff']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET status = '0', is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
				$message = "Recharge already successful, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." Ref. Id: ".$operator_ref_no;
				smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			} else {
				$db->query("UPDATE apps_recharge SET status = '0' WHERE recharge_id = '".$recharge_id."' ");
			}
			
			if($is_complaint == true) {
				echo "SUCCESS, Status updated and Complaint has been closed successfully.";
			} else {
				echo "SUCCESS, Status updated successfully.";
			}
			if($user_type=='1') {
                			
                				if($setting_info->reverse_url!='') {
                				
                					$url_txid = $recharge_id;
                				    $url_status = "SUCCESS";
                				    $status_details="Recharge already successfull";
                					$explodUrl = explode('?',$setting_info->reverse_url);				
                					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$recharge_info->reference_txn_no));
                					
                					$ch = curl_init();
                					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
                					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                					curl_exec ($ch);
                					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                					curl_close($ch);				
                					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$recharge_info->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                					
                				}
                			}
			exit();
			
		} else if ($action == '3') {		
			
			if($is_complaint == true) {
				$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['staff']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
			}			
			$message = "Invalid Transaction, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." ".$remark;
			smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			echo "SUCCESS, Complaint has been closed successfully.";
			 if($user_type=='1') {
                			
                				if($setting_info->reverse_url!='') {
                				
                					$url_txid = $recharge_id;
                				    $url_status = "INVALID TRANSACTION";
                				    $status_details="INVALID TRANSACTION";
                					$explodUrl = explode('?',$setting_info->reverse_url);				
                					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$recharge_info->reference_txn_no));
                					
                					$ch = curl_init();
                					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
                					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                					curl_exec ($ch);
                					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                					curl_close($ch);				
                					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$recharge_info->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                					
                				}
                			}
			exit();
			
		} else if ($action == '4') {
		
			if($is_complaint == true) {
				$db->query("UPDATE complaints SET status = '1', refund_status = '".$action."', refund_by = '".$_SESSION['staff']."', refund_date = NOW(), remark = '".$remark."' WHERE complaint_id = '".$complaint_id."' ");
				$db->query("UPDATE apps_recharge SET is_complaint = 'c' WHERE recharge_id = '".$recharge_id."' ");
			}			
			$message = "Recharge already refunded, Txn: ".$recharge_id.", ".$recharge_info->operator_name.", ".$recharge_info->account_no.", Rs.".$recharge_info->amount." ".$remark;
			smsSendSingle($user_info->mobile, $message, 'complaint_refund');
			echo "SUCCESS, Complaint has been closed successfully.";
				if($user_type=='1') {
                				
                				if($setting_info->reverse_url!='') {
                				
                					$url_txid = $recharge_id;
                				    $url_status = "REFUNDED";
                				    $status_details="Amount has been Refunded Succesfully";
                					$explodUrl = explode('?',$setting_info->reverse_url);				
                					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$recharge_info->reference_txn_no));
                					
                					$ch = curl_init();
                					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
                					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                					curl_exec ($ch);
                					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                					curl_close($ch);				
                					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$recharge_info->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                					
                				}
                			}
			exit();
			
		} else if ($action == '5') {
			echo "Error, Try Later";
			exit();
		} else if ($action == '6') {
			echo "Error, Try Later";
			exit();
		} else if ($action == '7') {
			echo "Error, Try Later";
			exit();
		} else if ($action == '8') {
			echo "Error, Try Later";
			exit();
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