<?php

include('../config.php');
		header("Access-Control-Allow-Origin: *");

$aFrom = date("Y-m-d 00:00:00");
$aTo = date("Y-m-d 23:59:59");
$sWhere = "WHERE comp.complaint_date BETWEEN '".$aFrom."' AND '".$aTo."' AND comp.status = '0' ";

// if(isset($_GET['s']) && $_GET['s'] != '') {
// 	$sWhere .= " AND ( comp.txn_no = '".mysql_real_escape_string($_GET['s'])."' ) ";
// }

$statement = "complaints comp LEFT JOIN apps_recharge rch ON comp.txn_no = rch.recharge_id LEFT JOIN operators opr ON rch.operator_id = opr.operator_id LEFT JOIN api_list api ON rch.api_id = api.api_id LEFT JOIN apps_user user ON user.uid = rch.uid $sWhere ORDER BY comp.complaint_date DESC";

$query = $db->query("SELECT comp.*,user.company_name, rch.recharge_id, rch.api_id, rch.account_no, rch.amount, rch.status as rch_status, rch.operator_ref_no, opr.operator_name FROM {$statement}");

if($db->numRows($query) < 1) echo "No Result Found";
$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
									?>