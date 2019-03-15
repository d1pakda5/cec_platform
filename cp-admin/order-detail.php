<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$order_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$ip = $_SERVER['REMOTE_ADDR'];
//
if(isset($_POST['submit'])) {
	if($_POST['action']=='' || $_POST['remark']=='') {
		$error = 1;
	} else {
		$message = mysql_real_escape_string($_POST['sms']);
		$mobile = mysql_real_escape_string($_POST['mobile']);
		$order_remark = mysql_real_escape_string($_POST['remark']);
		$db->query("UPDATE orders SET order_status='".$_POST['action']."', order_remark='".$order_remark."', order_update_user='".$_SESSION['admin']."', order_update_date=NOW(), order_update_ip='".$ip."' WHERE id='".$order_id."' ");
		
		if($_POST['action']=='completed') {
			$db->query("UPDATE apps_recharge SET status='0', status_details='Transaction successful' WHERE recharge_id='".$_POST['recharge_id']."' ");
			//Customer SMS
			if($mobile!='') {
				smsSendSingle($mobile, $message, 'recharge');
			}
			header("location:order-detail.php?id=".$order_id."&error=3");
			exit();
		} else if($_POST['action']=='submitted') {
			$db->query("UPDATE apps_recharge SET status='8', status_details='Transaction Submitted' WHERE recharge_id='".$_POST['recharge_id']."' ");
			//Customer SMS
			if($mobile!='') {
				smsSendSingle($mobile, $message, 'recharge');
			}
			header("location:order-detail.php?id=".$order_id."&error=3");
			exit();
			} else if($_POST['action']=='refunded') {
			//$db->query("UPDATE apps_recharge SET status='0', status_details='Transaction successful' WHERE recharge_id='".$_POST['recharge_id']."' ");
			//
			header("location:order-detail.php?id=".$order_id."&error=2");
			exit();
		}
	}
}

$order_info = $db->queryUniqueObject("SELECT * FROM orders WHERE id='".$order_id."'");
if(!$order_info) {
	header("location:orders.php");
	exit();
}
$pro_info = $db->queryUniqueObject("SELECT * FROM products WHERE id='".$order_info->product_id."'");
$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$order_info->agent_uid."'");
$meta['title'] = "Order Detail";
include('header.php');
?>
<style>
.control-label { text-align:left!important;}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('#orderFrm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to process order?")) {
        form.submit();
      }
		},
	  rules: {
	  	action: {
				required: true
			},
			remark: {
				required:true
			}
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Order ID: <?php echo $order_info->id;?></div>
			<div class="pull-right">
				<a href="orders.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
			</div>
		</div>
		<?php if($error==5) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Transaction has been rollback due to internal error.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> Transaction successful, Wallet updated.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Order Process Successfull.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Order Process Failed.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some parameters are missing!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-plus-square"></i> Order Detail</h3>
					</div>
					<div class="box-body padding-50 form-horizontal">								
						<div class="form-group">
							<label class="col-sm-4 control-label">Order ID :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->id;?></p>
							</div>
						</div>								
						<div class="form-group">
							<label class="col-sm-4 control-label">Order Date :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo date("d/m/Y H:i A", strtotime($order_info->order_date));?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Order Total :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->order_amount;?></p>
							</div>
						</div>		
						<div class="form-group">
							<label class="col-sm-4 control-label">Product Name :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $pro_info->product_name;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Product Price :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $pro_info->price;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Name :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_name;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Mobile :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_mobile;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Email :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_email;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Address :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_address;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer City :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_city;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Pincode :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_pincode;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer State :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $order_info->customer_state;?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label text-green">Retailer/Agent :</label>
							<div class="col-sm-8 jrequired">
								<p class="form-control-static"><?php echo $agent_info->company_name;?> (<?php echo $agent_info->uid;?>)</p>
							</div>
						</div>								
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-plus-square"></i> Process Order</h3>
					</div>
					<div class="box-body padding-50">		
						<form action="#" method="post" class="form-horizontal" id="orderFrm">
						<input type="hidden" name="recharge_id" value="<?php echo $order_info->recharge_id;?>" />
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer SMS :</label>
							<div class="col-sm-8 jrequired">
								<textarea type="text" name="sms" id="sms" class="form-control"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Mobile :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="mobile" id="mobile" readonly="" value="<?php echo $order_info->customer_mobile;?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Order Action :</label>
							<div class="col-sm-8 jrequired">
								<select name="action" id="action" class="form-control">
									<option value="">-- Select Action --</option>
									<option value="submitted">Submitted</option>
									<option value="completed">Completed</option>
									<option value="refunded">Refunded</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Remark (if any):</label>
							<div class="col-sm-8 jrequired">
								<textarea name="remark" id="remark" class="form-control"></textarea>
							</div>
						</div>
						<div class="form-group">
							<?php if($order_info->order_status=='pending') { ?>
							<label class="col-sm-4 control-label">&nbsp;</label>
							<div class="col-sm-8 jrequired">
								<button type="submit" name="submit" id="submit" class="btn btn-primary">
									<i class="fa fa-save"></i> Submit
								</button>
							</div>
							<?php } else { ?>
							<label class="col-sm-4 control-label">&nbsp;</label>
							<div class="col-sm-8 jrequired">
								<button type="submit" disabled="disabled" class="btn btn-primary">
									<i class="fa fa-save"></i> Submit
								</button>
							</div>
							<?php } ?>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>