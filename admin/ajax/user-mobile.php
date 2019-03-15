<?php
session_start();
include("../../config.php");
if($_POST['uid'] != '') {
	$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".mysql_real_escape_string($_POST['uid'])."' ");
	echo $user->mobile;
} else {
	echo "None";
}
?>