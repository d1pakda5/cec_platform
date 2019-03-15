<?php
session_start();
include("config.php");

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
	if($_POST['username']=='' || $_POST['password']=='') {
		$error = 1;
	} else {
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);		
		$row = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".$username."' ");
		if($row) {
			if($row->status == '1') {			
				$hashPassword = hashPassword($password);
				if($row->password == $hashPassword) {
					
					$useragent = $_SERVER["HTTP_USER_AGENT"];		
					$ip = $_SERVER['REMOTE_ADDR'];		
					$hostaddress = gethostbyaddr($ip);		
					if (preg_match("|MSIE ([0-9].[0-9]{1,2})|",$useragent,$matched)) {		
						$browser_version=$matched[1];		
						$browser = "IE";		
					} elseif (preg_match( "|Opera ([0-9].[0-9]{1,2})|",$useragent,$matched)) {		
						$browser_version=$matched[1];		
						$browser = "Opera";		
					} elseif(preg_match("|Firefox/([0-9\.]+)|",$useragent,$matched)) {		
						$browser_version=$matched[1];		
						$browser = "Firefox";		
					} elseif(preg_match("|Safari/([0-9\.]+)|",$useragent,$matched)) {		
						$browser_version=$matched[1];		
						$browser = "Safari";		
					} else {
						$browser_version = 0;		
						$browser= "other";	
					}		
					if (strstr($useragent,"win")) {		
						$os='Win';		
					} else if (strstr($useragent,"Mac")) {		
						$os='Mac';		
					} else if (strstr($useragent,"Linux")) {		
						$os='Linux';		
					} else if (strstr($useragent,"Unix")) {		
						$os='Unix';		
					} else {		
						$os='Other';		
					}
						
					$db->execute("INSERT INTO `activity_login`(`login_id`, `is_admin`, `user_type`, `username`, `ip`, `host_address`, `browser`, `login_time`, `is_online`) VALUES ('', 'n', '".$row->user_type."', '".$username."', '".$ip."', '".$hostaddress."', '".$useragent."', NOW(), 'y')	");
					$last_login_id = $db->lastInsertedId();			
					$WhiteLabel = $db->queryUniqueObject("SELECT * FROM website_profile WHERE website_uid = '".$row->mdist_id."' OR website_uid = '".$row->uid."' ");
					$_SESSION['token'] = hashToken($row->user_id.$last_login_id);
					if($row->user_type == '1') {					
						$_SESSION['apiuser'] = $row->user_id;
						$_SESSION['apiuser_uid'] = $row->uid;
						$_SESSION['apiuser_name'] = $row->fullname;
						$_SESSION['ap_login_id'] = $last_login_id;
						header("location:apiuser/index.php");
					} else if($row->user_type == '3') {					
						$_SESSION['mdistributor'] = $row->user_id;
						$_SESSION['mdistributor_uid'] = $row->uid;
						$_SESSION['mdistributor_name'] = $row->fullname;
						$_SESSION['md_login_id'] = $last_login_id;
						$_SESSION['whitelabel'] = $WhiteLabel->website_uid;
						$_SESSION['loginpage'] = $WhiteLabel->login_page;
						header("location:master-distributor/index.php");
					} else if($row->user_type == '4') {					
						$_SESSION['distributor'] = $row->user_id;
						$_SESSION['distributor_uid'] = $row->uid;
						$_SESSION['distributor_name'] = $row->fullname;
						$_SESSION['ds_login_id'] = $last_login_id;
						$_SESSION['whitelabel'] = $WhiteLabel->website_uid;
						$_SESSION['loginpage'] = $WhiteLabel->login_page;
						header("location:distributor/index.php");
					} else if($row->user_type == '5') {					
						$_SESSION['retailer'] = $row->user_id;
						$_SESSION['retailer_uid'] = $row->uid;
						$_SESSION['retailer_name'] = $row->fullname;
						$_SESSION['rt_login_id'] = $last_login_id;
						$_SESSION['whitelabel'] = $WhiteLabel->website_uid;
						$_SESSION['loginpage'] = $WhiteLabel->login_page;
						header("location:retailer/index.php");
					} else if($row->user_type == '6') {					
						$_SESSION['retailer'] = $row->user_id;
						$_SESSION['retailer_uid'] = $row->uid;
						$_SESSION['retailer_name'] = $row->fullname;
						$_SESSION['rt_login_id'] = $last_login_id;
						$_SESSION['whitelabel'] = $WhiteLabel->website_uid;
						$_SESSION['loginpage'] = $WhiteLabel->login_page;
						header("location:direct-retailer/index.php");
					}			
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">	
<title>Login</title>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" id="theme-style">
<link rel="stylesheet" href="css/theme.css" type="text/css" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.js"></script>
<script>
$(document).ready(function(){	
	$('#loginFrm').validate({
		rules: {
			username: {
				required: true
			},
			password: {
				required: true
			}
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<style>
.btn-and:hover {
	margin-top:-5px;
}
.btn-java:hover {
	margin-top:-5px;
}
</style>
</head>
<body class="hold-transition tushar_login-page">
<div class="header-login">
	<div class="container">
	    <img class="tushar_logo" src="images/vretail_logo.jpg">
		<a href="sms-format.php"><img src="images/sms-format.png" /></a>
	</div>
</div>
<div class="container">
	<div class="login-box">
		<div class="login-box-header">
			<i class="fa fa-lg fa-lock text-grey"></i> Sign in to start your session
		</div>
		<div class="login-box-body">		
			<?php if($error == 5) { ?>
			<div class="alert alert-warning">
				<a class="close" data-dismiss="alert">&times;</a>
				Invalid credit		</div>
			<?php } else if($error == 4) { ?>
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">&times;</a>
				Password is invalid!</div>
			<?php } else if($error == 3) { ?>
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">&times;</a>
				Your account is deactive, Please contact to Support! </div>
			<?php } else if($error == 2) { ?>
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">&times;</a>
				Invalid user details </div>
			<?php } else if($error == 1) { ?>
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">&times;</a>
				Oops! Something went wrong please try again.</div>
			<?php } ?>	
			<div class="row">	
				<form action="" method="post" id="loginFrm">
					<div class="col-sm-12">
						<div class="form-group jrequired">
							<input type="text" name="username" class="form-control" placeholder="Username">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group jrequired">
							<input type="password" name="password" class="form-control" placeholder="Password">
						</div>
					</div>
					<div class="col-sm-12">
						<input type="submit" name="submit" value="Sign In" class="btn btn-success btn-block btn-flat">
					</div>
					<div class="col-sm-12 margin-top-25">
						<p><a href="forget-password.php">Forgot Password ?</a></p>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

</body>
</html>