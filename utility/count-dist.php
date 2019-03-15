<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$countAll = $db->countOfAll("gst_monthly_txns");
echo $countAll;
echo "<br><br>";
$countDist = $db->countOf("gst_monthly_txns", "user_type='4'");
echo $countDist;
echo "<br>";
$countDists = $db->countOf("gst_monthly_txns", "user_type='4' AND is_update='0'");
echo $countDists;
echo "<br>";
$countDistt = $db->countOf("gst_monthly_txns", "user_type='4' AND is_update='1'");
echo $countDistt;
echo "<br>";
