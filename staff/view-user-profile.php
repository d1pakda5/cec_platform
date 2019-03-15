<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$uid = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".mysql_real_escape_string($_GET['uid'])."' ");
	if($uid) {
		$request_id = $uid->user_id;
	}
}
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['userPin'])) {
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
	$pin = generatePin();
	$hashPin = hashPin($pin);
	$db->execute("UPDATE apps_user SET pin = '".$hashPin."' WHERE user_id = '".$request_id."'");
	$message = smsPinChange($user_info->company_name, $pin);
	smsSendSingle($user_info->mobile, $message, 'pin');	
	if($user_info->email != '') {
		try {
			mailChangePin($user_info->email, $user_info->fullname, $pin);
		} catch(phpmailerException $e) {
			echo $e->errorMessage();
		}
	}
	header("location:view-user-profile.php?id=".$request_id."&error=1");
}

if(isset($_POST['userPassword'])) {
	$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
	$password = generatePassword();
	$hashPassword = hashPassword($password);
	$db->execute("UPDATE apps_user SET password = '".$hashPassword."' WHERE user_id = '".$request_id."'");
	$message = smsPasswordChange($user_info->company_name, $password);
	smsSendSingle($user_info->mobile, $message, 'password');	
	if($user_info->email != '') {
		try {
			mailForgetPassword($user_info->email, $user_info->fullname, $password);
		} catch(phpmailerException $e) {
			echo $e->errorMessage();
		}
	}
	header("location:view-user-profile.php?id=".$request_id."&error=2");
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
if(!$user) header("location:index.php");

if($user->user_type == '1') {	
	if(empty($sP['api_user']['view'])) {
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '3') {
	if(empty($sP['md_user']['view'])) { 
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '4') {
	if(empty($sP['ds_user']['view'])) { 
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '5') {
	if(empty($sP['rt_user']['view'])) { 
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '6') {
	if(empty($sP['rt_user']['view'])) { 
		include('permission.php');
		exit(); 
	}
} else {
	include('permission.php');
	exit();
}

$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$user->uid."' ");

$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#profileForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update profile?")) {
        form.submit();
      }
		},
	  rules: {
	  	fullname: {
				required: true
			},
			email: {
				required:true
			},
			mobile: {
				required: true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<style>
.list-buttons .btn {
	margin-bottom:10px;
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
		<?php if($error == 2) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Password has been reset successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
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
					<div class="box-body min-height-300">
						<div class="row padding-50 form-horizontal">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<p class="form-control-static"><?php echo $user->fullname;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<p class="form-control-static"><?php echo $user->company_name;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<p class="form-control-static"><?php echo $user->mobile;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<p class="form-control-static"><?php echo $user->phone;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<p class="form-control-static"><?php echo $user->email;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<p class="form-control-static"><?php echo $user->address;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<p class="form-control-static"><?php echo $user->city;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<p class="form-control-static"><?php echo $user->states;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode :</label>
									<p class="form-control-static"><?php echo $user->pincode;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">PAN Number :</label>
									<p class="form-control-static"><?php echo $user->panno;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Aadhar Number :</label>
									<p class="form-control-static"><?php echo $user->aadharno;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">GSTIN ID :</label>
									<p class="form-control-static"><?php echo $user->gstin;?></p>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">UID :</label>
									<p class="form-control-static"><?php echo $user->uid;?></p>
								</div>
								<?php if($user->user_type == '4' || $user->user_type == '5') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<?php $mdist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->mdist_id."'"); ?>
									<p class="form-control-static"><?php echo $mdist->company_name;?> (<?php echo $user->mdist_id;?>)</p>
								</div>
								<?php } ?>
								<?php if($user->user_type == '5') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Distributor :</label>
									<?php $dist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->dist_id."'"); ?>
									<p class="form-control-static"><?php echo $dist->company_name;?> (<?php echo $user->dist_id;?>)</p>
								</div>
								<?php } ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<p class="form-control-static"><?php echo $user->username;?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Online Access :</label>
									<p class="form-control-static"><?php if($user->is_access=='y') { echo "Yes";} else { echo "No";}?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Verified :</label>
									<p class="form-control-static"><?php if($user->is_verified=='y') { echo "Yes";} else { echo "No";}?></p>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<?php if($user->status=='9') { echo "Deleted";} else if($user->status=='1') { echo "Active";} else { echo "Suspended";}?>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Create Date :</label>
									<p class="form-control-static"><?php echo date("d-m-Y", strtotime($user->added_date));?></p>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
				</div>
			</div>
			<div class="col-sm-3">	
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
									<a onClick="actionRow('<?php echo $user->user_id;?>', 'i', 'is_recharge');" href="#" class="label label-success"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } else { ?>
									<a onClick="actionRow('<?php echo $user->user_id;?>', 'a', 'is_recharge');" href="#" class="label label-danger"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td>Money Transfer </td>
								<td>
									<?php if($user->is_money=='a') { ?>
									<a onClick="actionRow('<?php echo $user->user_id;?>', 'i', 'is_money');" href="#" class="label label-success"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } else { ?>
									<a onClick="actionRow('<?php echo $user->user_id;?>', 'a', 'is_money');" href="#" class="label label-danger"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } ?>
								</td>
							</tr>							
						</table>
					</div>
				</div>
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-gears"></i> Profile Functions</h3>
					</div>
					<div class="box-body list-buttons">
						<form method="post">
							<a href="edit-user-profile.php?id=<?php echo $user->user_id;?>" class="btn btn-default btn-block">Edit Profile</a>
							<?php if($kyc) { ?>
							<a href="kyc.php?uid=<?php echo $user->uid;?>" class="btn btn-default btn-block">KYC</a>
							<?php } else { ?>
							<a href="kyc-add.php?uid=<?php echo $user->uid;?>&action=add" class="btn btn-danger btn-block">Add KYC</a>
							<?php } ?>
							<?php if($user->user_type == '1') {?>								
								<a href="api-user-setting.php?id=<?php echo $user->user_id;?>" class="btn btn-default btn-block">API Settings</a>
							<?php } ?>
							<button type="submit" name="userPin" class="btn btn-default btn-block">Change Pin</button>
							<button type="submit" name="userPassword" class="btn btn-default btn-block">Change Password</button>
							<?php if($user->user_type == '4') {?>								
							<a href="move-distributor.php?id=<?php echo $user->user_id;?>" class="btn btn-default btn-block">Move Distributor</a>
							<?php } else if($user->user_type == '5'){ ?>
							<a href="move-retailer.php?id=<?php echo $user->user_id;?>" class="btn btn-default btn-block">Move Retailer</a>
							<?php } ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>