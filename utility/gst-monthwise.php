<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$day = isset($_GET['day']) && $_GET['day']!='' ? mysql_real_escape_string($_GET['day']) : '01';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$dtFrom = date("2017-".$month."-01 00:00:00");
$dtTo = date("2017-".$month."-t 23:59:59", strtotime("2017-".$month."-01"));
$rchDate =  date("2017-".$month."-01");
$scnt = '72437';
print_r("SELECT SUM(amount) AS sum_amount,SUM(surcharge) AS sum_surcharge,uid,operator_id FROM apps_recharge WHERE status IN (0,1) AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' GROUP BY uid,operator_id ORDER BY recharge_id ASC ");
echo "<br><br>";
$comms = [];
$query = $db->query("SELECT * FROM usercommissions WHERE uid='".$comm_uid."' ");
while($result = $db->fetchNextObject($query)) {
	$comms[] = $result;
}
//
$query = $db->query("SELECT SUM(rch.amount) AS sum_amount,SUM(rch.surcharge) AS sum_surcharge,rch.uid,rch.operator_id,opr.operator_name,opr.item_group,opr.billing_type,opr.hsn_sac_code FROM apps_recharge LEFT JOIN WHERE status IN (0,1) AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' GROUP BY uid,operator_id ORDER BY recharge_id ASC ");
while($result = $db->fetchNextObject($query)) {
	//$rows[] = $result;
	$scnt++;
	echo '"'.$scnt.'","0","'.$result->uid.'","'.$rchDate.'","'.$result->operator_id.'","'.$result->sum_amount.'","'.$result->sum_surcharge.'"<br>';
}