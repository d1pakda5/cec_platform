<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
if(!isset($_GET['token']) || $_GET['token'] != $token) {
	exit("Token not match");
}
$request_id = $_SESSION['retailer'];
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$ip = $_SERVER['REMOTE_ADDR'];

if(isset($_POST['submit'])) {	
	if($_POST['fullname']=='' || $_POST['email']=='' || $_POST['address']=='' || $_POST['state']=='' || $_POST['city']=='' || $_POST['pincode']=='' || $_POST['panno']=='' || $_POST['aadharno']=='') {
		$error = 1;		
	} else {		
		$fullname = htmlentities(addslashes($_POST['fullname']),ENT_QUOTES);
		//$company_name = htmlentities(addslashes($_POST['company_name']),ENT_QUOTES);
		$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$email = htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$address = htmlentities(addslashes($_POST['address']),ENT_QUOTES);
		$city = htmlentities(addslashes($_POST['city']),ENT_QUOTES);
		$pincode = htmlentities(addslashes($_POST['pincode']),ENT_QUOTES);
		$panno = htmlentities(addslashes($_POST['panno']),ENT_QUOTES);
		$aadharno = htmlentities(addslashes($_POST['aadharno']),ENT_QUOTES);
		$gstin = htmlentities(addslashes($_POST['gstin']),ENT_QUOTES);
		$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE (email='".$email."' OR panno='".$panno."' OR aadharno='".$aadharno."') AND user_id!='".$request_id."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("UPDATE `apps_user` SET `fullname`='".$fullname."', `phone`='".$phone."', `email`='".$email."', `address`='".$address."', `states`='".$_POST['state']."', `city`='".$city."', `pincode`='".$pincode."', `panno`='".$panno."', `aadharno`='".$aadharno."', `gstin`='".$gstin."', `update_date`=NOW(), `updateip`='".$ip."' WHERE user_id='".$request_id."' ");
			$db->execute("INSERT INTO `verificationlog`(`uid`, `type`, `verifystatus`) VALUES ('".$aRetailer->uid."', '1', '0')");
			$error = 3;
			header("location:profile.php?token=".$token."&error=".$error);
			exit();
		}
	}
}

$meta['title'] = "Profile";
include("header.php");
?>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Account <small>/ Profile</small></div>
			<div class="pull-right">
				<a href="kyc.php?token=<?php echo $token;?>" class="btn btn-info"><i class="fa fa-user"></i> KYC</a>
			</div>
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
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">UID :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="uid" id="uid" disabled="disabled" value="<?php echo $aRetailer->uid;?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Username :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="username" id="username" disabled="disabled" value="<?php echo $aRetailer->username;?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Full Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="fullname" id="fullname" value="<?php echo $aRetailer->fullname;?>" class="form-control" placeholder="Enter your full name" />
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="company_name" id="company_name" value="<?php echo $aRetailer->company_name;?>"<?php if($aRetailer->company_name!='') { ?> readonly="readonly"<?php } ?> class="form-control" placeholder="Enter your company name" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" readonly="" value="<?php echo $aRetailer->mobile;?>" class="form-control" placeholder="Enter mobile number" />
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="phone" id="phone" value="<?php echo $aRetailer->phone;?>" class="form-control" placeholder="Enter phone number" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="email" id="email" value="<?php echo $aRetailer->email;?>" class="form-control" placeholder="Enter email address" />
									</div>
								</div>	
							</div>
						</div>						
						<div class="row">
							<div class="col-sm-6">							
								<div class="form-group">
									<label class="col-sm-4 control-label">Address :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="address" id="address" class="form-control" placeholder="Enter email address"><?php echo $aRetailer->address;?></textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">City :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="city" id="city" value="<?php echo $aRetailer->city;?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">State :</label>
									<div class="col-sm-8 jrequired">
										<select name="state" id="state" class="form-control">
											<option value=""></option>
											<?php $qry = $db->query("SELECT * FROM states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>" <?php if($aRetailer->states==$rlt->states) {?>selected="selected"<?php } ?>><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Pincode:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="pincode" id="pincode" value="<?php echo $aRetailer->pincode;?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">PAN Number:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="panno" id="panno" value="<?php echo $aRetailer->panno;?>"<?php if($aRetailer->panno!='') { ?> readonly="readonly"<?php } ?> class="form-control">
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Aadhar Number:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="aadharno" id="aadharno" value="<?php echo $aRetailer->aadharno;?>"<?php if($aRetailer->aadharno!='') { ?> readonly="readonly"<?php } ?> class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">GSTIN ID:</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="gstin" id="gstin" value="<?php echo $aRetailer->gstin;?>"<?php if($aRetailer->gstin!='') { ?> readonly="readonly"<?php } ?> class="form-control">
									</div>
								</div>
							</div>
						</div>						
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
							    <?php if($_SESSION['retailer_uid']=='20032374')
								{?>
                                	<button type="submit" disabled name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Profile
								</button>

								<?php } else {
								?>
                                	<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Profile
								</button>

								<?php }?>

							
							</div>
						</div>
					</div>
					</form>
				</div>
				<p>Your account has been registered on <?php echo date("d/F/Y", strtotime($aRetailer->added_date));?>.
				<?php if($aRetailer->update_date!='0000-00-00 00:00:00') {?>
				&nbsp; Last update on <?php echo date("d/F/Y H:i A", strtotime($aRetailer->update_date));?> from IP <?php echo $aRetailer->updateip;?>
				<?php } ?>
				</p>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>