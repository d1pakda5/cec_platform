<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$day = isset($_GET['day']) && $_GET['day']!='' ? mysql_real_escape_string($_GET['day']) : '01';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$dtFrom = date("2017-".$month."-01 00:00:00");
$dtTo = date("2017-".$month."-t 23:59:59", strtotime("2017-".$month."-01"));
//
$cnSuccess = $db->countOf("apps_recharge", "status='0' AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."'");
echo "Success Count: ". $cnSuccess;
echo "<br>";
$cnPending = $db->countOf("apps_recharge", "status='1' AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."'");
echo "Pending Count: ". $cnPending;
echo "<br>";
echo "<br>";
$query = $db->query("SELECT SUM(amount) AS sum_amount,SUM(surcharge) AS sum_surcharge FROM apps_recharge WHERE status='0' AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ");
$result = $db->fetchNextObject($query);
echo "Amount (Success) Total: ".$result->sum_amount;
echo "<br>";
echo "Surcharge (Success) Total: ".$result->sum_surcharge;
echo "<br>";
echo "<br>";
$query1 = $db->query("SELECT SUM(amount) AS sum_amountp,SUM(surcharge) AS sum_surchargep FROM apps_recharge WHERE status='1' AND request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ");
$result1 = $db->fetchNextObject($query1);
echo "Amount (Pending) Total: ".$result1->sum_amountp;
echo "<br>";
echo "Surcharge (Pending) Total: ".$result1->sum_surchargep;
echo "<br>";
echo "<br>";

$query2 = $db->query("SELECT SUM(amount) AS sum_amount,SUM(surcharge) AS sum_surcharge FROM gst_monthwise WHERE rech_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ");
$result2 = $db->fetchNextObject($query2);
echo "Amount (Success) Total: ".$result2->sum_amount;
echo "<br>";
echo "Surcharge (Success) Total: ".$result2->sum_surcharge;
echo "<br>";
echo "<br>";