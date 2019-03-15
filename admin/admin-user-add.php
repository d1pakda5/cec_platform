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
	if($_POST['fullname'] == '' || $_POST['mobile'] == '' || $_POST['email'] == '' || $_POST['status'] == '' || $_POST['username'] == '' || $_POST['password'] == '' || $_POST['pin'] == '') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$mobile =  htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$username = htmlentities(addslashes($_POST['username']),ENT_QUOTES);
		$password = htmlentities(addslashes($_POST['password']),ENT_QUOTES);
		$hashPassword = hashPassword($password);
		$pin = htmlentities(addslashes($_POST['pin']),ENT_QUOTES);
		$hashPin = hashPin($pin);
		$exists = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE username = '".$username."' OR mobile = '".$mobile."' OR email = '".$email."' ");
		if($exists) {
			$error = 2;
		} else {			
			$db->execute("INSERT INTO `apps_admin`(`admin_id`, `username`, `password`, `pin`, `fullname`, `email`, `mobile`, `user_level`, `added_date`, `last_login_time`, `last_login_ip`, `status`) VALUES ('', '".$username."', '".$hashPassword."', '".$hashPin."', '".$fullname."', '".$email."', '".$mobile."', 'u', NOW(), '', '', '".$_POST['status']."')");	
			if($email != '') {
				mailNewAdmin($email, $fullname, $mobile, $username, $password, $pin, SITENAME);
			}
			$error = 3;
		}		
	}
}
$meta['title'] = "Admin User - Add";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#userForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add new user?")) {
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
			username: {
				required: true
			},
			password: {
				required: true,
				minlength: 6
			},
			pin: {
				required: true,
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
			<i class="fa fa-check"></i> Created successfully!
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
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" class="form-control">
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
										<input type="text" name="fullname" id="fullname" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<select name="status" id="status" class="form-control">
											<option value=""></option>
											<option value="1">ACTIVE</option>
											<option value="0">INACTIVE</option>
											<option value="9">TRASH</option>
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