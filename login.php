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
					$_SESSION['token'] = hashToken($row->user_id.$last_login_id);
					if($row->user_type == '1') {					
						$_SESSION['apiuser'] = $row->user_id;
						$_SESSION['apiuser_uid'] = $row->uid;
						$_SESSION['apiuser_name'] = $row->fullname;
						$_SESSION['api_kyc'] = $row->is_kyc;
						$_SESSION['ap_login_id'] = $last_login_id;
						header("location:apiuser/index.php");
					} else if($row->user_type == '3') {					
						$_SESSION['mdistributor'] = $row->user_id;
						$_SESSION['mdistributor_uid'] = $row->uid;
						$_SESSION['mdistributor_name'] = $row->fullname;
						$_SESSION['mdistributor_kyc'] = $row->is_kyc;
						$_SESSION['md_login_id'] = $last_login_id;
						header("location:master-distributor/index.php");
					} else if($row->user_type == '4') {					
						$_SESSION['distributor'] = $row->user_id;
						$_SESSION['distributor_uid'] = $row->uid;
						$_SESSION['distributor_name'] = $row->fullname;
						$_SESSION['distributor_kyc'] = $row->is_kyc;
						$_SESSION['ds_login_id'] = $last_login_id;
						header("location:distributor/index.php");
					} else if($row->user_type == '5') {					
						$_SESSION['retailer'] = $row->user_id;
						$_SESSION['retailer_uid'] = $row->uid;
						$_SESSION['retailer_name'] = $row->fullname;
						$_SESSION['retailer_kyc'] = $row->is_kyc;
						$_SESSION['rt_login_id'] = $last_login_id;
						header("location:retailer/index.php");
					} else if($row->user_type == '6') {					
						$_SESSION['retailer'] = $row->user_id;
						$_SESSION['retailer_uid'] = $row->uid;
						$_SESSION['retailer_name'] = $row->fullname;
						$_SESSION['retailer_kyc'] = $row->is_kyc;
						$_SESSION['rt_login_id'] = $last_login_id;
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
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<style>
	.imageRotateHorizontal{
    -moz-animation: spinHorizontal 1.8s infinite linear;
    -o-animation: spinHorizontal 1.8s infinite linear;    
    -webkit-animation: spinHorizontal 1.8s infinite linear;
    animation: spinHorizontal 1.8s infinite linear;
}

@keyframes spinHorizontal {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(360deg); }
}
.fullscreen-bg {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    overflow: hidden;
    z-index: -100;
}

.fullscreen-bg__video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    /*height: 100%;*/
}

</style>
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
<body class="">
<!--	<video autoplay muted loop id="myVideo" class="fullscreen-bg__video ">
				  <source src="images/holi.mp4" type="video/mp4">
				</video>-->
	<div class="limiter">
		<div class="container-login100">
			<div class="container" style="text-align: right;">
				<a href="sms-format.php"><img src="images/sms-format.png" /></a>
				<a href="ErechargeAsia.jar"><img src="images/java.png" /></a>
				<a href="ClickEcharge.apk"><img src="images/android.png" /></a>
			</div>
			<div class="wrap-login100">
				
				
				<div class="col-md-12" style="text-align: center; padding-bottom: 30px">
					<img src="images/logo-ec.png" alt="IMG">

				</div>
			<div class="col-md-12 login-box-body">		
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
		</div>
				<div class="imageRotateHorizontal login100-pic" data-tilt>
					<img src="images/512_logo.png" alt="IMG">
				</div>

				<form class="login100-form validate-form" style="z-index: 1000" action="" method="post" id="loginFrm">
					<span class="login100-form-title" style="z-index: 100">
						Partner Login
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="username" placeholder="Username">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<input type="submit" name="submit" value="Sign In" class="login100-form-btn">
						
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="forget-password.php">
							Username / Password?
						</a>
					</div>

					
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5748343406abb9034a415aa3/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

</body>
</html>