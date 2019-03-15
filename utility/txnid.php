<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
//
$query = $db->query("SELECT * FROM gst_trans ORDER BY id DESC LIMIT 5");
while($result = $db->fetchNextObject($query)) {
	print_r($result);
	echo "<br><br>";
}