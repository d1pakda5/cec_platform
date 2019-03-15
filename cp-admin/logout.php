<?php
session_start();
include("../config.php");
if(isset($_SESSION['admin'])) {
	$db->execute("UPDATE activity_login SET logout_time = NOW() WHERE login_id = '".$_SESSION['lastloginid']."' ");
	session_destroy();
	header("location:index.php");
} else {
	header("location:login.php");
}
?>