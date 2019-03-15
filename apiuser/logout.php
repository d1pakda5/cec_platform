<?php
session_start();
include('../config.php');
if(isset($_SESSION['apiuser'])) {
	$db->execute("UPDATE activity_login SET is_online = 'n', logout_time = NOW() WHERE login_id = '".$_SESSION['ap_login_id']."' ");
	unset($_SESSION['apiuser_name']);
	unset($_SESSION['apiuser_uid']);
	unset($_SESSION['apiuser']);
}
header('location:../login.php');
?>
