<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$qCounts = $db->query("SELECT SUM(rch_comm_value) AS sur FROM gst_monthly_txns WHERE user_type='5' AND bill_type='3' ");
$rCounts = $db->fetchNextObject($qCounts);
echo $rCounts->sur; 