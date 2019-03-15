<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];

$day = isset($_GET['day']) && $_GET['day']!='' ? mysql_real_escape_string($_GET['day']) : '01';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : '01';
$dtFrom = date("2018-".$month."-01 00:00:00");
$dtTo = date("2018-".$month."-t 23:59:59", strtotime("2017-".$month."-01"));

$query = $db->query("SELECT SUM(amount) AS sum_amount, SUM(surcharge) AS sum_surcharge FROM gst_monthwisejan ");
$result = $db->fetchNextObject($query);

echo $result->sum_amount;
echo "<br>";
echo $result->sum_surcharge;

echo "<br>";
echo "<br>";

$query1 = $db->query("SELECT SUM(rch_amount) AS sum_amount, SUM(IF(bill_type='3', rch_comm_value, 0)) AS sum_surcharge FROM gst_monthly_txns WHERE rch_date='2018-01-01' AND user_type IN (5) ");
$result1 = $db->fetchNextObject($query1);

echo $result1->sum_amount;
echo "<br>";
echo $result1->sum_surcharge;

echo "<br>";
echo "<br>";