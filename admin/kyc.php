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
	$db->execute("UPDATE `apps_user_kyc` SET `passport`='".$pp_img."' WHERE kyc_id = '".$kyc_id."' ");
}
$kyc = $db->queryUniqueObject("SELECT * FROM apps_user_kyc WHERE user_id = '".$request_id."' ");
if($kyc) {
	$kyc_type = "edit";
	$kyc_id = $kyc->kyc_id;
	$firstname = $kyc->firstname;
	$middlename = $kyc->middlename;
	$lastname = $kyc->lastname;
	$mothername = $kyc->mothername;
	$dob = $kyc->dob;
	$gender = $kyc->gender;
	$mobile = $kyc->mobile;
	$phone = $kyc->phone;
	$email = $kyc->email;
	$permanent_address = $kyc->permanent_address;
	$correspondence_address = $kyc->correspondence_address;
	$city = $kyc->city;
	$pincode = $kyc->pincode;
	$pancard_number = $kyc->pancard_number;
	$address_proof_type = $kyc->address_proof_type;
	$address_proof_doc_no = $kyc->address_proof_doc_no;
	$business_name = $kyc->business_name;
	$business_address = $kyc->business_address;
	$business_type = $kyc->business_type;
	$business_type_detail = $kyc->business_type_detail;
	$passport = $kyc->passport;
	$pancard = $kyc->pancard;
	$addressproof1 = $kyc->addressproof1;
	$addressproof2 = $kyc->addressproof2;
} else {
	$kyc_type = "add";
	$kyc_id = "0";
	$firstname = "";
	$middlename = "";
	$lastname = "";
	$mothername = "";
	$dob = "";
	$gender = "";
	$mobile = "";
	$phone = "";
	$email = "";
	$permanent_address = "";
	$correspondence_address = "";
	$city = "";
	$pincode = "";
	$pancard_number = "";
	$address_proof_type = "";
	$address_proof_doc_no = "";
	$business_name = "";
	$business_address = "";
	$business_type = "";
	$business_type_detail = "";
	$passport = "";
	$pancard = "";
	$addressproof1 = "";
	$addressproof2 = "";
}
$meta['title'] = "KYC";
include("header.php");
?>
<style>
	.img-kyc {
		border:1px solid #ddd;
		padding:5px;
		margin-top:15px;
		width:150px;
	}
