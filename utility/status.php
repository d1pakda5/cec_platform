<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$cnCount = $db->countOf("gst_monthwise", "user_type='0'");
echo "Count: ". $cnCount;
//echo "<br><br>";
//$rch_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE request_date > '2017-12-30' ORDER BY request_date ASC ");
//print_r($rch_info);