<?php
include("/home/recharge/public_html/config.php");
ini_set('memory_limit','128M');
set_time_limit(9999999999);


$sWhere = "WHERE type='reminder' AND status = '0'  ";
$sWhere .= " AND reminder_date_to>=CURDATE() ";
 
$query = $db->query("SELECT * FROM personal_notes $sWhere ");

	while($result = $db->fetchNextObject($query)) 
	{
	    $title=$result->title;
	    $description=$result->description;
	    $message="Reminder For ".$title." is ".$description;
	    $mobile="8600250250";
	    smsSendSingle($mobile, $message, 'fund_transfer');
	}


?>