<?php
session_start();
if(!isset($_SESSION['mdistributor'])) header("location:login.php");
include('../config.php');
include('common.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['fullname'] == '' || $_POST['company_name'] == '' || $_POST['mobile'] == '' || $_POST['email'] == '' || $_POST['address'] == '' || $_POST['state'] == '' || $_POST['uid'] == '' || $_POST['status'] == '' || $_POST['password'] == '') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$company_name =  htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
		$mobile =  htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$phone =  htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$address =  htmlentities(addslashes($_POST['address']),ENT_QUOTES);
		$city =  htmlentities(addslashes($_POST['city']),ENT_QUOTES);
		$uid =  htmlentities(addslashes($_POST['uid']),ENT_QUOTES);
		$cuttoff =  htmlentities(addslashes($_POST['cuttoff']),ENT_QUOTES);
		$is_access = isset($_POST['is_access']) ? $_POST['is_access'] : "n";
		$is_verified = isset($_POST['is_verified']) ? $_POST['is_verified'] : "n";
		$password = htmlentities(addslashes($_POST['password']),ENT_QUOTES);
		$hashPassword = hashPassword($password);
		$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' OR mobile = '".$mobile."' OR email = '".$email."' OR username = '".$mobile."' ");
		if($exists) {
			$error = 2;
		} else {			
			$db->execute("INSERT INTO `apps_user`(`user_id`, `uid`, `user_type`, `mdist_id`, `dist_id`, `fullname`, `company_name`, `mobile`, `email`, `address`, `city`, `states`, `phone`, `username`, `password`, `pin`, `is_access`, `is_verified`, `is_kyc`, `status`, `added_date`) VALUES ('', '".$uid."', '4', '".$_SESSION['mdistributor_uid']."', '0', '".$fullname."', '".$company_name."', '".$mobile."', '".$email."', '".$address."', '".$city."', '".$_POST['state']."', '".$phone."', '".$mobile."', '".$hashPassword."', '', '".$is_access."', '".$is_verified."', '0', '".$_POST['status']."', NOW())");	
			$user_id = $db->lastInsertedId();				
			$db->execute("INSERT INTO `apps_wallet`(`wallet_id`, `user_id`, `uid`, `balance`, `cuttoff`, `is_locked`, `update_time`) VALUES ('', '".$user_id."', '".$uid."', '0', '".$cuttoff."', '0', NOW())");
			$websitename = getWebsiteName($_SESSION['mdistributor_uid']);
			$message = smsUserActivation($websitename, $mobile, $password, date("d-m-Y"));
			smsSendSingle($mobile, $message, 'registration');
			if($email != '') {
				mailNewClient($fullname, $company_name, $mobile, $email, $mobile, $password, $websitename);
			}
			$error = 3;
		}		
	}
}
$meta['title'] = "Distributor - Add";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#mobile").keyup(function() {
		jQuery("#username").val(jQuery(this).val());
	});
	jQuery.validator.addMethod("mobileCheck", function(value, element) {
		var isSuccess = false;
		jQuery.ajax({
			url	: "../ajax/check-mobile.php",
			type	: "POST",
			data	: "mobile="+value,
			async	: false,
			success	: function(data) {
				if(data == 'false') { 
					isSuccess = true;
				} else {
					isSuccess = false;
				}
			}
		});
		return isSuccess;
	}, "Mobile is used by another user");
	
	jQuery.validator.addMethod("emailCheck", function(value, element) {
		var isSuccess = false;
		jQuery.ajax({
			url	: "../ajax/check-email.php",
			type	: "POST",
			data	: "email="+value,
			async	: false,
			success	: function(data) {
				if(data == 'false') { 
					isSuccess = true;
				} else {
					isSuccess = false;
				}
			}
		});
		return isSuccess;
	}, "Email is used by another user");
	
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
			company_name: {
				required: true
			},
			mobile: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10,
				mobileCheck: true
			},
			email: {
				required:true,
				email: true,
				emailCheck: true
			},
			address: {
				required: true
			},
			city: {
				required: true
			},
			state: {
				required: true
			},
			uid: {
				required: true
			},
			password: {
				required: true,
				minlength: 6
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
			<div class="page-title">Distributor <small>/ Add</small></div>
			<div class="pull-right">
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
						<h3 class="box-title"><i class="fa fa-plus-square"></i> Create new Distributor</h3>
					</div>
					<form action="" method="post" id="userForm" class="form-horizontal">
					<div class="box-body min-height-300">
						<div class="row padding-50">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="fullname" id="fullname" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="company_name" id="company_name" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="phone" id="phone" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="address" id="address" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="city" id="city" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<select name="state" id="state" class="form-control">
											<option value=""></option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>"><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">UID :</label>
									<div class="col-sm-8 jrequired">
									<input type="text" name="uid" id="uid" readonly="" value="<?php echo getUserUID();?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" readonly="" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Password :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="password" id="password" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Cutoff :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="cuttoff" id="cuttoff" readonly="" value="<?php echo getUserCuttOff('4');?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Online Access :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_access" id="is_access" value="y" checked="checked"></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Verified :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_verified" id="is_verified" value="y"></label>
										</div>
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
								<div class="form-group">
									<label class="col-sm-4 control-label">Create Date :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo date("d-m-Y");?></p>
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
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