<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$countAll = $db->countOfAll("gst_monthwise");
echo $countAll;
echo "<br><br>";
$countAlls = $db->countOf("gst_monthly_txns", "user_type IN (1,5)");
echo $countAlls;
echo "<br><br>";
$qCounts = $db->query("SELECT 
	SUM(CASE WHEN user_type='1' AND bill_type='1' THEN 1 ELSE 0 END) AS p2p, 
	SUM(CASE WHEN user_type='1' AND bill_type='2' THEN 1 ELSE 0 END) AS p2a, 
	SUM(CASE WHEN user_type='1' AND bill_type='3' THEN 1 ELSE 0 END) AS p2s
	FROM gst_monthly_txns ");
$rCounts = $db->fetchNextObject($qCounts);
echo $rCounts->p2p;
echo "<br>";
echo $rCounts->p2a;
echo "<br>";
echo $rCounts->p2s;
echo "<br><br><br>";
$qCounts1 = $db->query("SELECT 
	SUM(CASE WHEN user_type='4' AND bill_type='1' THEN 1 ELSE 0 END) AS p2p, 
	SUM(CASE WHEN user_type='4' AND bill_type='2' THEN 1 ELSE 0 END) AS p2a, 
	SUM(CASE WHEN user_type='4' AND bill_type='3' THEN 1 ELSE 0 END) AS p2s
	FROM gst_monthly_txns ");
$rCounts1 = $db->fetchNextObject($qCounts1);
echo $rCounts1->p2p;
echo "<br>";
echo $rCounts1->p2a;
echo "<br>";
echo $rCounts1->p2s;
echo "<br><br><br>";
$qCounts2 = $db->query("SELECT 
	SUM(CASE WHEN user_type='5' AND bill_type='1' THEN 1 ELSE 0 END) AS p2p, 
	SUM(CASE WHEN user_type='5' AND bill_type='2' THEN 1 ELSE 0 END) AS p2a, 
	SUM(CASE WHEN user_type='5' AND bill_type='3' THEN 1 ELSE 0 END) AS p2s
	FROM gst_monthly_txns ");
$rCounts2 = $db->fetchNextObject($qCounts2);
echo $rCounts2->p2p;
echo "<br>";
echo $rCounts2->p2a;
echo "<br>";
echo $rCounts2->p2s;
echo "<br><br><br>";
$qCounts3 = $db->query("SELECT 
	SUM(CASE WHEN user_type IN (1,5) AND bill_type='1' THEN 1 ELSE 0 END) AS p2p, 
	SUM(CASE WHEN user_type IN (1,5) AND bill_type='2' THEN 1 ELSE 0 END) AS p2a, 
	SUM(CASE WHEN user_type IN (1,5) AND bill_type='3' THEN 1 ELSE 0 END) AS p2s
	FROM gst_monthly_txns ");
$rCounts3 = $db->fetchNextObject($qCounts3);
echo $rCounts3->p2p;
echo "<br>";
echo $rCounts3->p2a;
echo "<br>";
echo $rCounts3->p2s;
echo "<br><br><br>";