<?php
session_start();
include("../config.php");
if(isset($_POST["uid"]) && $_POST["uid"]!='') {
	$uid =  htmlentities(addslashes($_POST['uid']),ENT_QUOTES);
	$row = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid='".$uid."' ");
	if($row) {		
		echo round($row->balance,2);
	} else {
		echo "NaN";
	}
} else {
	echo "NaN";
}
?>
