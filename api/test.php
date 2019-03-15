<?php
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
if(isset($_GET["userid"]) && isset($_GET["key"]) && isset($_GET["number"]) && isset($_GET["operator"]) && isset($_GET["amount"]) && isset($_GET["usertxn"])) {
	$mode = "API";
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$account = mysql_real_escape_string($_GET["number"]);
	$operator_code = mysql_real_escape_string($_GET["operator"]);
	$amount = mysql_real_escape_string($_GET["amount"]);
	$reference_txn_no = isset($_GET['usertxn']) && $_GET['usertxn']!='' ? mysql_real_escape_string($_GET['usertxn']) : '';
	$current_time = date('Y-m-d H:i:s', strtotime('-1 HOURS', time()));
	$customer_account = isset($_GET['account']) && $_GET['account']!='' ? mysql_real_escape_string($_GET['account']) : '';
	$dob = isset($_GET['dob']) && $_GET['dob']!='' ? mysql_real_escape_string($_GET['dob']) : '';
	$sub_division = isset($_GET['subdivision']) && $_GET['subdivision']!='' ? mysql_real_escape_string($_GET['subdivision']) : '';
	$billing_cycle = '';
	$billing_unit = isset($_GET['bu']) && $_GET['bu']!='' ? mysql_real_escape_string($_GET['bu']) : '';
	$pc_number = isset($_GET['pcnumber']) && $_GET['pcnumber']!='' ? mysql_real_escape_string($_GET['pcnumber']) : '';
	$customer_name = isset($_GET['cname']) && $_GET['cname']!='' ? mysql_real_escape_string($_GET['cname']) : '';
	$customer_mobile = isset($_GET['cmobile']) && $_GET['cmobile']!='' ? mysql_real_escape_string($_GET['cmobile']) : '';
	$customer_email = isset($_GET['cemail']) && $_GET['cemail']!='' ? mysql_real_escape_string($_GET['cemail']) : '';
	$customer_city = isset($_GET['ccity']) && $_GET['ccity']!='' ? mysql_real_escape_string($_GET['ccity']) : '';
	
	if(strlen($reference_txn_no) < 6 || strlen($reference_txn_no) > 22) {
		// "ERROR,Transaction number length between 6-20 Character";
		$result_code = '329';
		
	} else {	
		$txn_info = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE uid = '".$uid."' AND reference_txn_no = '".$reference_txn_no."' ");
		if($txn_info) {
			// "ERROR,Duplicate Transaction number";
			$result_code = '330';
		} else {
			$user_api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid = '".$uid."' ");
			if($user_api_info) {
				if($user_api_info->user_key == $userkey) {
					if($user_api_info->ip1==$ip || $user_api_info->ip2==$ip || $user_api_info->ip3==$ip || $user_api_info->ip4==$ip) {
						$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin FROM apps_user WHERE user_id = '".$user_api_info->user_id."' ");
						if($agent_info) {
								
							//include(DIR . '/library/recharge-request.php');
							// "ERROR,Invalid User ID";
							$result_code = '300';
							
						} else {
							// "ERROR,Invalid User ID";
							$result_code = '328';
						}
					} else {
						// "ERROR,Invalid IP";
						$result_code = '327';
					}			
				
				} else {
					// "ERROR,Invalid User Key";
					$result_code = '326';
				}
			} else {
				// "ERROR,Invalid User ID";
				$result_code = '325';
			}
		}
	}
} else {
	// Paramets are missing
	$result_code = '311';
}
$time = date("d-m-Y H:i:s");
if($result_code == '300') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Successful,".$time;
} else if($result_code == '301') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Processed,".$time;
} else if($result_code == '302') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$new_close_balance.",Transaction Failed,".$time;
} else if($result_code == '303') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Reversed,".$time;
} else if($result_code == '304') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Reversed,".$time;
} else if($result_code == '305') {
	$response_msg = "PENDING,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Pending,".$time;
} else if($result_code == '306') {
	$response_msg = "ERROR,Invalid Request";
} else if($result_code == '307') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Processed,".$time;
} else if($result_code == '308') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Submitted,".$time;
} else if($result_code == '309') {
	$response_msg = "ERROR,Invalid Request";
} else if($result_code == '310') {
	$response_msg = "ERROR,Duplicate Recharge try after 2 Hours";
} else if($result_code == '311') {
	$response_msg = "ERROR,Parameters are missing";
} else if($result_code == '312') {
	$response_msg = "ERROR,Invalid User";
} else if($result_code == '313') {
	$response_msg = "ERROR,Invalid User Pin";
} else if($result_code == '314') {
	$response_msg = "ERROR,Invalid Operator";
} else if($result_code == '315') {
	$response_msg = "ERROR,Service Downtime. Try Later";
} else if($result_code == '316') {
	$response_msg = "ERROR,Operator Downtime. Try Later";
} else if($result_code == '317') {
	$response_msg = "ERROR,Operator Downtime. Try Later";
} else if($result_code == '318') {
	$response_msg = "ERROR,Invalid Amount Range";
} else if($result_code == '319') {
	$response_msg = "ERROR,Insufficiant Balance";
} else if($result_code == '320') {
	$response_msg = "ERROR,";
} else if($result_code == '321') {
	$response_msg = "ERROR,Service not available";
} else if($result_code == '325') {
	$response_msg = "ERROR,Invalid User ID";
} else if($result_code == '326') {
	$response_msg = "ERROR,Invalid KEY";
} else if($result_code == '327') {
	$response_msg = "ERROR,Invalid IP Address";
} else if($result_code == '328') {
	$response_msg = "ERROR,Invalid User ID";
} else if($result_code == '329') {
	$response_msg = "ERROR,Invalid User Transaction No";
} else if($result_code == '330') {
	$response_msg = "ERROR,Duplicate Transaction number";
} else {
	$response_msg = "ERROR";
}
echo $response_msg;
exit();
?>
