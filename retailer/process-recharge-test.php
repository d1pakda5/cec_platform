<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if(!isset($_SESSION['retailer'])) header("location:index.php");
include("../config.php");
foreach($_POST as $valu=>$data) {
	echo $valu.":: ".$data."<br>";
}


echo "<br><br><br><br><br>";
?>
<a href="recharge-test.php">back</a>