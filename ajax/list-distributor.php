<?php
session_start();
include("../config.php");
if(isset($_POST['id']) && $_POST['id']!='') {
	echo "<option value=''>---Select---</option>";
	$query = $db->query("SELECT company_name,uid FROM apps_user WHERE mdist_id='".$_POST['id']."' AND status='1' AND user_type='4' ORDER BY company_name ASC");
	while($result = $db->fetchNextObject($query)) {
		echo "<option value='".$result->uid."'>".$result->company_name." (".$result->uid.")</option>";
	}
} else {
	echo "<option value=''>---Select---</option>";
}
?>