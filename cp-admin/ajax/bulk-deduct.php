<?php
session_start();
include("../../config.php");
if(isset($_POST['amount']) && $_POST['amount']!='' && isset($_POST['uid']) && !empty($_POST['uid'])) {
	$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);
	if($amount > 0) {
		foreach($_POST['uid'] as $key=>$uid) {	
			$db->query("START TRANSACTION");
			$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance,cuttoff FROM apps_wallet WHERE uid='".$uid."' ");
			if((trim($wallet->balance) - trim($wallet->cuttoff)) >= trim($amount)) {
				$closing_balance = $wallet->balance-$amount;
				$db->query("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
				$ts1 = mysql_affected_rows();
				$txn_remark = "BULK DEDUCT: $amount | $closing_balance";	
				$db->execute("INSERT INTO `transactions_adm`(`txndate`, `txntouid`, `txntype`, `txnamount`, `closingbalance`, `txnterm`, `txnrefno`, `remark`, `txnuser`) VALUES (NOW(), '".$wallet->uid."', 'dr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$txn_remark."', '".$_SESSION['admin']."')");
			}
			if($ts1) {
				$db->query("COMMIT");	
			} else {
				$db->query("ROLLBACK");
			}
				
		}
		echo "Transaction completed successfully";
	} else {
		echo "Failed, amount must be greater than zero";
	}
} else {
	echo "No Data Found";
}
?>