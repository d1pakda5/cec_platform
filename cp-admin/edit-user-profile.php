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
	if($_POST['user_type']=='5') {		
		if($_POST['fullname']=='' || $_POST['company_name']=='' || $_POST['address']=='' || $_POST['state']=='' || $_POST['status']=='') {
			$error = 1;		
		} else {		
			$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
			$company_name =  htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
			$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
			$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);
			$address =  htmlentities(addslashes($_POST['address']),ENT_QUOTES);
			$city =  htmlentities(addslashes($_POST['city']),ENT_QUOTES);
			$cuttoff =  htmlentities(addslashes($_POST['cuttoff']),ENT_QUOTES);
			$is_access = isset($_POST['is_access']) ? $_POST['is_access'] : "n";
			$is_verified = isset($_POST['is_verified']) ? $_POST['is_verified'] : "n";
			$is_duplicate = false;
			if($_POST['email'] != '') {
				$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE email='".$email."' AND user_id!='".$request_id."' ");
				if($exists) {
					$is_duplicate = true;
				}
			}			
			if($is_duplicate) {
				$error = 2;
			} else {
				$db->execute("UPDATE `apps_user` SET `fullname`='".$fullname."', `company_name`='".$company_name."', `email`='".$email."', `address`='".$address."', `city`='".$city."', `states`='".$_POST['state']."', `phone`='".$phone."', `is_access`='".$is_access."', `is_verified`='".$is_verified."', `status`='".$_POST['status']."' WHERE user_id = '".$request_id."' ");			
				$db->execute("UPDATE `apps_wallet` SET `cuttoff`='".$cuttoff."' WHERE user_id = '".$request_id."' ");
				$error = 3;
			}
		}	
		
	} else {
		if($_POST['fullname']=='' || $_POST['company_name']=='' || $_POST['email']=='' || $_POST['address']=='' || $_POST['state']=='' || $_POST['status']=='') {
			$error = 1;		
		} else {		
			$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
			$company_name =  htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
			$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
			$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);
			$address =  htmlentities(addslashes($_POST['address']),ENT_QUOTES);
			$city =  htmlentities(addslashes($_POST['city']),ENT_QUOTES);
			$cuttoff =  htmlentities(addslashes($_POST['cuttoff']),ENT_QUOTES);
			$is_access = isset($_POST['is_access']) ? $_POST['is_access'] : "n";
			$is_verified = isset($_POST['is_verified']) ? $_POST['is_verified'] : "n";
			
			$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE email='".$email."' AND user_id!='".$request_id."' ");
			if($exists) {
				$error = 2;
			} else {			
				$db->execute("UPDATE `apps_user` SET `fullname`='".$fullname."', `company_name`='".$company_name."', `email`='".$email."', `address`='".$address."', `city`='".$city."', `states`='".$_POST['state']."', `phone`='".$phone."', `is_access`='".$is_access."', `is_verified`='".$is_verified."', `status`='".$_POST['status']."' WHERE user_id = '".$request_id."' ");			
				$db->execute("UPDATE `apps_wallet` SET `cuttoff`='".$cuttoff."' WHERE user_id = '".$request_id."' ");
				$error = 3;
			}		
		}	
	}	
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$request_id."' ");
if(!$user) header("location:index.php");
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id='".$user->user_id."' AND uid='".$user->uid."' ");

