<?php
include("../config.php");
$query = $db->execute("INSERT INTO get_user_uid(id) values ('')");
$uid = $db->lastInsertedId();
echo $uid;
	?>