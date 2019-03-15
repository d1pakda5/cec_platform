<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['fullname']=='' || $_POST['company_name']=='' || $_POST['mobile']=='' || $_POST['email']=='' || $_POST['address']=='' || $_POST['state']=='' || $_POST['uid']=='' || $_POST['status']=='' || $_POST['dist_id']=='' || $_POST['password']=='') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		$company_name =  htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
		$mobile =  htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$phone =  htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$email =  htmlentities(addslashes($_POST['email']),ENT_QUOTES);	
		$aadhaar = htmlentities(addslashes($_POST['aadhaar']),ENT_QUOTES);
		$address =  htmlentities(addslashes($_POST['address']),ENT_QUOTES);
		$city =  htmlentities(addslashes($_POST['city']),ENT_QUOTES);	
		$pincode = htmlentities(addslashes($_POST['pincode']),ENT_QUOTES);
		$uid =  htmlentities(addslashes($_POST['uid']),ENT_QUOTES);
		$cuttoff =  htmlentities(addslashes($_POST['cuttoff']),ENT_QUOTES);
		$is_access = isset($_POST['is_access']) ? $_POST['is_access'] : "n";
		$is_verified = isset($_POST['is_verified']) ? $_POST['is_verified'] : "n";
		$password = htmlentities(addslashes($_POST['password']),ENT_QUOTES);
		$hashPassword = hashPassword($password);
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
		
		$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' OR mobile='".$mobile."' OR email='".$email."' OR username='".$mobile."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("INSERT INTO `apps_user`(`uid`, `user_type`, `dist_id`, `fullname`, `company_name`, `mobile`, `phone`, `email`, `address`, `states`, `city`, `pincode`, `aadhaar`, `tds_deduct`, `tds_per`, `panno`, `gst_deduct`, `has_gst`, `gstin`, `gst_type`, `bill_address`, `bill_pincode`, `bill_city`, `bill_state`, `username`, `password`, `pin`, `is_access`, `is_verified`, `is_kyc`, `is_recharge`, `is_money`, `is_deduct`, `assign_id`, `status`, `added_date`) VALUES ('".$uid."', '5', '".$_POST['dist_id']."', '".$fullname."', '".$company_name."', '".$mobile."', '".$phone."', '".$email."', '".$address."', '".$_POST['state']."', '".$city."', '".$pincode."', '".$aadhaar."', '".$tds_deduct."', '".$tds_per."', '".$panno."', '".$gst_deduct."', '".$has_gst."', '".$gstin."', '".$_POST['gst_type']."', '".$bill_address."', '".$bill_pincode."', '".$bill_city."', '".$_POST['bill_state']."', '".$mobile."', '".$hashPassword."', '1234', '".$is_access."', '".$is_verified."', '0', '1', '0', '0', '".$_POST['assign_id']."', '".$_POST['status']."', NOW())");			
			$user_id = $db->lastInsertedId();				
			$db->execute("INSERT INTO `apps_wallet`(`wallet_id`, `user_id`, `uid`, `balance`, `cuttoff`, `is_locked`, `update_time`) VALUES ('', '".$user_id."', '".$uid."', '0', '".$cuttoff."', '0', NOW())");
			$websitename = getWebsiteName($_POST['dist_id']);
			$message = smsUserActivation($websitename, $mobile, $password, date("d-m-Y"));
			smsSendSingle($mobile, $message, 'registration');
			if($email != '') {
				mailNewClient($fullname, $company_name, $mobile, $email, $mobile, $password, $websitename);
			}
			$error = 3;
		}		
	}
}
$meta['title'] = "Retailer - Add";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$("#mobile").keyup(function() {
		$("#username").val($(this).val());
	});
	$.validator.addMethod("mobileCheck", function(value, element) {
		var isSuccess = false;
		$.ajax({
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
	
	$.validator.addMethod("emailCheck", function(value, element) {
		var isSuccess = false;
		if(value == '') {
			return true;
		} else {
			$.ajax({
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
		}
	}, "Email is used by another user");
	
	$('#userForm').validate({
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
				required:false,
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
			<div class="page-title">Retailer <small>/ Add</small></div>
			<div class="pull-right">
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
						<h3 class="box-title"><i class="fa fa-plus-square"></i> Create new Retailer</h3>
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
									<label class="col-sm-4 control-label">Aadhaar Number:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="aadhaar" id="aadhaar" class="form-control">
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
											<option value="">---Select---</option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>"><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="pincode" id="pincode" class="form-control no-full-width">
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
									<label class="col-sm-4 control-label">Distributor :</label>
									<div class="col-sm-8 jrequired">
										<select name="dist_id" id="dist_id" class="form-control">
											<option value="">---Select---</option>
											<?php
											$qry = $db->query("SELECT * FROM apps_user WHERE user_type='4' AND status='1' ORDER BY company_name ASC ");
											while($rst = $db->fetchNextObject($qry)) {?>
											<option value="<?php echo $rst->uid;?>"><?php echo $rst->company_name;?> (<?php echo $rst->uid;?>)</option>
											<?php } ?>
										</select>
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
										<input type="text" name="cuttoff" id="cuttoff" class="form-control">
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
											<option value="">---Select---</option>
											<option value="1">ACTIVE</option>
											<option value="0">INACTIVE</option>
											<option value="9">TRASH</option>
										</select>
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
											<option value="<?php echo $row->admin_id;?>"><?php echo $row->fullname;?> <?php echo $nickname;?></option>
											<?php } ?>
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
						<!--start of GST row-->
						<div class="row profile-card">
							<div class="col-md-12">	
								<h3>PAN &amp; GST Detail</h3>
							</div>							
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">TDS (Deduct) :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="tds_deduct" id="tds_deduct_yes" value="1"> Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="tds_deduct" id="tds_deduct_no" value="0"> No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">TDS % :</label>
									<div class="col-sm-8 jrequired">
										<div class="input-group">
											<input type="text" name="tds_per" id="tds_per" class="form-control">
											<div class="input-group-addon">%</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">PAN NO :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="panno" id="panno" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">GST (Deduct) :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="gst_deduct" id="gst_deduct_yes" value="1"> Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_deduct" id="gst_deduct_no" value="0"> No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Has GST No. :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="has_gst" id="has_gst_yes" value="1" > Yes
										</label>
										<label class="radio-inline">
											<input type="radio" name="has_gst" id="has_gst_no" value="0" > No
										</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">GSTIN :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="gstin" id="gstin" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">GST Type :</label>
									<div class="col-sm-8 jrequired">
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_igst" value="1"> IGST
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_sgst" value="2"> SGST/CGST
										</label>
										<label class="radio-inline">
											<input type="radio" name="gst_type" id="gst_type_none" value="0" checked="checked"> None
										</label>
									</div>
								</div>
							</div>
						</div>
						<!-- end of GST row -->
						<div class="row profile-card">
							<div class="col-md-12">	
								<h3>Billing Details</h3>
							</div>
							<div class="col-md-6">							
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<select name="bill_state" id="bill_state" class="form-control">
											<option value="">- Select States -</option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>"><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="bill_city" id="bill_city" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="bill_address" id="bill_address" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="bill_pincode" id="bill_pincode" class="form-control no-full-width">
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