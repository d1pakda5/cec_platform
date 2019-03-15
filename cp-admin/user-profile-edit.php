<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$user_uid = $db->queryUniqueValue("SELECT uid FROM apps_user WHERE user_id='".$request_id."' ");

if(isset($_POST['submit'])) {
	if($_POST['fullname']=='' || $_POST['company_name']=='' || $_POST['address']=='' || $_POST['state']=='' || $_POST['status']=='') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$company_name = htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
		$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$email = htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$address = htmlentities(addslashes($_POST['address']),ENT_QUOTES);
		$city = htmlentities(addslashes($_POST['city']),ENT_QUOTES);
		$pincode = htmlentities(addslashes($_POST['pincode']),ENT_QUOTES);
		$aadhaar = htmlentities(addslashes($_POST['aadhaar']),ENT_QUOTES);
		$cuttoff = htmlentities(addslashes($_POST['cuttoff']),ENT_QUOTES);
		$is_access = isset($_POST['is_access']) ? $_POST['is_access'] : "n";
		$is_fos = isset($_POST['is_fos']) ? $_POST['is_fos'] : "0";
		$is_verified = isset($_POST['is_verified']) ? $_POST['is_verified'] : "n";
		//TDS
		$tds_deduct = isset($_POST['tds_deduct']) && $_POST['tds_deduct']!='' ? $_POST['tds_deduct'] : '0';
		$tds_per = htmlentities(addslashes($_POST['tds_per']),ENT_QUOTES);
		$panno = htmlentities(addslashes($_POST['panno']),ENT_QUOTES);
		//GST
		$gst_deduct = isset($_POST['gst_deduct']) && $_POST['gst_deduct']!='' ? $_POST['gst_deduct'] : '0';
		$has_gst = isset($_POST['has_gst']) && $_POST['has_gst']!='' ? $_POST['has_gst'] : '0';
		$gstin = htmlentities(addslashes($_POST['gstin']),ENT_QUOTES);
		$bill_address = htmlentities(addslashes($_POST['bill_address']),ENT_QUOTES);
		$bill_pincode = htmlentities(addslashes($_POST['bill_pincode']),ENT_QUOTES);
		$bill_city = htmlentities(addslashes($_POST['bill_city']),ENT_QUOTES);
		
		$sEmail = "";		
		if($_POST['user_type']=='5') {
			if($_POST['email']!='') {
				$sEmail = "AND email='".$email."'";
			}
		} else {
			$sEmail = "AND email='".$email."' ";
		}
		
		$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id!='".$request_id."' {$sEmail} ");
		if($exists) {
			$error = 2;
		} else {		
		    if($_POST['status']=='0'||$_POST['status']=='9')
		    { 
		        $db->execute("UPDATE `apps_user` SET `assign_id`='0' where dist_id='".$user_uid."'");
		    }
		    else
		    {
		        
		        
		    }
			$db->execute("UPDATE `apps_user` SET `fullname`='".$fullname."', `company_name`='".$company_name."', `email`='".$email."', `address`='".$address."', `city`='".$city."', `states`='".$_POST['state']."', `pincode`='".$pincode."', `phone`='".$phone."', `aadhaar`='".$aadhaar."', `tds_deduct`='".$tds_deduct."', `tds_per`='".$tds_per."', `panno`='".$panno."', `gst_deduct`='".$gst_deduct."', `has_gst`='".$has_gst."', `gstin`='".$gstin."', `gst_type`='".$_POST['gst_type']."', `bill_address`='".$bill_address."', `bill_pincode`='".$bill_pincode."', `bill_city`='".$bill_city."', `bill_state`='".$_POST['bill_state']."', `is_access`='".$is_access."', `is_fos`='".$is_fos."', `is_verified`='".$is_verified."', `assign_id`='".$_POST['assign_id']."', `status`='".$_POST['status']."' WHERE user_id='".$request_id."' ");			
			$db->execute("UPDATE `apps_wallet` SET `cuttoff`='".$cuttoff."' WHERE user_id='".$request_id."' ");
			$error = 3;
			header("location:user-profile-edit.php?id=".$request_id);
			exit();
		}		
	}
}
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$request_id."' ");
if(!$user) {
	header("location:index.php");
	exit();
}
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id='".$user->user_id."' AND uid='".$user->uid."' ");
//
$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$("#mdist_id").change(function(){
		$.ajax({ 
			url: "ajax/list-distributor.php",
			type: "POST",
			data: "id="+$(this).val(),
			async: false,
			success: function(data) {
				$("select#dist_id").html(data);
			}
		});
	});
	$.validator.addMethod("emailCheck", function(value, element) {
		var isSuccess = false;
		if(value == '') {
			return true;
		} else {
			$.ajax({
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
	
	$('#userForm').validate({
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
					return $("#user_type").val()!='5';
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
			},
			bill_state: {
				required:function(element){
					return $("input[name='gst_deduct']:checked").val()=='1';
				}
			},
			bill_city: {
				required:function(element){
					return $("input[name='gst_deduct']:checked").val()=='1';
				}
			},
			bill_address: {
				required:function(element){
					return $("input[name='gst_deduct']:checked").val()=='1';
				}
			},
			bill_pincode: {
				required:function(element){
					return $("input[name='gst_deduct']:checked").val()=='1';
				}
			}
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"><?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<a href="user-profile.php?id=<?php echo $user->user_id;?>" class="btn btn-primary"><i class="fa fa-user"></i> Back to Profile</a>
				<?php if($user->user_type=='1') {?>
				<a href="api-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
				<?php } elseif($user->user_type=='3') {?>
				<a href="master-distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
				<?php } elseif($user->user_type=='4') {?>
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
				<?php } elseif($user->user_type=='5') {?>
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
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
		<div class="row profile">
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
									<label class="col-sm-4 control-label">Aadhaar Number:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="aadhaar" id="aadhaar" value="<?php echo $user->aadhaar;?>" class="form-control">
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
											<option value="">---Select---</option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>" <?php if($user->states == $rlt->states) {?>selected="selected"<?php } ?>><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="address" id="address" class="form-control"><?php echo $user->address;?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="pincode" id="pincode" value="<?php echo $user->pincode;?>" class="form-control no-full-width">
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
								<?php if($user->user_type=='4') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<div class="col-sm-8 jrequired">
										<?php $mdist = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid = '".$user->mdist_id."'"); ?>
										<input type="text" name="mdist_id" id="mdist_id" disabled="disabled" value="<?php echo $mdist->company_name;?> (<?php echo $user->mdist_id;?>)" class="form-control">
									</div>
								</div>
								<?php } ?>								
								<?php if($user->user_type=='5') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<div class="col-sm-8 jrequired">
										<select name="mdist_id" id="mdist_id" class="form-control">
											<option value="">---Select---</option>
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
											<option value="">---Select---</option>
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
										<input type="text" name="cuttoff" id="cuttoff" value="<?php echo $wallet->cuttoff;?>" class="form-control no-full-width">
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
											<option value="1" <?php if($user->status=='1') {?>selected="selected"<?php } ?>>ACTIVE</option>
											<option value="0" <?php if($user->status=='0') {?>selected="selected"<?php } ?>>INACTIVE</option>
											<option value="9" <?php if($user->status=='9') {?>selected="selected"<?php } ?>>TRASH</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Create Date :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo date("d-m-Y", strtotime($user->added_date));?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Account Manager :</label>
									<div class="col-sm-8 jrequired">
										<select name="assign_id" id="assign_id" class="form-control">
											<option value="">-- Select --</option>
											<?php
											$query = $db->query("SELECT * FROM apps_admin WHERE user_level='a' OR user_level='s' AND status='1' ORDER BY fullname ASC ");
											while($row = $db->fetchNextObject($query)) {
												 if($row->user_level=='a'){
												 	$nickname = "(".$row->username.")";
												} else {
													$nickname = "(Admin)";
												}
											?>
											<option value="<?php echo $row->admin_id;?>"<?php if($row->admin_id==$user->assign_id) {?> selected="selected"<?php } ?>><?php echo $row->fullname;?> <?php echo $nickname;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Is Fos :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_fos" id="is_fos" value="1" <?php if($user->is_fos=='1') {?>checked="checked"<?php } ?>></label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row profile-card">
							<div class="col-md-12">	
								<h3>PAN &amp; GST Detail</h3>
							</div>							
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">TDS (Deduct) :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="tds_deduct" id="tds_deduct_yes" value="1"<?php if($user->tds_deduct=='1'){?> checked="checked"<?php }?>> Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="tds_deduct" id="tds_deduct_no" value="0"<?php if($user->tds_deduct=='0'){?> checked="checked"<?php }?>> No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">TDS % :</label>
									<div class="col-sm-8 jrequired">
										<div class="input-group">
											<input type="text" name="tds_per" id="tds_per" value="<?php echo $user->tds_per;?>" class="form-control">
											<div class="input-group-addon">%</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">PAN NO :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="panno" id="panno" value="<?php echo $user->panno;?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">GST (Deduct) :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="gst_deduct" id="gst_deduct_yes" value="1"<?php if($user->gst_deduct=='1'){?> checked="checked"<?php }?>> Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_deduct" id="gst_deduct_no" value="0"<?php if($user->gst_deduct=='0'){?> checked="checked"<?php }?>> No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Has GST No. :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="has_gst" id="has_gst_yes" value="1"<?php if($user->has_gst=='1'){?> checked="checked"<?php }?>> Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="has_gst" id="has_gst_no" value="0"<?php if($user->has_gst=='0'){?> checked="checked"<?php }?>> No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">GSTIN :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="gstin" id="gstin" value="<?php echo $user->gstin;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">GST Type :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_igst" value="1"<?php if($user->gst_type=='1'){?> checked="checked"<?php }?>> IGST
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_sgst" value="2"<?php if($user->gst_type=='2'){?> checked="checked"<?php }?>> SGST/CGST
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_none" value="0"<?php if($user->gst_type=='0'){?> checked="checked"<?php }?>> None
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row profile-card">
							<div class="col-md-12">	
								<h3>Billing Details</h3>
							</div>
							<div class="col-md-6">							
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<select name="bill_state" id="bill_state" class="form-control">
											<option value="">---Select---</option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>" <?php if($user->bill_state==$rlt->states) {?>selected="selected"<?php } ?>><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="bill_city" id="bill_city" value="<?php echo $user->bill_city;?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="bill_address" id="bill_address" class="form-control"><?php echo $user->bill_address;?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="bill_pincode" id="bill_pincode" value="<?php echo $user->bill_pincode;?>" class="form-control no-full-width">
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
									<i class="fa fa-save"></i> Update User
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