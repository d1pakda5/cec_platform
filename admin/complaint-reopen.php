<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
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
		$api_txn_no = $recharge_info->api_txn_no;
		$response_date = $recharge_info->response_date;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$operator_ref_no = $recharge_info->operator_ref_no;
		$recharge_ip = $recharge_info->recharge_ip;
		$is_refunded = $recharge_info->is_refunded;
		$is_complaint = $recharge_info->is_complaint;
		if($is_complaint == 'y') {
			$com_info = $db->queryUniqueObject("SELECT complaint_id FROM complaints WHERE txn_no = '".$recharge_id."' ");
			if($com_info) {
				$complaint_id = $com_info->complaint_id;
			} else {
				$complaint_id = '';
			}
		} else {
			$complaint_id = '';
		}
			
	} else {
		echo "ERROR,Invalid Recharge ID";
		exit();		
	}
	
} else {
	echo "ERROR,Invalid Transaction ID";
	exit();
}
$array['complaint_status'] = getComplaintStatusList();
$array['recharge_status'] = getRechargeStatusList();
?>
<style>
.fancy-box .bg-status {
	background:#27ae61!important;
	border-color:#27ae61!important;
	color:#fff!important;
}
.fancy-box .bg-status-api {
	background:#36a2cf!important;
	border-color:#36a2cf!important;
}
.fancy-box h3.box-title {
	font-size:18px!important;
}
.fancy-box .body-inner {
	padding:0px;
	width:100%;
	float:left;
}
.fancy-box .body-inner .form-group {
	padding-bottom:2px;
	padding-top:2px;
	margin-bottom:1px;
	border-bottom:1px solid #eee;
}
.input-sm {
  height: 30px;
  padding: 3px 5px;
  font-size: 12px;
  line-height: 1.5;
  border-radius: 3px;
}
select.input-sm {
  height: 30px;
  line-height: 30px;
}
textarea.input-sm,
select[multiple].input-sm {
  height: auto;
}
.fancy-box .body-inner .form-control-static {
	min-height: 1px!important;
	padding-top:2px!important;
	padding-bottom:2px!important;
	margin-bottom:0px!important;
}
.fancy-box .body-inner .control-label {
	padding-top:2px;
	padding-bottom:2px;
	margin-bottom:0px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#frmRefund").bind('submit', function(e) {
		e.preventDefault();
		jQuery.ajax({
			type : "POST",
			cache : false,
			url : 'ajax/complaint-reprocess.php',
			data : jQuery(this).serializeArray(),
			success : function(data) {
			  jQuery.fancybox(data, {
					closeClick : false,
					autoSize : true,
					padding : 10,
					helpers : { 
						overlay : {closeClick: false}
					}
			  });
			}
		});
		return false;
	});
});
</script>
<div class="box fancy-box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">Txn No : <?php echo $txn_no;?></h3>
	</div>
	<div class="box-body no-padding">
		<div class="body-inner">			
			<div class="col-md-12">
			<form action="" method="post" id="frmRefund" class="form-horizontal">
				<input type="hidden" name="recharge_id" id="recharge_id" value="<?php echo $recharge_id;?>" />
				<input type="hidden" name="complaint_id" id="complaint_id" value="<?php echo $complaint_id;?>" />
				
				<div class="form-group">
					<label class="col-md-4 control-label">Mobile/Amount</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $account_no;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Operator Name</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $operator_name;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Amount</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo round($amount,2);?> Rs</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Current Status</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo getRechargeStatusLabel($array['recharge_status'],$status);?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Date</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo date(("d-m-Y H:i:s A"), strtotime($request_date));?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">API Txn No</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $api_txn_no;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">API Status</label>
					<div class="col-md-8">
						<p class="form-control-static" id="apiStatus"><?php echo $api_status;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">API Status Details</label>
					<div class="col-md-8">
						<p class="form-control-static" id="apiStatusDetail"><?php echo $api_status_details;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Operator Ref No.</label>
					<div class="col-md-8">
						<input type="text" name="operator_ref_no" id="operator_ref_no" value="<?php echo $operator_ref_no;?>" class="form-control input-sm" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Action</label>
					<div class="col-md-8">
						<select name="action" id="action" class="form-control input-sm">
							<option value=""></option>
							<option value="1">REFUND AMOUNT</option>
							<option value="2">SUCCESS RECHARGE</option>
							<option value="3">INVALID TRANSACTION</option>
							<option value="4">ALREADY REFUNDED</option>
							<option value="7">REVERT AMOUNT</option>
							<option value=""></option>
							<optgroup label="" class="red">
							<option value="5">REFUND AMOUNT (X)</option>
							<option value="6">SUCCESS RECHARGE (X)</option>
							<option value="8">REVERT AMOUNT (X)</option>
							</optgroup>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Remark</label>
					<div class="col-md-8">
						<textarea name="remark" id="remark" rows="1" class="form-control input-sm"></textarea>
					</div>
				</div>
				<?php 
				$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid='".$uid."' ");
				if($wallet) { ?>
				<div class="form-group">
					<label class="col-md-4 control-label">Available Balance</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $wallet->balance;?></p>
					</div>
				</div>
				<?php } ?>
				<div class="form-group">
					<label class="col-md-4 control-label"></label>
					<div class="col-md-8" style="margin:15px 0px;">
						<button type="submit" name="submit" id="submit" class="btn btn-info">
							<i class="fa fa-save"></i> Submit
						</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>