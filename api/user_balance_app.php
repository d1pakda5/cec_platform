<?php
session_start();
header('Access-Control-Allow-Origin: *');  
include("../config.php");
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
    $mobile =  htmlentities(addslashes($_GET['uid']),ENT_QUOTES);
  $uid=$db->queryUniqueValue("Select uid from apps_user where mobile='".$mobile."' ");
   $row = $db->queryUniqueObject("SELECT balance FROM apps_wallet WHERE uid = '".$uid."'");
	if($row) {		
		echo round($row->balance,2);
	} else {
		echo "NaN";
	}
} else {
	echo "NaN";
}
?>
