<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

$server = "localhost";
$user = "erecharg_usr";
$pass = "(TO,NoKScrhS";
$base = "erecharg_db";

// $link = mysql_connect($server, $user, $pass) or die('Server connexion not possible.');
// mysql_select_db($base)               or die('Database connexion not possible.');

// echo date("h:i:s a");
  
// $sql=("SELECT trans.*, user.company_name, user.uid ,(select closing_balance from transactions tra where trans.account_id=tra.account_id  and tra.transaction_id<trans.transaction_id ORDER BY tra.transaction_id desc limit 0,1) as opn FROM transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid WHERE trans.transaction_date BETWEEN '2017-10-25 00:00:00' AND '2017-10-25 11:59:59' ORDER BY trans.transaction_id DESC");
// $query=mysql_query($sql);



include("../../config.php");
require(DIR."/system/php-excel.class.php");
ob_start();
$data[] = array('S.No', 'Txn Date', 'User Details', 'Type','Opening Balance', 'Debit Amount', 'Credit Amount', 'Closing Balance','Calculation','Difference', 'Term', 'Ref Txn No', 'Remark', 'User Type', 'Transaction User');

$from = isset($_GET["from"]) && $_GET["from"] != '' ? mysql_real_escape_string($_GET["from"]) : date("Y-m-d");
$to = isset($_GET["to"]) && $_GET["to"] != '' ? mysql_real_escape_string($_GET["to"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE trans.transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
	$sWhere .= " AND trans.account_id = '".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"] != '') {
	$sWhere .= " AND trans.transaction_term = '".mysql_real_escape_string($_GET["type"])."' ";
}

$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid $sWhere ORDER BY trans.transaction_date DESC";
$scnt = 1;
$query = $db->query("SELECT trans.*, user.company_name FROM {$statement}");
// $query = $db->query("SELECT trans.*, user.company_name, user.uid FROM transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid WHERE trans.transaction_date BETWEEN '2017-10-25 00:00:00' AND '2017-10-25 23:59:59' ORDER BY trans.transaction_date DESC");

$prev_account_id=0;
$prev_opening_balance=0;
$mm=0;
while($result = $db->fetchNextObject($query)) {
	
	$transaction_id=$result->transaction_id;
	$account_id=$result->account_id;
	$closing_balance=$result->closing_balance;

	$opening_balance=0;
	// if($prev_account_id!=$account_id)
	// {
		$sql_query="select closing_balance from transactions where account_id=$result->account_id  and transaction_id<$result->transaction_id ORDER BY transaction_id desc limit 1";
		$query_inner = $db->query($sql_query);

		$opnresult = mysql_fetch_object($query_inner);
		//if($opnresult!==null)
			$opening_balance=$opnresult->closing_balance;

		// else
		// 	$opening_balance=0;

		// $prev_account_id=$account_id;
	// }
	// else
	// {
	// 	$opening_balance=$prev_opening_balance;
	// }

	//print_r($opnresult);

	if($result->type == 'dr') {
		$debit_amount = $result->amount;
		$cal=$result->$opening_balance-$result->amount;
		$credit_amount = "";
	} else {
		$credit_amount = $result->amount;
		$cal=$result->$opening_balance+$result->amount;
		$debit_amount = "";
	}
		$diff=round($result->closing_balance,2)-round($cal,2);
	$data[] = array($scnt++, $result->transaction_date, $result->company_name, $result->type,$opening_balance, $debit_amount, $credit_amount, $result->closing_balance,$cal,$diff , $result->transaction_term, $result->transaction_ref_no, $result->remark, $result->transaction_user_type, $result->transaction_by,$opening_balance);
	$prev_opening_balance=$result->closing_balance;
}

// print_r($data);die;
/*echo date("h:i:s a");
die;*/
// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', true, '');
$xls->setWorksheetTitle(date("d-M-Y"));
$xls->addArray($data);
$xls->generateXML('user_transaction_report_'.date("d-M-Y"));
ob_end_flush();

// mysql_close($link);
?>