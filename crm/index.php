<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:../staff/login.php");
} else {
	header("location:dashboard.php");
}
?>