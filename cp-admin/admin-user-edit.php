<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['fullname'] == '' || $_POST['mobile'] == '' || $_POST['email'] == '' || $_POST['status'] == '') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$mobile =  htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);		
		$exists = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id!='".$request_id."' AND ( mobile='".$mobile."' OR email='".$email."' ) ");
		if($exists) {
			$error = 2;
		} else {			
			$db->execute("UPDATE `apps_admin` SET `fullname`='".$fullname."', `email`='".$email."', `mobile`='".$mobile."', `user_level`='".$_POST['user_level']."', `status`='".$_POST['status']."' WHERE admin_id='".$request_id."' ");	
			if($_POST['password'] != '') {
				$password = htmlentities(addslashes($_POST['password']),ENT_QUOTES);
				$hashPassword = hashPassword($password);
				$db->execute("UPDATE `apps_admin` SET `password`='".$hashPassword."' WHERE admin_id = '".$request_id."' ");	
			}
			if($_POST['pin'] != '') {
				$pin = htmlentities(addslashes($_POST['pin']),ENT_QUOTES);
				$hashPin = hashPin($pin);
				$db->execute("UPDATE `apps_admin` SET `pin`='".$hashPin."' WHERE admin_id = '".$request_id."' ");	
			}
			$error = 3;
		}		
	}
}

$admin = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$request_id."' ");
if(!$admin) header("location:admin-user.php");

$meta['title'] = "Admin User - Edit";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#userForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update user?")) {
        form.submit();
      }
		},
	  rules: {
	  	fullname: {
				required: true
			},
			mobile: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10
			},
			email: {
				required:true,
				email: true
			},
			user_level: {
				required: true
			},
			username: {
				required: true
			},
			password: {
				required: false,
				minlength: 6
			},
			pin: {
				required: false,
				minlength: 4,
				maxlength: 4,
				digits: true
			},
			status: {
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
			<div class="page-title">Admin User <small>/ Add</small></div>
			<div class="pull-right">
				<a href="admin-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Duplicate values found!
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
						<h3 class="box-title"><i class="fa fa-plus-square"></i> Create new user</h3>
					</div>
					<form action="" method="post" id="userForm" class="form-horizontal">
					<div class="box-body min-height-300">
						<div class="row padding-50">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">User Type :</label>
									<div class="col-sm-8 jrequired">
										<select name="user_level" id="user_level" class="form-control">
											<option value="">-- Select --</option>
											<option value="u"<?php if($admin->user_level=='u') {?> selected="selected"<?php } ?>>Staff User</option>
											<option value="a"<?php if($admin->user_level=='a') {?> selected="selected"<?php } ?>>Account Manager</option>
										</select>
									</div>
								</div>								
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" readonly="" value="<?php echo $admin->username;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Password :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="password" id="password" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pin :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="pin" id="pin" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">								
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="fullname" id="fullname" value="<?php echo $admin->fullname;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" value="<?php echo $admin->mobile;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" value="<?php echo $admin->email;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<select name="status" id="status" class="form-control">
											<option value=""></option>
											<option value="1" <?php if($admin->status == '1') {?>selected="selected"<?php } ?>>ACTIVE</option>
											<option value="0" <?php if($admin->status == '0') {?>selected="selected"<?php } ?>>INACTIVE</option>
											<option value="9" <?php if($admin->status == '9') {?>selected="selected"<?php } ?>>TRASH</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
									<i class="fa fa-save"></i> Save User
								</button>
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