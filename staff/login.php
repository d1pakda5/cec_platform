<?php
session_start();
include("../config.php");
if(isset($_SESSION['staff'])) {
	header("location:dashboard.php");
	exit();
}
$error=0;
if(isset($_POST['submit'])) {
	if($_POST['username']=='' || $_POST['password']=='') {
		$error = 1;
	} else {
		if($_POST['csrftoken']==$_SESSION['csrf']) {
			$username = mysql_real_escape_string($_POST['username']);
			$password = mysql_real_escape_string($_POST['password']);		
			$row = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE username = '".$username."' ");
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
							
						$db->execute("INSERT INTO `activity_login`(`login_id`, `is_admin`, `user_type`, `username`, `ip`, `host_address`, `browser`, `login_time`, `is_online`) VALUES ('', 'y', '0', '".$username."', '".$ip."', '".$hostaddress."', '".$useragent."', NOW(), 'y')	");
						$_SESSION['lastloginid'] = $db->lastInsertedId();
						if($row->user_level == 's') {
							$_SESSION['admin'] = $row->admin_id;
							$_SESSION['admin_name'] = $row->username;
							$_SESSION['admin_level'] = $row->user_level;
							$db->execute("UPDATE apps_admin SET last_login_time=NOW(), last_login_ip='".$ip."' WHERE admin_id='".$row->admin_id."' ");					
							header("location:../cp-admin/dashboard.php");
							exit();
						}
						elseif($row->user_level=='a') {
							$_SESSION['accmgr'] = $row->admin_id;
							$_SESSION['accmgr_name'] = $row->username;
							$_SESSION['accmgr_level'] = $row->user_level;
							$db->execute("UPDATE apps_admin SET last_login_time=NOW(), last_login_ip='".$ip."' WHERE admin_id='".$row->admin_id."' ");					
							header("location:../crm/dashboard.php");
							exit();
						} 
						else {
							$_SESSION['staff'] = $row->admin_id;
							$_SESSION['staff_name'] = $row->username;
							$_SESSION['staff_level'] = $row->user_level;
							$db->execute("UPDATE apps_admin SET last_login_time=NOW(), last_login_ip='".$ip."' WHERE admin_id='".$row->admin_id."' ");					
							header("location:dashboard.php");
							exit();
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
		} else {
			$error = 1;
		}
	}
}
$csrfToken = csrfToken();
$_SESSION['csrf'] = $csrfToken;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Staff Login</title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/font-awesome.min.css" type="text/css" id="theme-style">
<link rel="stylesheet" href="../css/stylesheet.css" type="text/css" />
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.validate.js"></script>
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
</head>
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-box-header"><i class="fa fa-lg fa-lock text-grey"></i>&nbsp; Sign in to start your session</div>
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
				<input type="hidden" name="csrftoken" value="<?php echo $csrfToken;?>">
				<div class="col-sm-12">
					<div class="form-group jrequired">
						<label>Username</label>
						<input type="text" name="username" class="form-control" placeholder="Username">
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group jrequired">
						<label>Password</label>
						<input type="password" name="password" class="form-control" placeholder="Password">
					</div>
				</div>
				<div class="col-sm-12 margin-top-15">
					<input type="submit" name="submit" value="Sign In" class="btn btn-primary btn-block btn-flat">
				</div>
				<div class="col-sm-12 margin-top-25">
					<p><a href="forget-password.php">Forgot Password ?</a></p>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>