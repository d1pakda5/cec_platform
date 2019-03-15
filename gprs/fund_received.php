<?php
include("../config.php");
	header("Access-Control-Allow-Origin: *");
$uid = isset($_GET['uid']) ? mysql_real_escape_string($_GET['uid']) : "";
$from = isset($_GET["from_date"]) && $_GET["from_date"] != '' ? mysql_real_escape_string($_GET["from_date"]) : date("Y-m-d");
$to = isset($_GET["to_date"]) && $_GET["to_date"] != '' ? mysql_real_escape_string($_GET["to_date"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$sWhere = "WHERE transaction_status = '1' AND account_id = '".$uid."' AND transaction_date between '".$aFrom."' and '".$aTo."' and type='cr' ";
$statement = "transactions $sWhere ORDER BY transaction_date DESC";
$query = $db->query("SELECT * FROM {$statement} ");

	if($db->numRows($query) < 1) $number .= "No Transaction Found";

		$output=array();

    while($result = $db->fetchNextObject($query)) {
         $output[]=$result;
       
    }
 echo json_encode($output);
	?>