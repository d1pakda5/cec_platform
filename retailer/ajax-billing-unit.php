<?php
session_start();
include("../config.php");
if($_POST['operator'] != '') {
    header("Access-Control-Allow-Origin: *");
	echo "<option value=''></option>";
	$query = $db->query("SELECT * FROM mh_bu_circle_code ORDER BY bu_circle_name ASC");
	if($db->numRows($query) < 1) echo "<option value=''></option>";
	while($result = $db->fetchNextObject($query)) {
		echo "<option value='".$result->bu_circle_code."'>".$result->bu_circle_name."</option>";
	}
} else {
	echo "<option value=''></option>";
}
?>