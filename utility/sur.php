<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$scnt = 1;
//
$rows = [];
$query = $db->query("SELECT * FROM gst_monthwise WHERE surcharge!='0' ORDER BY id ASC");
while($result = $db->fetchNextObject($query)) {
	$rows[] = $result;
}
//
foreach($rows as $row) {
	$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$row->operator_id."' ");
	$amount = $row->amount;
	$surcharge = $row->surcharge;
	$rch_date = $row->rech_date;
	//
	echo $scnt++;
	echo "&nbsp;";
	//
	$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	$sCommission = getRetCommission(trim($agent_info->dist_id),$row->operator_id,$amount,'r');
	echo $operator_info->operator_name;
	echo "&nbsp;";	
	print_r($sCommission);
	echo "<br>";
}