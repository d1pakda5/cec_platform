<?php
session_start();
header('Access-Control-Allow-Origin: *');  
include("../config.php");
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
    $uid =  htmlentities(addslashes($_GET['uid']),ENT_QUOTES);

    $query = $db->query("SELECT a.fullname,a.admin_id FROM apps_admin a where a.admin_id = (SELECT u.assign_id FROM apps_user u WHERE u.mobile ='".$uid."')");
    //echo "SELECT a.fullname,a.admin_id FROM apps_admin a where a.admin_id = (SELECT u.assign_id FROM apps_user u WHERE u.uid ='".$uid."')";
    if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
    while($result = $db->fetchNextObject($query)) {
        echo "<option value='".$result->admin_id."'>".$result->fullname." (".$result->admin_id.")</option>";
    }
} else {
   	echo "<option value=''></option>";
   
}
?>
