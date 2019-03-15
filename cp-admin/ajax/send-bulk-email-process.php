<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}

include("../../config.php");
if(isset($_POST["emails"]) && $_POST["emails"] != '') {
    $mobile_array=array();
	$emails = mysql_real_escape_string($_POST["emails"]);	
	$message = mysql_real_escape_string($_POST["message"]);
	$mobile_array=(explode(",",$emails));

	for($i=0;$i<count($mobile_array);$i++)
	{
	    $email=$mobile_array[$i];
	    sendbulkemail($email, $message);
	}
 	

		echo "Email submitted successfully";

} else {
	echo "Some fields are blank, submit again";
}
?>
