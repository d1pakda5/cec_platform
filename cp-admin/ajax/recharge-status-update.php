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
$status = isset($_POST['status']) && $_POST['status']!='' ? mysql_real_escape_string($_POST['status']) : 0;
$current_status = isset($_POST['current_status']) && $_POST['current_status']!='' ? mysql_real_escape_string($_POST['current_status']) : 0;
$recharge_id = isset($_POST['recharge_id']) && $_POST['recharge_id']!='' ? mysql_real_escape_string($_POST['recharge_id']) : 0;
$user_type = isset($_POST['user_type']) && $_POST['user_type']!='' ? mysql_real_escape_string($_POST['user_type']) : 0;
$user_id= isset($_POST['user_id']) && $_POST['user_id']!='' ? mysql_real_escape_string($_POST['user_id']) : 0;
$uid= isset($_POST['uid']) && $_POST['uid']!='' ? mysql_real_escape_string($_POST['uid']) : 0;
$operator_ref_no = isset($_POST['operator_ref_no']) && $_POST['operator_ref_no']!='' ? mysql_real_escape_string($_POST['operator_ref_no']) : '';
$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id='".$recharge_id."' ");
if($recharge_info) {
	$status_details = "";
	$url_status="";
	if($status == '0') {
	    $url_status="SUCCESS";
		$status_details = "Transaction Successful Amount Debited";
		$db->query("UPDATE apps_recharge SET is_status_changed='1' WHERE recharge_id='".$recharge_id."' ");
		
		$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance,cuttoff FROM apps_wallet WHERE uid='".$recharge_info->uid."' ");
		$transaction = $db->queryUniqueObject("SELECT amount FROM transactions WHERE transaction_ref_no='".$recharge_id."' ANd type='dr' ");
        	$amount=$transaction->amount;
            $closing_balance = $wallet->balance-$amount;
            $recharge_remark = "Set Status to Success";	
            if($current_status=='1'){
                $db->execute("INSERT INTO `recharge_change_log`(`recharge_id`, `remark`, `updated_by`, `updated_date`, `type`) VALUES ('".$recharge_id."','".$recharge_remark."','1',NOW(),'0')");
        // 	$db->execute("INSERT INTO `transactions_adm`(`txndate`, `txntouid`, `txntype`, `txnamount`, `closingbalance`, `txnterm`, `txnrefno`, `remark`, `txnuser`) VALUES (NOW(), '".$wallet->uid."', 'dr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$txn_remark."', '".$_SESSION['admin']."')");
            }
            
	} else if($status == '1') {
	    $url_status="PENDING";
		$status_details = "Transaction Pending Amount Debited";
	} else if($status == '2') {
	    $url_status="FAILED";
		$status_details = "Transaction Failed Amount Reversed";
	} else if($status == '3') {
	    $url_status="REFUNDED";
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '4') {
	    $url_status="REFUNDED";
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '5') {
	    $url_status="REFUNDED";
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '6') {
	    $url_status="REFUNDED";
		$status_details = "Transaction Failed Amount Refunded";
	} else if($status == '7') {
	    $url_status="PROCESSED";
		$status_details = "Transaction Processed Amount Debited";
	} else if($status == '8') {
	    $url_status="SUBMITTED";
		$status_details = "Transaction Submitted Amount Debited";
	} else {
	    $url_status="SUCCESS";
		$status_details = "Transaction Successful!";
	}
	
	if($user_type=='1') {
				$setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id='".$user_id."' ");
				if($setting_info->reverse_url!='') {
				
					$url_txid = $recharge_id;
				    $url_status = $url_status;
				
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
	
	$db->query("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', operator_ref_no='".$operator_ref_no."' WHERE recharge_id='".$recharge_id."' ");
	echo "Recharge has been saved as success!";
	
	exit();
} else {
	echo "ERROR, Invalid recharge ID.";
	exit();
}