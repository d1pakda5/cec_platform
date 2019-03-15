<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$fromDate = "2017-01-01 00:00:59";
$toDate = "2017-06-30 23:59:59";
$day = isset($_GET['day']) && $_GET['day']!='' ? mysql_real_escape_string($_GET['day']) : '01';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$dtFrom = date("2017-".$month."-".$day." 00:00:00");
$dtTo = date("2017-".$month."-".$day." 23:23:59");
$rchDate =  date("2017-".$month."-".$day);
//$rows = [];
$scnt = 1;
$query = $db->query("SELECT SUM(amount) AS sum_amount,SUM(surcharge) AS sum_surcharge,uid,operator_id FROM apps_recharge WHERE status IN (0,1) AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' GROUP BY uid,operator_id ORDER BY recharge_id ASC ");
while($result = $db->fetchNextObject($query)) {
	//$rows[] = $result;
	$db->execute("INSERT INTO `gst_txn_month`(`user_type`, `uid`, `rech_date`, `operator_id`, `amount`, `surcharge`) VALUES ('','".$result->uid."', '".$rchDate."', '".$result->operator_id."', '".$result->sum_amount."', '".$result->sum_surcharge."')");
}