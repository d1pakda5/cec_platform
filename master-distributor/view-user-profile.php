<?php
session_start();
if(!isset($_SESSION['mdistributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
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
	exit();
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
	exit();
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$request_id."' AND mdist_id='".$_SESSION['mdistributor_uid']."' ");
if(!$user) header("location:index.php");

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
<div class="content">
	<div class="container">
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
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title pull-left"><i class="fa fa-pencil-square"></i> Profile</h3>
						<div class="pull-right">
							<form method="post">
							<button type="submit" name="userPin" class="btn btn-sm btn-default">Change Pin</button>
							<button type="submit" name="userPassword" class="btn btn-sm btn-default">Change Password</button>
							<a href="userkyc.php?uid=<?php echo $user->uid;?>" class="btn btn-sm btn-default">KYC</a>
							<a href="edit-user-profile.php?token=<?php echo $token;?>&id=<?php echo $user->user_id;?>" class="btn btn-sm btn-default">Edit</a>
							</form>
						</div>
					</div>
					<div class="box-body min-height-300">
						<div class="row padding-50 form-horizontal">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->fullname;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->company_name;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->mobile;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->phone;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->email;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->address;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->city;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->states;?></p>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">UID :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->uid;?></p>
									</div>
								</div>
								<?php if($user->user_type == '5') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Distributor :</label>
									<div class="col-sm-8 jrequired">
										<?php
										$dist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->dist_id."'");
										?>
										<p class="form-control-static"><?php echo $dist->company_name;?> (<?php echo $user->dist_id;?>)</p>
									</div>
								</div>
								<?php } ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $user->username;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Online Access :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php if($user->is_access=='y') { echo "Yes";} else { echo "No";}?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Verified :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php if($user->is_verified=='y') { echo "Yes";} else { echo "No";}?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static">
											<?php if($user->status=='9') { echo "Deleted";} else if($user->status=='1') { echo "Active";} else { echo "Suspended";}?>
										</p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Create Date :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo date("d-m-Y", strtotime($user->added_date));?></p>
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>