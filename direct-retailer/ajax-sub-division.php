<?php
session_start();
include("../config.php");
if($_POST['operator'] != '') {
	echo "<option value=''></option>";
	$query = $db->query("SELECT * FROM sub_divisions WHERE parent_id = '".$_POST['operator']."' ORDER BY sub_division ASC");
	if($db->numRows($query) < 1) echo "<option value=''></option>";
	while($result = $db->fetchNextObject($query)) {
		echo "<option value='".$result->sub_division."'>".$result->sub_division."</option>";
	}
} else {
	echo "<option value=''></option>";
}
?>