$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#mdist_id").change(function(){
		jQuery.ajax({ 
			url: "ajax/list-distributor.php",
			type: "POST",
			data: "id="+jQuery(this).val(),
			async: false,
			success: function(data) {
				jQuery("select#dist_id").html(data);
			}
		});
	});
	jQuery.validator.addMethod("emailCheck", function(value, element) {
		var isSuccess = false;
		if(value == '') {
			return true;
		} else {
			jQuery.ajax({
				url	: "ajax/check-email.php",
				type	: "POST",
				data	: "email="+value+"&id="+<?php echo $user->user_id;?>,
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
		}
	}, "Email is used by another user");	
	
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
			company_name: {
				required: true
			},
			email: {
				required:function(element){
					return jQuery("#user_type").val() != '5';
				},
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
			<div class="page-title"><?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<?php if($user->user_type == '1') {?>
				<a href="api-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '3') {?>
				<a href="master-distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '4') {?>
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '5') {?>
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } ?>
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
			<i class="fa fa-times"></i> Found duplicate values!
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Update Profile</h3>
					</div>
					<form action="" method="post" id="userForm" class="form-horizontal">
					<input type="hidden" name="user_type" id="user_type" value="<?php echo $user->user_type;?>">
					<div class="box-body min-height-300">
						<div class="row padding-50">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="fullname" id="fullname" value="<?php echo $user->fullname;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="company_name" id="company_name" value="<?php echo $user->company_name;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" disabled="disabled" value="<?php echo $user->mobile;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="phone" id="phone" value="<?php echo $user->phone;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" value="<?php echo $user->email;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="address" id="address" class="form-control"><?php echo $user->address;?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="city" id="city" value="<?php echo $user->city;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<select name="state" id="state" class="form-control">
											<option value=""></option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>" <?php if($user->states == $rlt->states) {?>selected="selected"<?php } ?>><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">UID :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="uid" id="uid" disabled="disabled" value="<?php echo $user->uid;?>" class="form-control">
									</div>
								</div>
								
								<?php if($user->user_type == '4') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<div class="col-sm-8 jrequired">
										<?php
										$mdist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->mdist_id."'");
										?>
										<input type="text" name="mdist_id" id="mdist_id" disabled="disabled" value="<?php echo $mdist->company_name;?> (<?php echo $user->mdist_id;?>)" class="form-control">
									</div>
								</div>
								<?php } ?>
								
								<?php if($user->user_type == '5') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<div class="col-sm-8 jrequired">
										<select name="mdist_id" id="mdist_id" class="form-control">
											<option value=""></option>
											<?php
											$qry = $db->query("SELECT * FROM apps_user WHERE user_type='3' AND status='1' ORDER BY company_name ASC ");
											while($rst = $db->fetchNextObject($qry)) {?>
											<option value="<?php echo $rst->uid;?>"<?php if($rst->uid==$user->mdist_id) {?> selected="selected"<?php } ?>><?php echo $rst->company_name;?> (<?php echo $rst->uid;?>)</option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Distributor :</label>
									<div class="col-sm-8 jrequired">
										<select name="dist_id" id="dist_id" class="form-control">
											<option value=""></option>
											<?php
											$qry = $db->query("SELECT * FROM apps_user WHERE user_type='4' AND status='1' ORDER BY company_name ASC ");
											while($rst = $db->fetchNextObject($qry)) {?>
											<option value="<?php echo $rst->uid;?>"<?php if($rst->uid==$user->dist_id) {?> selected="selected"<?php } ?>><?php echo $rst->company_name;?> (<?php echo $rst->uid;?>)</option>
											<?php } ?>
										</select>
									</div>
								</div>
								<?php } ?>
								
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" disabled="disabled" value="<?php echo $user->username;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Cutoff :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="cuttoff" id="cuttoff" value="<?php echo $wallet->cuttoff;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Online Access :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_access" id="is_access" value="y" <?php if($user->is_access=='y') {?>checked="checked"<?php } ?>></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Verified :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_verified" id="is_verified" value="y" <?php if($user->is_verified=='y') {?>checked="checked"<?php } ?>></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<select name="status" id="status" class="form-control">
											<option value=""></option>
											<option value="1" <?php if($user->status == '1') {?>selected="selected"<?php } ?>>ACTIVE</option>
											<option value="0" <?php if($user->status == '0') {?>selected="selected"<?php } ?>>INACTIVE</option>
											<option value="9" <?php if($user->status == '9') {?>selected="selected"<?php } ?>>TRASH</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Create Date :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo date("d-m-Y", strtotime($user->added_date));?></p>
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
									<i class="fa fa-save"></i> Update
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