<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}
include("../../config.php");
if(isset($_POST["mobile"]) && $_POST["mobile"] != '' && isset($_POST["message"]) && $_POST["message"] != '' && isset($_POST["api"]) && $_POST["api"] != '') {
	$mobile = mysql_real_escape_string($_POST["mobile"]);	
	$message = mysql_real_escape_string($_POST["message"]);	
	$out = smsSendUrl($mobile, $message, $_POST["api"]);
	if($out) {
		echo "SMS submitted successfully";
	} else {
		echo "Error, Please check sms api/balance";
	}
} else {
	echo "Some fields are blank, submit again";
}
?>
