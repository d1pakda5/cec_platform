<?php

include('../config.php');
include('../system/class.pagination.php');

$sWhere = "WHERE (rqst.user_type = '3' or rqst.user_type='6') ";
$sWhere .= " AND rqst.status = '0' ";


$statement = "fund_requests rqst LEFT JOIN apps_user user ON rqst.request_user = user.uid $sWhere ORDER BY request_date DESC";

$query = $db->query("SELECT *, user.company_name FROM {$statement} ");
if($db->numRows($query) < 1);

$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
 ?>
 