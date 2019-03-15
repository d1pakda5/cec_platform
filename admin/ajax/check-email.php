<?php
session_start();
include("../../config.php");
if(isset($_POST["email"]) && $_POST["email"] != '') {
	$sWhere = "";
	if(isset($_POST["id"]) && $_POST["id"] != '') {
		$id =  htmlentities(addslashes($_POST['id']),ENT_QUOTES);
		$sWhere = "AND user_id != '".$id."' ";
	}
	$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);
	$row = $db->queryUniqueObject("SELECT email FROM apps_user WHERE email = '".$email."' $sWhere ");
	if($row) {		
		echo "true";
	} else {
		echo "false";
	}
} else {
	echo "true";
}
?>
