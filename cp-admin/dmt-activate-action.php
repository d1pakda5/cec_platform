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
	$request_info = $db->queryUniqueObject("SELECT dmt.*, user.user_id, user.company_name FROM dmt_activation_request dmt LEFT JOIN apps_user user ON dmt.dmt_request_uid = user.uid WHERE dmt.dmt_request_id = '".$request_id."' ");
	if($request_info) {
		$dmt_request_date =  $request_info->dmt_request_date;
		$dmt_request_uid =  $request_info->dmt_request_uid;
		$company_name =  $request_info->company_name;
		$dmt_activation_charge =  $request_info->dmt_activation_charge;
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
	jQuery("#frmRequest").bind('submit', function(e) {
		e.preventDefault();
		var actz = jQuery("#action").val();
		if(actz=='') {
			alert("Action cannot be blank");
			return false;
		} else {
			jQuery.ajax({
				type : "POST",
				cache : false,
				url : 'ajax/dmt-request-process.php',
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
		}
	});
});
</script>
<div class="box fancy-box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">Request No : <?php echo $request_id;?></h3>
	</div>
	<div class="box-body no-padding">
		<div class="body-inner">			
			<div class="col-md-12">
			<form action="" method="post" id="frmRequest" class="form-horizontal">
				<input type="hidden" name="request_id" id="request_id" value="<?php echo $request_id;?>" />				
				<div class="form-group">
					<label class="col-md-4 control-label">Request Date</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $dmt_request_date;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Request User</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $company_name;?> <?php echo $dmt_request_uid;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Activation Charge</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo round($dmt_activation_charge,2);?> Rs</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Action</label>
					<div class="col-md-8">
						<select name="action" id="action" class="form-control input-sm">
							<option value=""></option>
							<option value="1">Accept</option>
							<option value="2">Reject</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Remark</label>
					<div class="col-md-8">
						<textarea name="remark" id="remark" rows="1" class="form-control input-sm"></textarea>
					</div>
				</div>
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