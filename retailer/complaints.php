<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$prev_date = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") .' -72 HOURS'));
$is_true = false;
if(isset($_POST['submit'])) {
	if($_POST['recharge_id'] == '') {
		$error = 1;		
	} else {		
		$recharge_id = htmlentities(addslashes($_POST['recharge_id']),ENT_QUOTES);			
		$recharge = $db->queryUniqueObject("SELECT * FROM apps_recharge WHERE recharge_id = '".$_POST['recharge_id']."' AND uid = '".$_SESSION['retailer_uid']."'");
		if($recharge) {
			$is_true = true;
			if($recharge->is_refunded == 'y') {
				$error = 3;
			} else {
				if($recharge->operator_ref_no != '') {
					$error = 4;
				} else {
					$trans_info = $db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no = '".$recharge_id."' ORDER BY transaction_id DESC");
					if($trans_info) {
						if($trans_info->transaction_term == 'RECHARGE') {						
							$complaint = $db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no = '".$recharge_id."' ");
							if($complaint) {
								if($complaint->status == '1') {				
									if($complaint->refund_status == '0') {
										$error = 6;
									} else if($complaint->refund_status == '1') {
										$error = 7;
									} else if($complaint->refund_status == '2') {
										$error = 8;
									} else if($complaint->refund_status == '3') {
										$error = 9;
									}
								} else {
									$error = 6;
								}
							} else {
								if($recharge->request_date > $prev_date) {
									$db->execute("INSERT INTO `complaints`(`complaint_id`, `complaint_by`, `txn_no`, `uid`, `complaint_date`, `status`, `refund_status`) VALUES ('', '".$_SESSION['retailer_uid']."', '".$_POST['recharge_id']."', '".$recharge->uid."', NOW(), '0', '0')");
									$db->execute("UPDATE `apps_recharge` SET `is_complaint`='y' WHERE recharge_id = '".$recharge->recharge_id."' ");
									$error = 11;
								} else {
									$error = 10;
								}								
							}
						} else {
							$error = 3;
						}
					} else {
						$error = 5;
					}
				}
			}
		} else {
			$error = 2;
		}						
	}
}
$array['recharge_status'] = getRechargeStatusList();
$meta['title'] = "Complaints ";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#complaintForm').validate({
	  rules: {
	  	recharge_id: {
				required: true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Complaints <small>/ Add New</small></div>
		</div>
		<?php if($error == 11) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Complaint Registered successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 10) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Transaction is to old, only registred previous 3 days.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 9) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Complaint resolved, Transaction Invalid!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 8) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Complaint resolved, Recharge Success!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 7) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Complaint resolved, Recharge Refunded!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 6) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Complaint pending, resolved soon!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 5) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Transaction not found!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Recharge successful!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Already refunded!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Insert a valid Transaction Id!!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Register Complaint</h3>
					</div>
					<form action="" method="post" id="complaintForm" class="form-horizontal">
					<div class="box-body padding-50">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-xs-12">TXN No <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="recharge_id" id="recharge_id" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
							    <?php if($_SESSION['retailer_uid']=='20032374')
								{?>
								<button type="submit" disabled name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>

								<?php } else {
								?>
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>

								<?php }?>
								
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Recharge Details</h3>
					</div>
					<div class="box-body min-height-300">
						<?php if($is_true) {
						$operator_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id = '".trim($recharge->operator_id)."'");
						?>
							<table class="table table-hover">	
								<tr>
									<td>Status</td>
									<td><?php echo getRechargeStatusLabel($array['recharge_status'],$recharge->status);?></td>
								</tr>		
								<tr>
									<td>Recharge Txn</td>
									<td><?php echo $recharge->recharge_id;?></td>
								</tr>							
								<tr>
									<td>Recharge Date</td>
									<td><?php echo date("d-m-Y H:i:s", strtotime($recharge->request_date));?></td>
								</tr>
								<tr>
									<td>Service Type</td>
									<td><?php echo $recharge->service_type;?></td>
								</tr>
								<tr>
									<td>Operator</td>
									<td><?php echo $operator_info->operator_name;?></td>
								</tr>
								
								<tr>
									<td>Mobile/Account No</td>
									<td><?php echo $recharge->account_no;?></td>
								</tr>
								<tr>
									<td>Amount</td>
									<td><?php echo round($recharge->amount,2);?></td>
								</tr>
								<tr>
									<td>Operator Ref No</td>
									<td><?php echo getOperatorRefNo($recharge->operator_ref_no,$recharge->status);?></td>
								</tr>
							</table>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>