<?php
session_start();
include('../config.php');
if(isset($_SESSION['admin'])) {
	header("location:dashboard.php");
	exit();
} else if(isset($_SESSION['staff'])) {
	header("location:../staff/dashboard.php");
	exit();
}
$error = 0;
if(isset($_POST['submit'])) {
	if($_POST['username'] == '' || $_POST['mobile'] == '') {
		$error = 1;		
	} else {
		if($_POST['csrftoken']==$_SESSION['csrf']) {
			$username = mysql_real_escape_string($_POST['username']);
			$mobile = mysql_real_escape_string($_POST['mobile']);		
			$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE username = '".$username."' ");
			if($admin_info) {			
				if($admin_info->status == '1') {
					if($admin_info->mobile == $mobile) {
						$password = generatePassword();	
						$hashpassword = hashPassword($password);			
						$db->execute("UPDATE `apps_admin` SET `password`='".$hashpassword."' WHERE admin_id = '".$admin_info->admin_id."' ");
						mailForgetPassword($admin_info->email, $admin_info->fullname, $password);					
						$error = 5;
													
					} else {
						$error = 4;
					}
					
				} else {
					$error = 3;
				}			
			} else {
				$error = 2;
			}
		} else {
			$error = 1;
		}
	}
}
$csrfToken = csrfToken();
$_SESSION['csrf'] = $csrfToken;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Staff Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!-- Styles -->
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css" id="theme-style">
<link href="../css/stylesheet.css" rel="stylesheet" type="text/css" id="theme-style">
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.validate.js"></script>
</head>
<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-box-header">
			<i class="fa fa-lg fa-lock text-grey"></i>&nbsp; Forget Password
		</div>
		<div class="login-box-body">		
		<?php if($error == 5) { ?>
		<div class="alert alert-success margin-top-15">
			<a class="close" data-dismiss="alert">&times;</a>
			Successfully reset and sent to your registered email address!
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-danger margin-top-15">
			<a class="close" data-dismiss="alert">&times;</a>
			Mobile number not matched!
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger margin-top-15">
			<a class="close" data-dismiss="alert">&times;</a>
			Your account is inactive, Please contact to Support!
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger margin-top-15">
			<a class="close" data-dismiss="alert">&times;</a>
			Username not found!
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger margin-top-15">
			<a class="close" data-dismiss="alert">&times;</a>
			Oops! Some fields are missing
		</div>
		<?php } ?>
		<div class="row">	
			<form action="" method="post">
			<input type="hidden" name="csrftoken" value="<?php echo $csrfToken;?>">				
			<div class="col-sm-12">
				<div class="form-group jrequired">
					<label>Username</label>
					<input type="text" name="username" id="username" class="form-control" placeholder="Username">
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group jrequired">
					<label>Mobile</label>
					<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile">
				</div>
			</div>
			<div class="col-sm-12 margin-top-15">
				<input type="submit" name="submit" value="Forget Password" class="btn btn-primary btn-block btn-flat">
			</div>
			<div class="col-sm-12 margin-top-25">
				<p><a href="login.php">Have password login now?</a></p>
			</div>
			</form>
		</div>
	</div>
</div> 
</body>
</html>