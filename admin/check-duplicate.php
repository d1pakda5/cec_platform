<?php
include('../config.php');
if(isset($_GET['u']) == 'y') {
	$query = $db->query("SELECT mobile, COUNT(*) c FROM apps_user GROUP BY mobile HAVING c > 1 ");
	while($result = $db->fetchNextObject($query)) {
		echo $result->mobile." - ".$result->c;
		echo "<br>=============================================<br>";
	}
}