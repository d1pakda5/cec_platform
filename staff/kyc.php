<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
include("common.php");
if(empty($sP['kyc']['view'])) { 
	include('permission.php');
	exit(); 
}
$requestid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {	
	if($_POST['declare']=='' || $_POST['status']=='') {
		$error = 1;
	} else {		
		$remark = trim(htmlentities(addslashes($_POST['remark']),ENT_QUOTES));
		$db->execute("UPDATE `userskyc` SET `validatedate`=NOW(), `validateremark`='".$remark."', `validatedby`='".$_SESSION['staff']."', `status`='".$_POST['status']."' WHERE id='".$requestid."' ");
		if($_POST['status']=='2') {
			$uid = trim(htmlentities(addslashes($_POST['uid']),ENT_QUOTES));
			$db->execute("UPDATE `apps_user` SET `is_kyc`='0' WHERE uid='".$uid."' ");
		}
		header("location:kyc-verification.php?id=".$requestid."&error=3");	
		exit();
	}
}

// Fetch KYC
$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$requestid."' ");
if($kyc) {
	$action = "eadit";
	$id = $kyc->id;
	$uid = $kyc->uid;
	$firstname = $kyc->firstname;
	$middlename = $kyc->middlename;
	$lastname = $kyc->lastname;
	$fathersname = $kyc->fathersname;
	$mothersname = $kyc->mothersname;
	$dob = $kyc->dob;
	$gender = $kyc->gender;
	$mobile = $kyc->mobile;
	$phone = $kyc->phone;
	$fax = $kyc->fax;
	$email = $kyc->email;
	$addressperm = $kyc->addressperm;
	$addresscorres = $kyc->addresscorres;
	$city = $kyc->city;
	$state = $kyc->state;
	$pincode = $kyc->pincode;
	$pancard = $kyc->pancard;
	$companytype = $kyc->companytype;
	$aadhaar = $kyc->aadhaar;
	$gstin = $kyc->gstin;
	$adrprooftype = $kyc->adrprooftype;
	$adrprooftypeno = $kyc->adrprooftypeno;
	$businessname = $kyc->businessname;
	$businessaddress = $kyc->businessaddress;
	$businesstype = $kyc->businesstype;
	$businesstypename = $kyc->businesstypename;
	$photofile = $kyc->photofile;
	$pancardfile = $kyc->pancardfile;
	$aadhaarfile = $kyc->aadhaarfile;
	$addressfile = $kyc->addressfile;
	$validatedate = $kyc->validatedate;
	$validateremark = $kyc->validateremark;
	$validatedby = $kyc->validatedby;
	$status = $kyc->status;
} else {
	header("location:kyc-add.php?uid=".$requestid);	
	exit();
}

// Fetch User
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$kyc->uid."' ");
if(!$user) {
	header("location:all-users.php");	
	exit();
}

