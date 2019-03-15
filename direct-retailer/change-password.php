<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(!isset($_GET['token']) || $_GET['token'] != $token) {
	exit("Token not match");
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['newpassword'] == '' || $_POST['password'] == '') {
		$error = 1;		
	} else {	
		$password =  hashPassword(htmlentities(addslashes($_POST['password']),ENT_QUOTES));
		$db->execute("UPDATE `apps_user` SET `password` = '".$password."' WHERE `user_id` = '".$_SESSION['retailer']."' ");
		$message = smsPasswordChange($aRetailer->company_name, $_POST['password']);
		smsSendSingle($aRetailer->mobile, $message, 'password');
		if($aRetailer->email != '') {	
			mailChangePassword($aRetailer->email, $aRetailer->fullname, $_POST['password']);
		}
		$error = 3;
		session_destroy();
		header("refresh:5;url=index.php");		
	}
}
$meta['title'] = "Change Password";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#passwordForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update password?")) {
        form.submit();
      }
		},
	  rules: {
	  	newpassword: {
				required: true,
				minlength: 5
			},
			password: {
				required:true,
				equalTo: "#newpassword"
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
			<div class="page-title">My Account <small>/ Change Password</small></div>
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
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Change Password</h3>
					</div>
					<form action="" method="post" id="passwordForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-5 control-label">New Password<i class="text-red">*</i> :</label>
									<div class="col-sm-7 jrequired">
										<input type="password" name="newpassword" id="newpassword" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">Confirm Password<i class="text-red">*</i> :</label>
									<div class="col-sm-7 jrequired">
										<input type="password" name="password" id="password" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Save Changes
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Password Help</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="col-md-12">
							Password length between 8 to 20 Character<br />Atleast 1 Small Alphabate (a-z)<br />Atleast 1 Capital Alphabate (A-Z)<br />Atleast 1 Number (0-9)
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>