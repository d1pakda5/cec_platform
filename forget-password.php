<?php
session_start();
include('config.php');
if(isset($_SESSION['apiuser'])) {
	header("location:apiuser/index.php");
} else if(isset($_SESSION['mdistributor'])) {
	header("location:master-distributor/index.php");
} else if(isset($_SESSION['distributor'])) {
	header("location:distributor/index.php");
} else if(isset($_SESSION['retailer'])) {
	header("location:retailer/index.php");
}
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {

	if($_POST['username'] == '' || $_POST['mobile'] == '') {
		$error = 1;		
	} else {
		$username = mysql_real_escape_string($_POST['username']);
		$mobile = mysql_real_escape_string($_POST['mobile']);		
		$row = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".$username."' ");
		if($row) {			
			if($row->status == '1') {
				if($row->mobile == $mobile) {
					$password = generatePassword();	
					$hashpassword = hashPassword($password);			
					$db->execute("UPDATE `apps_user` SET `password`='".$hashpassword."' WHERE user_id = '".$row->user_id."' ");
					
					if($row->user_type == '1') {
						mailForgetPassword($row->email, $row->company_name, $password);
					}
					
					if($row->user_type != '1') {
						$message = smsPasswordChange($row->company_name, $password);
						smsSendSingle($row->mobile, $message, 'password');
					}
									
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
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Forget Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!-- Styles -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" id="theme-style">
<link href="css/theme.css" rel="stylesheet" type="text/css" id="theme-style">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.js"></script>
</head>
<body class="hold-transition login-page">
<div class="header-login">
	<div class="container">
		<a href="ErechargeAsia.jar"><img src="images/java.png" /></a>
		<a href="ClickEcharge.apk"><img src="images/android.png" /></a>
	</div>
</div>
<div class="container">
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
			<div class="col-sm-12">
				<div class="form-group jrequired">
					<input type="text" name="username" id="username" class="form-control" placeholder="Username">
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group jrequired">
					<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile">
				</div>
			</div>
			<div class="col-sm-12">
				<input type="submit" name="submit" value="Forget Password" class="btn btn-success btn-block btn-flat">
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