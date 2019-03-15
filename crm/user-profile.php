<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$uid = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".mysql_real_escape_string($_GET['uid'])."' ");
	if($uid) {
		$request_id = $uid->user_id;
	}
}
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$request_id."' AND assign_id='".$_SESSION['accmgr']."' ");
if(!$user) {
	header("location:index.php");
	exit();
}
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id='".$user->user_id."' ");
if(!$wallet) {
	header("location:index.php");
	exit();
}
$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$user->uid."' ");
//
$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<style>
.list-buttons .btn {
	margin-bottom:10px;
	text-align:left;
}
.profile .form-group .control-label {
	font-weight:normal;
	text-align:left;
}
</style>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"><?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<?php if($user->user_type == '1') {?>
				<a href="api-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '3') {?>
				<a href="master-distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '4') {?>
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '5') {?>
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } ?>
			</div>
		</div>
		<?php if($error==2) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Password has been reset successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==1) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Pin has been reset successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Profile</h3>
					</div>
					<div class="box-body">					
						<div class="row preview-card">
							<div class="col-sm-12">	
								<h3>Basic Profile Details</h3>
							</div>							
							<div class="col-sm-6">
								<p><span class="name">Company Name</span> <span class="dots">:</span> <span class="value"><?php echo $user->company_name;?></span></p>
								<p><span class="name">Full Name</span> <span class="dots">:</span> <span class="value"><?php echo $user->fullname;?></span></p>
								<p><span class="name">Mobile</span> <span class="dots">:</span> <span class="value"><?php echo $user->mobile;?></span></p>
								<p><span class="name">Phone</span> <span class="dots">:</span> <span class="value"><?php echo $user->phone;?></span></p>
								<p><span class="name">Email</span> <span class="dots">:</span> <span class="value"><?php echo $user->email;?></span></p>
								<p><span class="name">Aadhaar Number</span> <span class="dots">:</span> <span class="value"><?php echo $user->aadhaar;?></span></p>
								<p><span class="name">Address</span> <span class="dots">:</span> <span class="value"><?php echo $user->address;?></span></p>
								<p><span class="name">City</span> <span class="dots">:</span> <span class="value"><?php echo $user->city;?></span></p>
								<p><span class="name">State</span> <span class="dots">:</span> <span class="value"><?php echo $user->states;?></span></p>
								<p><span class="name">Pincode</span> <span class="dots">:</span> <span class="value"><?php echo $user->pincode;?></span></p>
							</div>
							<div class="col-sm-6">
								<p><span class="name">UID</span> <span class="dots">:</span> <span class="value"><?php echo $user->uid;?></span></p>
								<p><span class="name">Username</span> <span class="dots">:</span> <span class="value"><?php echo $user->username;?></span></p>
								<?php if($user->user_type=='4' || $user->user_type=='5') { ?>
								<?php $mdist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid='".$user->mdist_id."'");?>
								<p><span class="name">Master Distributor</span> <span class="dots">:</span> <span class="value"><?php echo $mdist->company_name;?> (<?php echo $user->mdist_id;?>)</span></p>
								<?php } ?>
								<?php if($user->user_type=='5') { ?>
								<?php $dist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->dist_id."'"); ?>
								<p><span class="name">Distributor</span> <span class="dots">:</span> <span class="value"><?php echo $dist->company_name;?> (<?php echo $user->dist_id;?>)</span></p>
								<?php } ?>
								<p><span class="name">Online Access</span> <span class="dots">:</span> <span class="value"><?php if($user->is_access=='y') { echo "Yes";} else { echo "No";}?></span></p>
								<p><span class="name">Verified</span> <span class="dots">:</span> <span class="value"><?php if($user->is_verified=='y') { echo "Yes";} else { echo "No";}?></span></p>
								<p><span class="name">Status</span> <span class="dots">:</span> <span class="value"><?php if($user->status=='9') { echo "Deleted";} else if($user->status=='1') { echo "Active";} else { echo "Suspended";}?></span></p>
								<p><span class="name">Create Date</span> <span class="dots">:</span> <span class="value"><?php echo date("d, M Y", strtotime($user->added_date));?></span></p>	
								<p><span class="name">Last Update</span> <span class="dots">:</span> <span class="value"><?php echo date("d, M Y", strtotime($user->update_date));?></span></p>
								<p><span class="name">Last Update IP</span> <span class="dots">:</span> <span class="value"><?php echo $user->updateip;?></span></p>							
							</div>
						</div>
						<!--end of row-->
						<div class="row preview-card">
							<div class="col-sm-12">	
								<h3>PAN &amp; GST Details</h3>
							</div>
							<div class="col-sm-6">
								<p><span class="name">TDS (Deduct)</span> <span class="dots">:</span> <span class="value"><?php if($user->tds_deduct=='1') { echo "Yes";} else { echo "No";}?></span></p>
								<p><span class="name">TDS Percentage</span> <span class="dots">:</span> <span class="value"><?php echo $user->tds_per;?> %</span></p>
								<p><span class="name">PAN Number</span> <span class="dots">:</span> <span class="value"><?php echo $user->panno;?></span></p>
							</div>
							<div class="col-sm-6">
								<p><span class="name">GST (Deduct)</span> <span class="dots">:</span> <span class="value"><?php if($user->gst_deduct=='1') { echo "Yes";} else { echo "No";}?></span></p>
								<p><span class="name">Hash GST Number</span> <span class="dots">:</span> <span class="value"><?php if($user->has_gst=='1') { echo "Yes";} else { echo "No";}?></span></p>
								<p><span class="name">GSTIN Number</span> <span class="dots">:</span> <span class="value"><?php echo $user->gstin;?></span></p>
								<p><span class="name">GST Type</span> <span class="dots">:</span> <span class="value"><?php echo getGstType($user->gst_type);?></span></p>
							</div>
						</div>
						<!--end of row-->
						<div class="row preview-card">
							<div class="col-sm-12">	
								<h3>Billing Details</h3>
							</div>
							<div class="col-sm-6">
								<p><span class="name">Address</span> <span class="dots">:</span> <span class="value"><?php echo $user->bill_address;?></span></p>
								<p><span class="name">Pincode</span> <span class="dots">:</span> <span class="value"><?php echo $user->bill_pincode;?></span></p>
							</div>
							<div class="col-sm-6">
								<p><span class="name">City</span> <span class="dots">:</span> <span class="value"><?php echo $user->bill_city;?></span></p>
								<p><span class="name">State</span> <span class="dots">:</span> <span class="value"><?php echo $user->bill_state;?></span></p>
							</div>
						</div>
						<!--end of row-->
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-inr"></i> Wallet</h3>
					</div>
					<div class="box-body no-padding">
						<table class="table table-bordered table-striped">
							<tr>
								<td>Balance </td>
								<td width="50%"><i class="fa fa-inr"></i> <b><?php echo round($wallet->balance,2);?></b></td>
							</tr>
							<tr>
								<td>Cutoff </td>
								<td width="50%"><i class="fa fa-inr"></i> <b><?php echo $wallet->cuttoff;?></b></td>
							</tr>
						</table>
					</div>
				</div>	
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-gears"></i> Services</h3>
					</div>
					<div class="box-body no-padding">
						<table class="table table-striped">
							<tr>
								<td>Recharge / Bills </td>
								<td width="25%">
									<?php if($user->is_recharge=='a') { ?>
									<a href="#" class="label label-success"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } else { ?>
									<a href="#" class="label label-danger"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td>Money Transfer </td>
								<td>
									<?php if($user->is_money=='a') { ?>
									<a href="#" class="label label-success"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } else { ?>
									<a href="#" class="label label-danger"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } ?>
								</td>
							</tr>							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> 
<?php include('footer.php'); ?>