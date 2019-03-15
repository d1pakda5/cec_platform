<?php
session_start();
include("../config.php");
$ip = $_SERVER['REMOTE_ADDR'];


$db->execute("INSERT INTO `mobile_sms`(`sms_date`, `provider`, `msg`) VALUES (NOW(),'".$_GET['provider']."','".$_GET['msg']."')");

die();
?>