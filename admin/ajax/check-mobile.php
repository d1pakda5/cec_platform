<?php
session_start();
include("../../config.php");
if(isset($_POST["mobile"]) && $_POST["mobile"] != '') {
	$mobile =  htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
	$row = $db->queryUniqueObject("SELECT mobile,username FROM apps_user WHERE mobile = '".$mobile."' OR username = '".$mobile."' ");
	if($row) {		
		echo "true";
	} else {
		echo "false";
	}
} else {
	echo "true";
}
?>