$meta['title'] = "KYC Preview";
include("header.php");
?>
<style>
.kyc-form {
	background:#fbfcfe;
}
.form-control-static {
	font-weight:bold;
	border:1px solid #ddd;
	padding-left:5px!important;
}
.kyc-form .form-control {
	border-color:#62a3d1;
}
.kyc-form .form-control:focus {
	border-color:#e04a1c;
	box-shadow: none;
}
.kyc-form .form-group {
	margin-bottom:0px;
	border-bottom:0px;
}
.kyc-form .form-control[readonly],
.kyc-form fieldset[disabled] .form-control {
  background-color: #eee;
  opacity: 1;
}
.kyc-form.kyc-form-group .form-group {
	padding-bottom:15px!important;
	padding-top:15px!important;
	margin:0px!important;
}
.img-kyc {
	border:1px solid #ddd;
	padding:5px;
	margin-top:15px;
	width:150px;
}
.text-error {
	color:#FF0000;
}
.file-control-static {
	width:100%;
	padding:5px;
	border:1px solid #ddd;
}
</style>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<script>
jQuery(document).ready(function() {
	jQuery(".fancybox").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	jQuery("#declare").click(function() {
		var thisCheck = jQuery(this);
		if(thisCheck.is(':checked')) {
   		jQuery('#submit').prop("disabled", false);
		} else {
      jQuery("#submit").prop("disabled", true);
    }
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">KYC <small>/ Preview</small></div>
			<div class="pull-right">
				<a href="kyc-print.php?id=<?php echo $kyc->id;?>" class="btn btn-default" target="_blank"><i class="fa fa-print"></i> Print</a>
				<a href="kyc-edit.php?id=<?php echo $kyc->id;?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
				<a href="view-user-profile.php?uid=<?php echo $requestid;?>" class="btn btn-default"><i class="fa fa-user"></i> Profile</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> KYC has been submitted successfully.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<?php if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Some fields or paramerters are blank, please check and submit again.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> KYC</h3>
					</div>
					<div class="box-body no-padding kyc-form">
						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<div class="col-sm-4">
										<label>UID</label>
										<p class="form-control-static"><?php echo $uid;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>First Name</label>
										<p class="form-control-static"><?php echo $firstname;?></p>
									</div>
									<div class="col-sm-4">
										<label>Middle Name</label>
										<p class="form-control-static"><?php echo $middlename;?></p>
									</div>
									<div class="col-sm-4">
										<label>Last Name</label>
										<p class="form-control-static"><?php echo $lastname;?></p>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-6">
										<label>Father Name</label>
										<p class="form-control-static"><?php echo $fathersname;?></p>
									</div>
									<div class="col-sm-6">
										<label>Mother's Maiden Name</label>
										<p class="form-control-static"><?php echo $mothersname;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<label>Date Of Birth</label>
										<p class="form-control-static"><?php echo $dob;?></p>
									</div>
									<div class="col-sm-3">
										<label>Gender</label>
										<p class="form-control-static"><?php echo $gender;?></p>
									</div>
									<div class="col-sm-6">
										<label>Email</label>
										<p class="form-control-static"><?php echo $email;?></p>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>Mobile Number</label>
										<p class="form-control-static"><?php echo $mobile;?></p>
									</div>
									<div class="col-sm-4">
										<label>Phone Number</label>
										<p class="form-control-static"><?php echo $phone;?></p>
									</div>
									<div class="col-sm-4">
										<label>Fax Number</label>
										<p class="form-control-static"><?php echo $fax;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-6">
										<label>Permanent Address</label>
										<p class="form-control-static"><?php echo $addressperm;?></p>
									</div>
									<div class="col-sm-6">
										<label>Correspondence Address</label>
										<p class="form-control-static"><?php echo $addresscorres;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>City</label>
										<p class="form-control-static"><?php echo $city;?></p>
									</div>
									<div class="col-sm-5">
										<label>States</label>
										<p class="form-control-static"><?php echo $state;?></p>
									</div>
									<div class="col-sm-3">
										<label>Pincode</label>
										<p class="form-control-static"><?php echo $pincode;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>Pancard Number</label>
										<p class="form-control-static"><?php echo $pancard;?></p>
									</div>
									<div class="col-sm-4">
										<label>Company Type</label>
										<p class="form-control-static"><?php echo $companytype;?></p>
									</div>
									<div class="col-sm-4">
										<label>Aadhaar Number</label>
										<p class="form-control-static"><?php echo $aadhaar;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>GSTIN ID</label>
										<p class="form-control-static"><?php echo $gstin;?></p>
									</div>
									<div class="col-sm-4">
										<label>Address Proof Document Type</label>
										<p class="form-control-static"><?php echo $adrprooftype;?></p>
									</div>
									<div class="col-sm-4">
										<label>Document Ref No Details</label>
										<p class="form-control-static"><?php echo $adrprooftypeno;?></p>
									</div>
								</div>
							</div>
							<!-- start of business details -->
							<div class="col-md-4">
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business Name</label>
										<p class="form-control-static"><?php echo $businessname;?></p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business address</label>
										<p class="form-control-static"><?php echo $businessaddress;?></p>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-12">								
										<label>Business Type</label>
										<p class="form-control-static">
											<?php if($businesstype=='1'){?>
												Retail Shop
											<?php } elseif($businesstype=='2'){?>
												Super Market
											<?php } elseif($businesstype=='3'){?>
												IT Company
											<?php } elseif($businesstype=='3'){?>
												Courier
											<?php } elseif($businesstype=='3'){?>
												Medical Shop
											<?php } elseif($businesstype=='3'){?>
												Mobile Repairing
											<?php } elseif($businesstype=='3'){?>
												Provision Store
											<?php } elseif($businesstype=='3'){?>
												Mobile Showrooms
											<?php } elseif($businesstype=='3'){?>
												PAN Shop
											<?php } elseif($businesstype=='0'){?>
												<?php echo $businesstypename;?>
											<?php } ?>
										</p>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-6">								
										<label>Photo</label>
										<p class="file-control-static">
											<?php if($photofile!='') {?>
											<a href="../<?php echo $photofile;?>" class="fancybox" rel="gallery1"><img src="../<?php echo $photofile;?>" class="img-responsive" /></a>
											<?php } ?>
										</p>
									</div>
									<div class="col-sm-6">								
										<label>Pancard</label>
										<p class="file-control-static">
											<?php if($pancardfile!='') {?>
											<a href="../<?php echo $pancardfile;?>" class="fancybox" rel="gallery1"><img src="../<?php echo $pancardfile;?>" class="img-responsive" /></a>
											<?php } ?>
										</p>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-6">								
										<label>Aadhaar Card</label>
										<p class="file-control-static">
											<?php if($aadhaarfile!='') {?>
											<a href="../<?php echo $aadhaarfile;?>" class="fancybox" rel="gallery1"><img src="../<?php echo $aadhaarfile;?>" class="img-responsive" /></a>
											<?php } ?>
										</p>
									</div>
									<div class="col-sm-6">								
										<label>Address Proof</label>
										<p class="file-control-static">
											<?php if($addressfile!='') {?>
											<a href="../<?php echo $addressfile;?>" class="fancybox" rel="gallery1"><img src="../<?php echo $addressfile;?>" class="img-responsive" /></a>
											<?php } ?>
										</p>
									</div>
								</div>
							</div>
							<!-- end of business details -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>