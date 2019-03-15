<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');

if(!empty($sP['is_notification'])) {
	echo "Not Empty";
} 
echo "<br>";