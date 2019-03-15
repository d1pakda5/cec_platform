<?php
include("../config.php");

$sFrom = date("Y-m-d H:i:s", strtotime("-2 DAYS"));
$sTo = date("Y-m-d H:i:s", strtotime("-5 MINUTE"));

$rows = [];		
$query = $db->query("SELECT comp.complaint_id, comp.status, rch.recharge_id, rch.status AS rs_status, rch.request_date, rch.operator_ref_no FROM complaints comp LEFT JOIN apps_recharge rch ON comp.txn_no = rch.recharge_id WHERE comp.status = '0' AND comp.complaint_date BETWEEN '".$sFrom."' AND '".$sTo."' AND rch.status = '0' ORDER BY comp.complaint_date DESC LIMIT 25 ");
while($result = $db->fetchNextObject($query)) {
	$rows[] = $result;
} // end of while loop

foreach($rows as $row) {
	$operator_ref_no = $row->operator_ref_no;
	if($operator_ref_no != '') {
		if(preg_match('/[0-9]/', $operator_ref_no) && strlen($operator_ref_no) > 4 && strlen($operator_ref_no) < 24 ) {
			$db->query("UPDATE complaints SET status = '1', refund_status = '2', refund_by = '0', refund_date = NOW(), remark = 'Complaint closed, recharge successful', is_cron = '1' WHERE complaint_id = '".$row->complaint_id."' ");
			$db->query("UPDATE apps_recharge SET is_refunded = 'y', is_complaint = 'c' WHERE recharge_id = '".$row->recharge_id."' ");
			//echo "Updated: ".$result->recharge_id."<br>";
			//echo $operator_ref_no.", ".$result->rs_status."<br>";
		}	
	}
	//echo "Updated: ".$row->recharge_id.", Operator Ref No: ".$operator_ref_no." <br>";	
}//end of for loop

exit();
?>