<?php
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
if(isset($_GET["userid"]) && isset($_GET["key"]) && isset($_GET["data"])) {
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$data = mysql_real_escape_string($_GET["data"]);
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
	if($user_info) {
		if($user_info->status == '1') {
			$api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid = '".$user_info->uid."' ");
			if(api_info) {
				if($api_info->user_key == $userkey) {
					if($api_info->ip1 == $ip || $api_info->ip2 == $ip || $api_info->ip3 == $ip || $api_info->ip4 == $ip) {
						if($data == 'BU') {
							$query = $db->query("SELECT * FROM mh_bu_circle_code ");
							while($result = $db->fetchNextObject($query)) {
								echo $result->bu_circle_name."<br>";
							}
						} else if($data == 'SBE') {
							$query = $db->query("SELECT * FROM sub_divisions WHERE parent_id = '56' ");
							while($result = $db->fetchNextObject($query)) {
								echo $result->sub_division."<br>";
							}						
						} else if($data == 'NBE') {
							$query = $db->query("SELECT * FROM sub_divisions WHERE parent_id = '55' ");
							while($result = $db->fetchNextObject($query)) {
								echo $result->sub_division."<br>";
							}
						}
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