<?php

if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
if(isset($_GET['u']) == 'y') {
	$query = $db->query("SELECT * FROM apps_user WHERE status != '9' ");
	while($result = $db->fetchNextObject($query)) {
		$db->query("UPDATE apps_user SET status = '1' WHERE user_id = '".$result->user_id."' AND uid = '".$result->uid."' ");
	}
}