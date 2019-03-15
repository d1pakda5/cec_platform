<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['userPin'])) {
	$error = 1;
}

if(isset($_POST['userPassword'])) {
	$error = 1;
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
if(!$user) header("location:master-distributor.php");

$meta['title'] = "Master Distributor - ".$user->company_name;
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
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">API User <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some fields are empty!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Profile</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="row text-right">
							<div class="col-sm-12">
								<form method="post">								
								<a href="api-user-setting.php?id=<?php echo $user->user_id;?>" class="btn btn-default">API Settings</a>
								<button type="submit" name="userPin" class="btn btn-default">Change Pin</button>
								<button type="submit" name="userPassword" class="btn btn-default">Change Password</button>
								<a href="user-kyc.php?id=<?php echo $user->user_id;?>" class="btn btn-default">KYC</a>
								<a href="api-user-edit.php?id=<?php echo $user->user_id;?>" class="btn btn-default">Edit</a>
								</form>
							</div>
						</div>
						<!--end of row-->		
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