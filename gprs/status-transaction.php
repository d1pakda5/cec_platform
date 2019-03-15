<?php

include('../config.php');
	header("Access-Control-Allow-Origin: *");
	
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere = " WHERE trans.transaction_ref_no = '".mysql_real_escape_string($_GET["s"])."' ";
} else {
	$sWhere = " WHERE trans.transaction_id = 'a' ";
}

$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid $sWhere ORDER BY trans.transaction_date DESC";
$query = $db->query("SELECT trans.*, user.company_name, user.uid FROM {$statement}");
			if($db->numRows($query) < 1) echo "No Result Found";
			$output=array();
			while($row = $db->fetchNextObject($query)) {
			    
			     $output[]=$row;
			}
			echo json_encode($output);
			
						?>