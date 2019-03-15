<?php
session_start();
include('../config.php');
$oprs = [];
$query = $db->query("SELECT * FROM operators WHERE service_type!='10' ORDER BY operator_id ASC ");
while($result = $db->fetchNextObject($query)) {
	$oprs[] = $result;
}
$uid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' ");
if($user) {
	foreach($oprs as $opr) {
		$com = $db->queryUniqueObject("SELECT * FROM apps_commission WHERE uid='".$user->uid."' AND operator_id='".$opr->operator_id."' ");
		if($com) {
			$db->execute("INSERT INTO `usercommissions`(`uid`, `operator_id`, `comm_dist`, `comm_ret`, `comm_api`, `commission_type`, `is_surcharge`, `surcharge_type`, `is_percentage`, `surcharge_value`, `status`) VALUES ('".$user->uid."', '".$opr->operator_id."', '".$com->comm_dist."', '".$com->comm_ret."', '".$com->comm_api."', '".$com->commission_type."', '".$com->is_surcharge."', '".$com->surcharge_type."', '".$com->is_percentage."', '".$com->surcharge_value."', '".$com->status."')");
		} else {
			$db->execute("INSERT INTO `usercommissions`(`uid`, `operator_id`, `comm_dist`, `comm_ret`, `comm_api`, `commission_type`, `is_surcharge`, `surcharge_type`, `is_percentage`, `surcharge_value`, `status`) VALUES ('".$user->uid."', '".$opr->operator_id."', '0', '0', '0', '".$opr->commission_type."', '".$opr->is_surcharge."', 'f', 'n', '".$opr->surcharge_value."', '".$opr->status."')");
		}
	}
}
header("location:api-com.php");
?>