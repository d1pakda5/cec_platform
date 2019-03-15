<?php
session_start();
include("../../config.php");
 if($_POST['admin_id'] != '') {
	echo "<option value=''>--Select Distributor--</option>";
    $query = $db->query("SELECT company_name,mobile,uid FROM apps_user where assign_id='".$_POST['admin_id']."' and status='1' and user_type='4'");
    //echo "SELECT a.fullname,a.admin_id FROM apps_admin a where a.admin_id = (SELECT u.assign_id FROM apps_user u WHERE u.uid ='".$uid."')";
    if($db->numRows($query) < 1) $number .= "No Mobile Number Found";
    while($result = $db->fetchNextObject($query)) {
        echo "<option value='".$result->uid."'>".$result->company_name." (".$result->uid.")</option>";
    }
}

?>
