<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$scnt = 1;
//
$rows = [];
$query = $db->query("SELECT * FROM gst_monthlytxns WHERE bill_type='3' GROUP BY operator_id ORDER BY id ASC");
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
	//$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$row->uid."'");
	//if($agent_info->user_type=='5') {
		echo $scnt++;
		echo "   ";
		echo $operator_info->operator_name;
		echo "<br>";
	//}
}