<?php
include("../config.php");
$error = 0;
$username = "";
$update_date = "";
$status = "";
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : 0;
if($type == 'complaint') {
	$info = $db->queryUniqueObject("SELECT * FROM complaints WHERE complaint_id = '".$request_id."' ");
	if($info) {
		if($info->is_cron == '1') {
			$username = "Cron";
		} else {
			$user = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$info->refund_by."' ");
			if($user) {
				$username = $user->fullname;
			} else {
				$username = "Auto";
			}
		}
		$update_date = date("d/m/Y H:i:s A", strtotime($info->refund_date));
		if($info->refund_status=='1' || $info->refund_status=='5') {
			$status = "Refunded";
		} else if($info->refund_status=='2' || $info->refund_status=='6') {
			$status = "Already Success";
		} else if($info->refund_status=='3') {
			$status = "Invalid Transaction";
		} else if($info->refund_status=='4') {
			$status = "Already Refunded";
		} else if($info->refund_status=='7' || $info->refund_status=='8') {
			$status = "Revert Amount";
		} else {
			$status = "Error, Undefine";
		}
	}
}
if($info) {
	
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
<div class="box">
	<div class="box-header bg-status">
		<h3 class="box-title">User Details</h3>
	</div>
	<div class="box-body no-padding">
		<div class="fancy-body-inner">
			<table class="table table-condensed">
				<tr>
					<td>Updated By</td>
					<td><?php echo $username;?></td>
				</tr>				
				<tr>
					<td>Updated Date</td>
					<td><?php echo $update_date;?> </td>
				</tr>
				<tr>
					<td>Updated Status</td>
					<td><?php echo $status;?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<?php } else { 
	echo "ERROR,Invalid Transaction ID";
	exit();
} ?>