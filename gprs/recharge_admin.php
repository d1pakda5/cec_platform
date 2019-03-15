<?php
include('../config.php');



$sWhere = "WHERE recharge.recharge_id!='' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND (recharge.recharge_id='".mysql_real_escape_string($_GET['s'])."' OR recharge.account_no='".mysql_real_escape_string($_GET['s'])."' OR recharge.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
} 



$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid $sWhere ORDER BY recharge.request_date DESC limit 0,1";



$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name FROM {$statement} ");
if($db->numRows($query) < 1) echo "No Result Found";
 $output=array();
      header("Access-Control-Allow-Origin: *");
						while($result = $db->fetchNextObject($query)) {
						    // $output[]=$result;
						    echo json_encode($result);
						}
?>
