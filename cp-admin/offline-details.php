<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$recharge_id = isset($_GET['recharge_id']) && $_GET['recharge_id'] != '' ? mysql_real_escape_string($_GET['recharge_id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['action'] == '' || $_POST['remark'] == '') {
		$error = 1;
	} else {
		$operator_name = mysql_real_escape_string($_POST['operator_name']);
		$uid = mysql_real_escape_string($_POST['uid']);
		$amount = mysql_real_escape_string($_POST['amount']);
		$account = mysql_real_escape_string($_POST['account']);
		$operator_ref_no = mysql_real_escape_string($_POST['operator_ref_no']);
		$is_csms = isset($_POST['is_csms']) ? $_POST['is_csms'] : "n";
		$is_rsms = isset($_POST['is_rsms']) ? $_POST['is_rsms'] : "n";
		$customer_mobile = mysql_real_escape_string($_POST['customer_mobile']);
		$retailer_mobile = mysql_real_escape_string($_POST['retailer_mobile']);
		if($_POST['action'] == '1') {			
			$db->query("UPDATE apps_recharge SET status = '0', status_details = 'Transaction Successful', operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_id."' ");
			//Customer SMS
			if($is_csms == 'y') {
				$message = "Dear Customer, Your ".$operator_name." bill payment of Rs ".$amount." against a/c ".$account." processed successfully, Your transaction ref is ".$operator_ref_no;
				smsSendSingle($customer_mobile, $message, 'recharge');
			}
			//Retailer SMS
			if($is_rsms == 'y') {
				$message = "Transaction Success Txn No. ".$recharge_id.", ".$operator_name." bill payment of Rs ".$amount." against a/c ".$account." processed successfully, Your transaction ref is ".$operator_ref_no;
				smsSendSingle($retailer_mobile, $message, 'recharge');
			}
			header("location:offline-details.php?recharge_id=".$recharge_id."&error=3");
		} else if($_POST['action'] == '2') {
			
			$db->query("UPDATE apps_recharge SET status = '2', status_details = 'Transaction Failed' WHERE recharge_id = '".$recharge_id."' ");
			$complaint = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."' ");
			if($complaint) {}
			else
			{
			    	$db->execute("INSERT INTO `complaints`(`complaint_id`, `complaint_by`, `txn_no`, `uid`, `complaint_date`, `status`, `refund_status`) VALUES ('', '".$uid."', '".$recharge_id."', '".$uid."', NOW(), '0', '0')");
			}
			//Customer SMS
			if($is_csms == 'y') {
				$message = "Dear Customer, Your ".$operator_name." bill payment of Rs ".$amount." against a/c ".$account." is Failed, amount has been refunded.";
				smsSendSingle($customer_mobile, $message, 'recharge');
			}
			//Retailer SMS
			if($is_rsms == 'y') {
				$message = "Transaction Failed Txn No. ".$recharge_id.", ".$operator_name." bill payment of Rs ".$amount." against a/c ".$account." is Failed, amount has been refunded.";
				smsSendSingle($retailer_mobile, $message, 'recharge');
			}
			header("location:offline-details.php?recharge_id=".$recharge_id."&error=2");
		}
	}
}
$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, user.user_id, user.uid, user.company_name, user.mobile FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$recharge_id."' ");
$param_info = $db->queryUniqueObject("SELECT * FROM apps_recharge_details WHERE recharge_id = '".$recharge_id."' "); 
$meta['title'] = "Recharge/Transaction Detail";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#offlineForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add fund?")) {
        form.submit();
      }
		},
	  rules: {
	  	action: {
				required: true
			},
			operator_ref_no: {
				required:function () {
					return jQuery('select[name="action"]').val() === '1';
				}
			},
			remark: {
				required:true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Payment <small>/ Offline</small></div>
			<div class="pull-right">
				<a href="rpt-offline.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 5) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-check"></i> Transaction has been rollback due to internal error.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-success"></i> Transaction successful, Wallet updated.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Transaction Successfull.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-danger"></i> Transaction Failed.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-times"></i> Oops, Some parameters are missing!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-plus-square"></i> <?php echo $recharge_info->operator_name;?></h3>
					</div>
					<div class="container">
					    &nbsp;&nbsp;
					  <a type="button" target="_blank" class="btn  pull-right" href="https://paytm.com/recharge"><img style="height: 26px;" src="../images/paytm.png"></a>
					
					    &nbsp;&nbsp;&nbsp;
					<a type="button" target="_blank" class="btn  pull-right" href="https://www.freecharge.in"><img style="width: 110px;" src="https://s.freecharge.in/desktop/images/fc_logo.png"></a>
					&nbsp;&nbsp;&nbsp;
					<a type="button" class="btn pull-right" target="_blank" href="recharge2.php?recharge_id=<?php echo $recharge_id?>"><img style="width: 139px;" src="../images/logo-ec.png"></a>
				
					</div>
					
					<form action="" method="post" id="offlineForm" class="form-horizontal">
					<input type="hidden" name="uid" id="uid" readonly="" value="<?php echo $recharge_info->uid;?>" class="form-control">
					<div class="box-body padding-50">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="operator_name" id="operator_name" readonly="" value="<?php echo $recharge_info->operator_name;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile/Account :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="account" id="account" readonly="" value="<?php echo $recharge_info->account_no;?>" class="form-control">
									</div>
								</div>
								<?php if($recharge_info->operator_id == '58') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Billing Unit :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $param_info->billing_unit;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">P.C. Number :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $param_info->pc_number;?></p>
									</div>
								</div>
								<?php } ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Amount :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $recharge_info->amount;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Surcharge :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $recharge_info->surcharge;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 text-green control-label">Retailer Detail :</label>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Name :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $recharge_info->company_name;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="retailer_mobile" id="retailer_mobile" readonly="" value="<?php echo $recharge_info->mobile;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 text-green control-label">Customer Detail :</label>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="customer_name" id="customer_name" readonly="" value="<?php echo $param_info->customer_name;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="customer_mobile" id="customer_mobile" readonly="" value="<?php echo $param_info->customer_mobile;?>" class="form-control">
									</div>
								</div>								
								<div class="form-group">
									<label class="col-sm-4 control-label">Email</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="customer_email" id="customer_email" readonly="" value="<?php echo $param_info->customer_email;?>" class="form-control">
									</div>
								</div>								
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="customer_city" id="customer_city" readonly="" value="<?php echo $param_info->customer_city;?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Amount :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="amount" id="amount" readonly="" value="<?php echo round($recharge_info->amount);?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Surcharge :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="surcharge" id="surcharge" readonly="" value="<?php echo $recharge_info->surcharge;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<select name="action" id="action" class="form-control">
											<option value=""></option>
											<option value="1">SUCCESS</option>
											<option value="2">FAILED</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Reference No :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="operator_ref_no" id="operator_ref_no" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Customer SMS :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_csms" id="is_csms" value="y" checked="checked"> Yes</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Retailer SMS :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_rsms" id="is_rsms" value="y" checked="checked"> Yes</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Remark :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="remark" id="remark" class="form-control"></textarea>
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<?php if($recharge_info->status == '1' || $recharge_info->status == '7' || $recharge_info->status == '8') { ?>
								<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
								<?php } else { ?>
								<button type="submit" disabled="disabled" class="btn btn-info pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
								<?php } ?>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>