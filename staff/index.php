<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:login.php");
} else {
	header("location:dashboard.php");
}
?>