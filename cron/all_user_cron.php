<?php
include("/home/recharge/public_html/config.php");
ini_set('memory_limit','128M');
set_time_limit(9999999999);
include("/home/recharge/public_html/system/php-excel.class.php");
ob_start();
$data[] = array('S.No', 'Type', 'UID', 'Name', 'Mobile', 'Cut Off', 'Balance');
$sWhere = "WHERE user.uid!='0' ";
$sWhere .= " AND user.status='1' ";
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid $sWhere ORDER BY user.user_id DESC";
$scnt = 1;
$file='/home/recharge/public_html/all_user_csv/All_Users_rpt_'.date("d-M-Y").'.csv';
$path='all_user_csv/All_Users_rpt_'.date("d-M-Y").'.csv';
$filename='All_Users_rpt_'.date("d-M-Y"); 
$clb = $db->query("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS walletBalance FROM apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid WHERE user.uid!='0' AND user.user_type IN (1,4,5,6) AND user.status='1' ORDER BY user.user_id DESC");
	$row = $db->fetchNextObject($clb);
$query = $db->execute("SELECT (CASE WHEN user.user_type='5' THEN 'Retailer' WHEN user.user_type='4' THEN 'Distributor' WHEN user.user_type='3' THEN 'Master Distributor'WHEN user.user_type='2' THEN 'Administator' WHEN user.user_type='6' THEN 'Direct Retailor' WHEN user.user_type='1' THEN 'API User' ELSE '' END) AS user ,user.uid,user.company_name,user.mobile, wallet.balance, wallet.cuttoff FROM {$statement} INTO OUTFILE '".$file."' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ");
$date=date("Y-m-d");
$query = $db->execute("INSERT INTO `all_user_rpt`(`rpt_date`, `name`, `path`, `closing_balance`) VALUES ('".$date."','".$filename."','".$path."','".$row->walletBalance."')");


// while($result = $db->fetchNextObject($query)) {
// 	$data[] = array($scnt++, getUserType($result->user_type), $result->uid, $result->company_name, $result->mobile, round($result->cuttoff,2), round($result->balance,2));
// } 
// $qry = $db->query("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS walletBalance FROM {$statement}");
// $row = $db->fetchNextObject($qry);
// $data[] = array("", "", "","", "Total", round($row->cuttoffBalance,2), round($row->walletBalance,2));
// // generate file (constructor parameters are optional)
// $xls = new Excel_XML('UTF-8', true, '');
// $xls->setWorksheetTitle(date("d-M-Y"));
// $xls->addArray($data);
// $xls->generateXMLuser('All_Users_rpt_'.date("d-M-Y"));

// ob_end_flush();
?>