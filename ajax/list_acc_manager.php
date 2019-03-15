<?php
session_start();
include("../config.php");
 
	echo "<option value=''>--Select Manager--</option>";
    $query = $db->query("SELECT fullname,admin_id FROM apps_admin where user_level='a' and status='1'");
    //echo "SELECT a.fullname,a.admin_id FROM apps_admin a where a.admin_id = (SELECT u.assign_id FROM apps_user u WHERE u.uid ='".$uid."')";
    if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
    while($result = $db->fetchNextObject($query)) {
        echo "<option value='".$result->admin_id."'>".$result->fullname." (".$result->admin_id.")</option>";
    }

?>
