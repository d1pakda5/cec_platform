<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../cp-admin/login.php");
} else {
	header("location:../cp-admin/dashboard.php");
}
?>