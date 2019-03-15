<?php
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
if(isset($_GET["userid"]) && isset($_GET["key"])) {
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
	if($user_info) {
		if($user_info->status == '1') {
			$api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid = '".$user_info->uid."' ");
			if(api_info) {
				if($api_info->user_key == $userkey) {
					if($api_info->ip1 == $ip || $api_info->ip2 == $ip || $api_info->ip3 == $ip || $api_info->ip4 == $ip) {
						$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id = '".$user_info->user_id."' ");
						echo $wallet->balance;
					} else {
						echo "ERROR,Invalid IP";
					}
				} else {
					echo "ERROR,Invalid KEY";
				}
			} else {
				echo "ERROR,Inactive User";
			}
		} else {
			echo "ERROR,Inactive User";
		}
	} else {
		echo "ERROR,Invalid User ID";
	}
} else {
	echo "ERROR,Parameter Is Missing";
}
?>
