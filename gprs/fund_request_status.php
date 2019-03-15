<?php

include('../config.php');
include('../system/class.pagination.php');

$sWhere = "WHERE request_id='".$_GET["request_id"]."'";


$statement = "fund_requests rqst LEFT JOIN apps_admin user ON rqst.updated_by = user.admin_id $sWhere ORDER BY request_date DESC";

$query = $db->query("SELECT * FROM {$statement} ");
if($db->numRows($query) < 1);

$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
 ?>
 