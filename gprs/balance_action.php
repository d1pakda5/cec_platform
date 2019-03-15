<?php
include("../config.php");
	//Change Pin
$request_id = isset($_GET['uid']) ? mysql_real_escape_string($_GET['uid']) : 0;
	$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$request_id."' ");

	echo json_encode($wallet);
	?>