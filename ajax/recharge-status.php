<?php
session_start();
include("../config.php");
$error = 0;
if(!isset($_GET['token']) || $_GET['token'] != $_SESSION['token']) { exit("Token not match"); }
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
$array['recharge_status'] = getRechargeStatusList();
if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$retailer = $db->queryUniqueObject("SELECT uid,company_name FROM apps_user WHERE uid = '".$recharge_info->uid."' ");
		$recharge_id = $recharge_info->recharge_id;		
		$txn_no = $recharge_info->recharge_id;
		$retailer_uid = $recharge_info->uid;
		$recharge_mode = $recharge_info->recharge_mode;		
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
		$api_txn_no = $recharge_info->api_txn_no;
		$response_date = $recharge_info->response_date;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$operator_ref_no = $recharge_info->operator_ref_no;
		$request_ip = $recharge_info->recharge_ip;
		$is_refunded = $recharge_info->is_refunded;
			
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
.bg-status {
	background:#27ae61!important;
	border-color:#27ae61!important;
	color:#fff;
}
.bg-status h3.box-title{
	color:#fff;
	font-weight:700!important;
}
.bg-status-api {
	background:#36a2cf!important;
	border-color:#36a2cf!important;
}
.fancy-body-inner {
	padding:0px;
	margin:0px;
	width:100%;
	float:left;
}
.fancy-body-inner .table {border-collapse:collapse; margin-bottom:0px;}
.fancy-body-inner .table td {border:1px solid #eee; padding:2px 8px!important; vertical-align:top;}
.fancy-body-inner .form-group {
	margin-bottom:5px;
}
</style>
<div class="box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">Recharge Status : <?php echo $txn_no;?></h3>
	</div>
	<div class="box-body no-padding">
		<div class="fancy-body-inner">
			<table class="table table-condensed">					
				<tr>
					<td>Status</td>
					<td><?php echo getRechargeStatusLabelUser($status);?></td>
				</tr>
				<tr>
					<td>Recharge Mode</td>
					<td><?php echo $recharge_mode;?></td>
				</tr>	
				<tr>
					<td>Service Name</td>
					<td><?php echo $service_type;?></td>
				</tr>		
				<tr>
					<td>Operator Name</td>
					<td><?php echo $operator_name;?></td>
				</tr>
				<tr>
					<td>Mobile/Account ID</td>
					<td><?php echo $account_no;?></td>
				</tr>
				<tr>
					<td>Amount</td>
					<td><?php echo round($amount, 2);?> Rs</td>
				</tr>
				<tr>
					<td>Surcharge</td>
					<td><?php if($surcharge != '0') { echo round($surcharge, 2). " Rs"; } else { echo "-";}?></td>
				</tr>
				<tr>
					<td>Operator Ref No.</td>
					<td><?php echo getOperatorRefNo($operator_ref_no,$status);?></td>
				</tr>
				<tr>
					<td>Request Time</td>
					<td><?php echo $request_date;?></td>
				</tr>
				<tr>
					<td>Response Time</td>
					<td><?php if($response_date != '0000-00-00 00:00:00') { echo $response_date; } else { echo "NA";}?></td>
				</tr>
				<?php if($recharge_mode == 'API') { ?>
				<tr>
					<td>Your Txn No</td>
					<td><?php echo $reference_txn_no;?></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Recharge IP</td>
					<td><?php echo $request_ip;?></td>
				</tr>
				<tr>
					<td>Retailer</td>
					<td><?php echo $retailer->company_name;?></td>
				</tr>
				<tr>
					<td>Retailer UID</td>
					<td><?php echo $retailer->uid;?></td>
				</tr>
			</table>
		</div>
	</div>
</div>