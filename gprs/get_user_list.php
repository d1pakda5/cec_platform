<?php
include("../config.php");
	header("Access-Control-Allow-Origin: *");
$uid = isset($_GET['uid']) ? mysql_real_escape_string($_GET['uid']) : "";
$user_type = isset($_GET['type']) ? mysql_real_escape_string($_GET['type']) : "";
if($user_type=='4')
{
$sWhere = "WHERE user.user_type = '5' AND dist_id = '".$uid."' and status='1' ";
}
if($user_type=='3')
{
$sWhere = "WHERE (user.user_type = '5' or user.user_type = '4') AND dist_id = '".$uid."' and status='1' ";
}

$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id DESC";

$query = $db->query("SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement}");
	if($db->numRows($query) < 1) $number .= "No Transaction Found";

		$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
	?>