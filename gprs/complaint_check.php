<?php

include("../config.php");
$recharge_id = isset($_GET['recharge_id']) ? mysql_real_escape_string($_GET['recharge_id']) : 0;
$com_info = $db->queryUniqueObject("SELECT complaint_id FROM complaints WHERE txn_no = '".$recharge_id."' ");
		
echo json_encode($com_info);
?>