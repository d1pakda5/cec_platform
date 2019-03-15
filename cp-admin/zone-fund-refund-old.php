<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$trans_info = $db->queryUniqueObject("SELECT * FROM trans_deduct WHERE transaction_id = '".$request_id."' ");
if($trans_info) { 
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$trans_info->account_id."' ");
	if($user_info) {
		$user = $user_info->fullname;
	} else {
		$user = "";
	}
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
	padding-bottom:3px;
	padding-top:3px;
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
			url : 'ajax/zone-fund-refund.php',
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
		<h3 class="box-title">Transaction Details : <?php echo $request_id;?></h3>
	</div>
	<div class="box-body no-padding">
		<div class="body-inner">			
			<div class="col-md-12">
			<form action="" method="post" id="frmRefund" class="form-horizontal">
				<input type="hidden" name="amount" id="amount" value="<?php echo $trans_info->amount;?>" />
				<input type="hidden" name="account_id" id="account_id" value="<?php echo $trans_info->account_id;?>" />
				<input type="hidden" name="transaction_id" id="transaction_id" value="<?php echo $trans_info->transaction_id;?>" />
				<div class="form-group">
					<label class="col-md-4 control-label">User</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $user;?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Amount</label>
					<div class="col-md-8">
						<p class="form-control-static"><b><?php echo $trans_info->amount;?> Rs</b></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Closing Balance</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo $trans_info->closing_balance;?> Rs</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label">Date</label>
					<div class="col-md-8">
						<p class="form-control-static"><?php echo date(("d-m-Y H:i:s A"), strtotime($trans_info->transaction_date));?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label"></label>
					<div class="col-md-8" style="margin:15px 0px;">
						<button type="submit" name="submit" id="submit" class="btn btn-info">
							<i class="fa fa-save"></i> Revert Fund
						</button>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>
<?php			
} else {
	echo "ERROR,Invalid Recharge ID";
	exit();		
}
?>
