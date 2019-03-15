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
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.* FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;	
		$user_type= $recharge_info->user_type;
		$txn_no = $recharge_info->recharge_id;
		$uid = $recharge_info->uid;
		$user_id=$recharge_info->user_id;
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
function bindvalue()
{
    var ref_no=$("#ref_no").val();
    var operator_id=$("#operator_id").val();

    if(operator_id=='20')
    {
        var first=ref_no.substring(0, ref_no.length-6);
        var random=randomString(6, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        var reference=first+random;
    }
    else
    {
        var first=ref_no.substring(0, ref_no.length-6);
        var random=Math.floor(100000 + Math.random() * 900000);
        var reference=first+random;
    }
    $("#operator_ref_no").val(reference);
    
}
function randomString(length, chars) {
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}
$(document).ready(function() {
	$("#frmRefund").bind('submit', function(e) {
		e.preventDefault();
		jQuery.ajax({
			type : "POST",
			cache : false,
			url : 'ajax/recharge-status-update.php',
			data : $(this).serializeArray(),
			success : function(data) {
			  $.fancybox(data, {
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
				<input type="hidden" name="user_type" id="user_type" value="<?php echo $user_type;?>" />
				<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>" />
				<input type="hidden" name="uid" id="uid" value="<?php echo $uid;?>" />
				<input type="hidden" name="operator_id" id="operator_id" value="<?php echo $operator_id;?>" />
					<input type="hidden" name="current_status" id="current_status" value="<?php echo $status;?>" />
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
					<label class="col-md-4 control-label">Transaction Ref NO.</label>
					<div class="col-md-8">
						<select id="ref_no" name="ref_no" class="form-control" onchange="bindvalue()">
						    <option value=""> </option>
						    <?php
									$query2 = $db->query("SELECT sample_ref_no,operator_name FROM operators where operator_id in('1','2','3','5','9','10','20','27','28','30','35') order by operator_name ASC ");
									while($result2 = $db->fetchNextObject($query2)) {	?>
									<option value="<?php echo $result2->sample_ref_no;?>"> <?php echo $result2->operator_name;?> </option>
									<?php } ?>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-md-4 control-label">Operator Ref No.</label>
					<div class="col-md-8">
						<input type="text" name="operator_ref_no" id="operator_ref_no" value="<?php echo $operator_ref_no;?>" class="form-control input-sm" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Status</label>
					<div class="col-md-8">
						<select name="status" id="status" class="form-control">
							<option value="">--Select Status--</option>
							<?php foreach($array['recharge_status'] as $data) { ?>
								<option value="<?php echo $data['id'];?>"<?php if($status==$data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label"></label>
					<div class="col-md-8" style="margin:15px 0px;">
						<button type="submit" name="submit" id="submit" class="btn btn-success">
							<i class="fa fa-check"></i> Save Status
						</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>