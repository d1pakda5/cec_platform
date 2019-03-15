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
	if($_POST['fullname'] == '' || $_POST['mobile'] == '' || $_POST['email'] == '') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$email = htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$db->execute("UPDATE `apps_admin` SET `fullname` = '".$fullname."', `mobile` = '".$mobile."', `email` = '".$email."' WHERE `admin_id` = '".$_SESSION['admin']."' ");
		$error = 3;
	}
}
$profile = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['admin']."' ");
$meta['title'] = "Profile";
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
			<div class="page-title">My Account <small>/ Profile</small></div>
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
					<form action="" method="post" id="profileForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="fullname" id="fullname" value="<?php echo $profile->fullname;?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" value="<?php echo $profile->mobile;?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" value="<?php echo $profile->email;?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" readonly="" value="<?php echo $profile->username;?>" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Profile
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
