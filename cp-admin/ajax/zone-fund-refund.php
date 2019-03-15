<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include('../../config.php');
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['txnuid']) && $_POST['txnuid']!='' && isset($_POST['txnamount']) && $_POST['txnamount']!='') {

	$txnuid = htmlentities(addslashes($_POST['txnuid']),ENT_QUOTES);	
	$txnamount = htmlentities(addslashes($_POST['txnamount']),ENT_QUOTES);	
	$txnadmid = htmlentities(addslashes($_POST['txnadmid']),ENT_QUOTES);	
	
	$db->query("START TRANSACTION");
	$wallet = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid='".$txnuid."' ");
	$closing_balance = $wallet->balance+$txnamount;					
	$db->query("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE uid='".$txnuid."' ");
	$ts1 = mysql_affected_rows();	
	if($wallet && $ts1) {
		$commit = $db->query("COMMIT");
		if($commit) {			
			$txn_remark = "REVERSE DEDUCT: $txnamount | $closing_balance";	
			$db->execute("INSERT INTO `transactions_adm`(`txndate`, `txntouid`, `txntype`, `txnamount`, `closingbalance`, `txnterm`, `txnrefno`, `remark`, `txnuser`) VALUES (NOW(), '".$txnuid."', 'cr', '".$txnamount."', '".$closing_balance."', 'FUND', '', '".$txn_remark."', '".$_SESSION['admin']."')");			
			$db->query("UPDATE transactions_adm SET txnstatus='1' WHERE txnadmid='".$txnadmid."' ");
			
			echo "SUCCESS, Fund Reversed successfully";
			exit();
		} else {
			echo "SUCCESS, Internal server error";
			exit();
		}
	} else {
		echo "ERROR, Transaction incomplete, try again";
		exit();
	}
} else {
	echo "ERROR, Invalid transaction id";
	exit();
}
?>