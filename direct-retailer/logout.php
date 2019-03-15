<?php
session_start();
include('../config.php');
if(isset($_SESSION['retailer'])) {
	$db->execute("UPDATE activity_login SET is_online = 'n', logout_time = NOW() WHERE login_id = '".$_SESSION['rt_login_id']."' ");
	unset($_SESSION['retailer_name']);
	unset($_SESSION['retailer_uid']);
	unset($_SESSION['retailer']);
	unset($_SESSION['rt_login_id']);
}
header('location:../login.php');
?>
