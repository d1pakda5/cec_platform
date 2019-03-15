<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
$array['recharge_status'] = getRechargeStatusList();
if(isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['status']) && $_GET['status'] != '') 
{
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.uid, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;		
		$txn_no = $recharge_info->recharge_id;
		$uid = $recharge_info->uid;
		$api_id = $recharge_info->api_id;
		$operator_name = $recharge_info->operator_name;
		$account_no = $recharge_info->account_no;
		$amount = $recharge_info->amount;
	    $status = $recharge_info->status;
	    $api_name = $recharge_info->api_name;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$operator_ref_no = $recharge_info->operator_ref_no;
		$api_complaint_remark = $recharge_info->api_complaint_remark;
		$company_name = $recharge_info->company_name;	
		
	}
	
}

if(isset($_GET['id']) && $_GET['id'] != '') {
    if(empty($_GET['status']))
    {
 $db->execute("Update apps_recharge set is_api_complaint=1 WHERE recharge_id = '".$request_id."' ");

	echo "Complaint Registered Successfully";
    }
			
	} else {
		echo "ERROR,Invalid Recharge ID";
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
	<?php if(isset($_GET['status']) && $_GET['status'] != '' && ($_GET['id']) && $_GET['id'] != '' )
	{ ?>
<div class="box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">API Complaint Status</h3>
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
				<tr>
					<td>Amount</td>
					<td><?php echo round($amount, 2);?></td>
				</tr>
				<tr>
					<td>API Complaint Remark</td>
					<td><?php echo $api_complaint_remark;?></td>
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
				
			</table>
		</div>
	</div>
</div>	
<?php } ?> 
		    