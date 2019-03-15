<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:login.php");
} else {
	header("location:dashboard.php");
}
?>