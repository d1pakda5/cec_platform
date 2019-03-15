<?php
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
$array['recharge_status'] = getRechargeStatusList();
if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.uid, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;		
		$txn_no = $recharge_info->recharge_id;
		$uid = $recharge_info->uid;
		$api_id = $recharge_info->api_id;
		$service_type = $recharge_info->service_type;
		$operator_id = $recharge_info->operator_id;
		$operator_name = $recharge_info->operator_name;
		$account_no = $recharge_info->account_no;
		$amount = $recharge_info->amount;
		$surcharge = $recharge_info->surcharge;
		$status = $recharge_info->status;
		$status_details = $recharge_info->status_details;
		$request_date = $recharge_info->request_date;
		$request_txn_no = $recharge_info->request_txn_no;
		$reference_txn_no = $recharge_info->reference_txn_no;
		$api_name = $recharge_info->api_name;
		$api_txn_no = $recharge_info->api_txn_no;
		$response_date = $recharge_info->response_date;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$operator_ref_no = $recharge_info->operator_ref_no;
		$request_ip = $recharge_info->recharge_ip;
		$company_name = $recharge_info->company_name;	
		$is_refunded = $recharge_info->is_refunded;
		$param_info = $db->queryUniqueObject("SELECT * FROM apps_recharge_details WHERE recharge_id = '".$recharge_id."' ");
		if($param_info) {
			$customer_name = $param_info->customer_name;
			$customer_mobile = $param_info->customer_mobile;
			$customer_email = $param_info->customer_email;
			$customer_city = $param_info->customer_city;
			$customer_account = $param_info->customer_account;
			$billing_cycle = $param_info->billing_cycle;
			$dob = $param_info->dob;
			$sub_division = $param_info->sub_division;
			$billing_unit = $param_info->billing_unit;
			$pc_number = $param_info->pc_number;
		} else {
			$customer_name = "";
			$customer_mobile = "";
			$customer_email = "";
			$customer_city = "";
			$customer_account = "";
			$billing_cycle = "";
			$dob = "";
			$sub_division = "";
			$billing_unit = "";
			$pc_number = "";
		}
	} else {
		echo "ERROR,Invalid Recharge ID";
		exit();		
	}
	
} else {
	echo "ERROR,Invalid Transaction ID";
	exit();
}
?>
<style>
.box {margin-bottom:0px;}
.bg-status {
	background:#27ae61!important;
	border-color:#27ae61!important;
	color:#FFFFFF!important;
}
.bg-status-api {
	background:#36a2cf!important;
	border-color:#36a2cf!important;
}
.fancy-body-inner {
	width:100%;
	float:left;
}
.fancy-body-inner .table {border-collapse:collapse; margin-bottom:0px;}
.fancy-body-inner .table td {border:1px solid #eee; padding:2px 8px!important; vertical-align:top;}
</style>
<div class="box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">Recharge Status : <?php echo $txn_no;?></h3>
	</div>
	<div class="box-body no-padding">
		<div class="fancy-body-inner">
			<table class="table table-condensed">
				<tr>
					<td>User Details</td>
					<td><?php echo $company_name;?> (<?php echo $uid;?>)</td>
				</tr>				
				<tr>
					<td>Operator Name</td>
					<td><?php echo $operator_name;?> </td>
				</tr>
				<tr>
					<td>Mobile/Account</td>
					<td><?php echo $account_no;?></td>
				</tr>
				<?php if($operator_id == '58') {?>
				<tr>
					<td>Billing Unit</td>
					<td><?php echo $billing_unit;?></td>
				</tr>
				<tr>
					<td>PC Number</td>
					<td><?php echo $pc_number;?></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Amount</td>
					<td><?php echo round($amount, 2);?></td>
				</tr>
				<tr>
					<td>Surcharge</td>
					<td><?php echo $surcharge;?></td>
				</tr>				
				<tr>
					<td>Operator Ref No.</td>
					<td><?php echo $operator_ref_no;?></td>
				</tr>
				<tr>
					<td>Status</td>
					<td><?php echo getRechargeStatusLabel($array['recharge_status'],$status);?></td>
				</tr>
				<tr>
					<td>Status Details</td>
					<td><?php echo $status_details;?></td>
				</tr>
				<tr>
					<td>Request Time</td>
					<td><?php echo $request_date;?></td>
				</tr>
				<tr>
					<td>Request Txn No</td>
					<td><b><?php echo $request_txn_no;?></b></td>
				</tr>				
				<tr>
					<td>Response Time</td>
					<td><?php echo $response_date;?></td>
				</tr>
				<tr>
					<td>API</td>
					<td><?php echo $api_name;?></td>
				</tr>
				<tr>
					<td>API Txn No</td>
					<td><?php echo $api_txn_no;?></td>
				</tr>
				<tr>
					<td>API Status</td>
					<td><?php echo $api_status;?></td>
				</tr>
				<tr>
					<td>API Status Details</td>
					<td><?php echo $api_status_details;?></td>
				</tr>
				<tr>
					<td>Request IP Address</td>
					<td><?php echo $request_ip;?></td>
				</tr>
			</table>
		</div>
	</div>
</div>