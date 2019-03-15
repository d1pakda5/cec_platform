<?php
session_start();
if(!isset($_SESSION['mdistributor'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
if(!isset($_GET['token']) || $_GET['token']!=$token) {
	exit("Token not match");
}
$request_id = $_SESSION['mdistributor'];
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

$kyc = $db->queryUniqueObject("SELECT * FROM apps_user_kyc WHERE user_id='".$aMaster->user_id."' ");
if($kyc) {
	$kyc_id = $kyc->kyc_id;
	$firstname = $kyc->firstname;
	$middlename = $kyc->middlename;
	$lastname = $kyc->lastname;
	$fathername = $kyc->fathername;
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
	$aadhaar_number = $kyc->aadhaar_card_number;
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
	$is_edit = $kyc->is_edit;
} else {
	header("location:kyc-form.php?token=".$token);
	exit();
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
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Account <small>/ KYC</small></div>
			<div class="pull-right">
				<?php if($is_edit=='y') { ?>
				<a href="kyc-form.php?token=<?php echo $token;?>" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
				<?php } ?>
				<a href="profile.php?token=<?php echo $token;?>" class="btn btn-primary"><i class="fa fa-user"></i> Profile</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
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
					<div class="box-body padding-50 kyc-form">
						<div class="row">
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
								<div class="col-sm-4">
									<label>Father Name</label>
									<p class="form-control-static"><?php echo $fathername;?></p>
								</div>
								<div class="col-sm-4">
									<label>Mother's Maiden Name</label>
									<p class="form-control-static"><?php echo $mothername;?></p>
								</div>
								<div class="col-sm-2">
									<label>Date Of Birth</label>
									<p class="form-control-static"><?php echo $dob;?></p>
								</div>
								<div class="col-sm-2">
									<label>Gender</label>
									<p class="form-control-static"><?php if($gender=='male'){ echo "Male";} else { echo "Female";}?></p>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-sm-3">
									<label>Mobile Number</label>
									<p class="form-control-static"><?php echo $mobile;?></p>
								</div>
								<div class="col-sm-3">
									<label>Phone Number</label>
									<p class="form-control-static"><?php echo $phone;?></p>
								</div>
								<div class="col-sm-6">
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
								<div class="col-sm-2">
									<label>Pincode</label>
									<p class="form-control-static"><?php echo $pincode;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3">
									<label>Pancard Number</label>
									<p class="form-control-static"><?php echo $pancard_number;?></p>
								</div>
								<div class="col-sm-3">
									<label>Aadhaar Number</label>
									<p class="form-control-static"><?php echo $aadhaar_number;?></p>
								</div>
								<div class="col-sm-3">
									<label>Address Proof Doc Type</label>
									<p class="form-control-static">
										<?php if($address_proof_type=='driving'){ echo "Driving License";} elseif($address_proof_type=='passport') { echo "Passport Number"; } elseif($address_proof_type=='voterid') { echo "Election ID Card";}?>
									</p>
								</div>
								<div class="col-sm-3">
									<label>Address Proof Doc Ref No</label>
									<p class="form-control-static"><?php echo $address_proof_doc_no;?></p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12"><h4>Business Details</h4></div>
							</div>
							<div class="form-group">									
								<div class="col-sm-6">
									<label>Business Type</label>
									<p class="form-control-static">
										<?php if($business_type=='1'){?>
											Retail Shop
										<?php }elseif($business_type=='2'){?>
											Super Market
										<?php }elseif($business_type=='3'){?>
											IT Company
										<?php }elseif($business_type=='4'){?>
											Courier
										<?php }elseif($business_type=='5'){?>
											Medical Shop
										<?php }elseif($business_type=='6'){?>
											Mobile Repairing
										<?php }elseif($business_type=='7'){?>
											Provision Store
										<?php }elseif($business_type=='8'){?>
											Mobile Showrooms
										<?php }elseif($business_type=='9'){?>
											PAN Shop
										<?php }elseif($business_type=='0'){?>
											Others
										<?php } ?>
									</p>
								</div>
								<div class="col-sm-6" id="business_type_other" <?php if($business_type=='0'){?>style="display:block;"<?php } else {?>style="display:none;"<?php } ?>>
									<label>Other Business Specify</label>
									<p class="form-control-static"><?php echo $business_type_detail;?></p>
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
								<div class="col-sm-12">
									<h4>Attached Documents</h4>
								</div>
							</div>
							<div class="form-group">
								<div class="col-xs-3">
									<label>Photograph</label>
									<div class="jrequired">
										<?php if($passport!='' && file_exists("../uploads/kyc/".$passport)) { ?>
										<a href="<?php echo "../uploads/kyc/".$passport;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$passport;?>" class="img-kyc" /></a>
										<?php } ?>
									</div>
								</div>
								<div class="col-xs-3">
									<label>Pancard</label>
									<div class="jrequired">
										<?php if($pancard!='' && file_exists("../uploads/kyc/".$pancard)) { ?>
										<a href="<?php echo "../uploads/kyc/".$pancard;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$pancard;?>" class="img-kyc" /></a>
										<?php } ?>
									</div>
								</div>
								<div class="col-xs-3">
									<label>Aadhaar Card</label>
									<div class="jrequired">
										<?php if($addressproof1!='' && file_exists("../uploads/kyc/".$addressproof1)) { ?>
										<a href="<?php echo "../uploads/kyc/".$addressproof1;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof1;?>" class="img-kyc" /></a>
										<?php } ?>
									</div>
								</div>
								<div class="col-xs-3">
									<label>Address Proof (1)</label>
									<div class="jrequired">
										<?php if($addressproof2!='' && file_exists("../uploads/kyc/".$addressproof2)) { ?>
										<a href="<?php echo "../uploads/kyc/".$addressproof2;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof2;?>" class="img-kyc" /></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>