</style>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script>
jQuery(document).ready(function(){		
	jQuery('#kycForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<style>
.kyc-form .form-group {
	border-bottom:1px solid #ddd;
	padding-bottom:10px;
	padding-top:10px;
}
.kyc-form label {
	font-weight:300;
}
.kyc-form p.form-control-static {
	font-weight:700;
}
</style>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">My Account <small>/ KYC</small></div>
			<div class="pull-right">
				<a href="kyc-print.php?id=<?php echo $request_id;?>" class="btn btn-default" target="_blank"><i class="fa fa-print"></i> Print Preview</a>
				<a href="kyc-form.php?id=<?php echo $request_id;?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
				<a href="view-user-profile.php?id=<?php echo $request_id;?>" class="btn btn-default"><i class="fa fa-user"></i> Profile</a>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> KYC</h3>
					</div>
					<form action="" method="post" id="kycForm" class="form-horizontal kyc-form" enctype="multipart/form-data">
					<input type="hidden" name="kyc_id" id="kyc_id" value="<?php echo $kyc_id;?>" />
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="form-group margin-top-40">
								<div class="col-sm-4">
									<label>First Name</label>
									<div class="jrequired">
									<p class="form-control-static"><?php echo $firstname;?></p>
									</div>
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
								<div class="col-sm-4">
									<label>Mother's Maiden Name</label>
									<p class="form-control-static"><?php echo $mothername;?></p>
								</div>
								<div class="col-sm-4">
									<label>Date Of Birth</label>
									<p class="form-control-static"><?php echo $dob;?></p>
								</div>
								<div class="col-sm-4">
									<label>Gender</label>
									<p class="form-control-static"><?php echo $gender;?></p>
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
									<label>Email</label>
									<p class="form-control-static"><?php echo $email;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label>Permanent Address</label>
									<p class="form-control-static"><?php echo $permanent_address;?></p>
								</div>
								<div class="col-sm-6">
									<label>Correspondence Address</label>
									<p class="form-control-static"><?php echo $correspondence_address;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<label>City</label>
									<p class="form-control-static"><?php echo $city;?></p>
								</div>
								<div class="col-sm-4">
									<label>Pincode</label>
									<p class="form-control-static"><?php echo $pincode;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<label>Pancard Number</label>
									<p class="form-control-static"><?php echo $pancard_number;?></p>
								</div>
								<div class="col-sm-4">
									<label>Address Proof Document Type</label>
									<p class="form-control-static">
									<?php if($address_proof_type=='aadhaar'){?>Aadhaar Card
									<?php } else if($address_proof_type=='driving'){?>Driving License
									<?php } else if($address_proof_type=='passport'){?>Passport
									<?php } else if($address_proof_type=='voterid'){?>Election ID Card
									<?php } ?>
									</p>
								</div>
								<div class="col-sm-4">
									<label>Address Proof Document Ref No</label>
									<p class="form-control-static"><?php echo $address_proof_doc_no;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<b><i class="fa fa-bank"></i> Business Details</b>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6">
									<label>Business Name</label>
									<p class="form-control-static"><?php echo $business_name;?></p>
								</div>
								<div class="col-sm-6">
									<label>Bussiness Address</label>
									<p class="form-control-static"><?php echo $business_address;?></p>
								</div>
							</div>
							<div class="form-group">									
								<div class="col-sm-6">
									<label>Business Type</label>
									<p class="form-control-static">
									<?php if($business_type=='1'){?>Retail Shop
									<?php } else if($business_type=='2'){?>Super Market
									<?php } else if($business_type=='3'){?>IT Company
									<?php } else if($business_type=='4'){?>Courier
									<?php } else if($business_type=='5'){?>Medical Shop
									<?php } else if($business_type=='6'){?>Mobile Repairing
									<?php } else if($business_type=='7'){?>Provision Store
									<?php } else if($business_type=='8'){?>Mobile Showrooms
									<?php } else if($business_type=='9'){?>PAN Shop
									<?php } else if($business_type=='0'){?>Others
									<?php } ?>
									</p>
								</div>
								<div class="col-sm-6" id="business_type_other" <?php if($business_type=='0'){?>style="display:block;"<?php } else {?>style="display:none;"<?php } ?>>
									<label>Other Business Specify</label>
									<p class="form-control-static"><?php echo $business_type_detail;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<b><i class="fa fa-paperclip"></i> Attachments</b>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<label>Photograph</label>
									<p class="form-control-static">
									<?php if($passport!='' && file_exists("../uploads/kyc/".$passport)) { ?>
									<a href="<?php echo "../uploads/kyc/".$passport;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$passport;?>" class="img-kyc" /></a>
									<?php } ?>
									</p>
								</div>
								<div class="col-sm-3">
									<label>Pancard</label>
									<p class="form-control-static">
										<?php if($pancard!='' && file_exists("../uploads/kyc/".$pancard)) { ?>
										<a href="<?php echo "../uploads/kyc/".$pancard;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$pancard;?>" class="img-kyc" /></a>
										<?php } ?>
									</p>
								</div>
								<div class="col-sm-3">
									<label>Address Proof (1)</label>
									<p class="form-control-static">
										<?php if($addressproof1!='' && file_exists("../uploads/kyc/".$addressproof1)) { ?>
										<a href="<?php echo "../uploads/kyc/".$addressproof1;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof1;?>" class="img-kyc" /></a>
										<?php } ?>
									</p>
								</div>
								<div class="col-sm-3">
									<label>Address Proof (2)</label>
									<p class="form-control-static">
										<?php if($addressproof2!='' && file_exists("../uploads/kyc/".$addressproof2)) { ?>
										<a href="<?php echo "../uploads/kyc/".$addressproof2;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof2;?>" class="img-kyc" /></a>
										<?php } ?>
									</p>
								</div>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>