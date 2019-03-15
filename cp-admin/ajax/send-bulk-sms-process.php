<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:../index.php");
	exit();
}

include("../../config.php");
if(isset($_POST["mobiles"]) && $_POST["mobiles"] != '' && isset($_POST["message"]) && $_POST["message"] != '' && isset($_POST["api_id"]) && $_POST["api_id"] != '') {
    $mobile_array=array();
	$mobiles = mysql_real_escape_string($_POST["mobiles"]);	
	$message = $_POST["message"];
	$mobile_array=(explode(",",$mobiles));

	for($i=0;$i<count($mobile_array);$i++)
	{
	    $mobile=$mobile_array[$i];
	    $out = smsSendUrl($mobile, $message, $_POST["api_id"]);
	}
 	
	if($out) {
		echo "SMS submitted successfully";
	} else {
		echo "Error, Please check sms api/balance";
	}
} else {
	echo "Some fields are blank, submit again";
}
?>
