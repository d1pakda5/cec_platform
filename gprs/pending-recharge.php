<?php

include('../config.php');
		header("Access-Control-Allow-Origin: *");

$from = isset($_GET["from_date"]) && $_GET["from_date"]!='' ? mysql_real_escape_string($_GET["from_date"]) : date("Y-m-d");
$to = isset($_GET["to_date"]) && $_GET["to_date"]!='' ? mysql_real_escape_string($_GET["to_date"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";

$sWhere .= " And recharge.status='1' ";
$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.org_operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid $sWhere ORDER BY recharge.request_date ASC";

$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name FROM {$statement}");

if($db->numRows($query) < 1) echo "No Result Found";
$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
									?>