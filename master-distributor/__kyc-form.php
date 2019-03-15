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
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {	
	if($_POST['firstname']=='' || $_POST['lastname']=='' || $_POST['mothername']=='' || $_POST['dob']=='' || $_POST['gender']=='' || $_POST['mobile']=='' || $_POST['email']=='' || $_POST['permanent_address']=='' || $_POST['correspondence_address']=='' || $_POST['city']=='' || $_POST['pincode']=='' || $_POST['pancard_number']=='' || $_POST['aadhaar_number']=='' || $_POST['address_proof_type']=='' || $_POST['address_proof_doc_no']=='' || $_POST['business_name']=='' || $_POST['business_address']=='' || $_POST['business_type']=='') {
		$error = 1;
	} else {
		
		$kyc_type = trim(htmlentities(addslashes($_POST['kyc_type']),ENT_QUOTES));
		$kyc_id = trim(htmlentities(addslashes($_POST['kyc_id']),ENT_QUOTES));
		
		$firstname = htmlentities(addslashes($_POST['firstname']),ENT_QUOTES);
		$middlename = htmlentities(addslashes($_POST['middlename']),ENT_QUOTES);
		$lastname = htmlentities(addslashes($_POST['lastname']),ENT_QUOTES);
		$fathername = htmlentities(addslashes($_POST['fathername']),ENT_QUOTES);
		$mothername = htmlentities(addslashes($_POST['mothername']),ENT_QUOTES);
		$dob = htmlentities(addslashes($_POST['dob']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$email = htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$permanent_address = htmlentities(addslashes($_POST['permanent_address']),ENT_QUOTES);
		$correspondence_address = htmlentities(addslashes($_POST['correspondence_address']),ENT_QUOTES);
		$city = htmlentities(addslashes($_POST['city']),ENT_QUOTES);
		$pincode = htmlentities(addslashes($_POST['pincode']),ENT_QUOTES);
		$pancard_number = htmlentities(addslashes($_POST['pancard_number']),ENT_QUOTES);
		$aadhaar_card_number = htmlentities(addslashes($_POST['aadhaar_number']),ENT_QUOTES);
		$address_proof_doc_no = htmlentities(addslashes($_POST['address_proof_doc_no']),ENT_QUOTES);
		$business_name = htmlentities(addslashes($_POST['business_name']),ENT_QUOTES);
		$business_address = htmlentities(addslashes($_POST['business_address']),ENT_QUOTES);
		$business_type_detail = htmlentities(addslashes($_POST['business_type_detail']),ENT_QUOTES);
		$passport = htmlentities(addslashes($_FILES['passport']['name']),ENT_QUOTES);
		$pancard = htmlentities(addslashes($_FILES['pancard']['name']),ENT_QUOTES);
		$addressproof1 = htmlentities(addslashes($_FILES['addressproof1']['name']),ENT_QUOTES);
		$addressproof2 = htmlentities(addslashes($_FILES['addressproof2']['name']),ENT_QUOTES);
		
		/*
		* Upload Files
		*/
		$file_types = array('.jpg','.gif','.bmp','.png','.jpeg');
		$max_filesize = 2097152; // Maximum filesize in BYTES (currently 2MB).	
		$uploads = "../uploads/kyc";
		
		$pp_img='';
		$ext_pp=substr($_FILES['passport']['name'], strpos($_FILES['passport']['name'],'.'), strlen($_FILES['passport']['name'])-1);
		if($passport!='' && filesize($_FILES['passport']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_pp), $file_types)) {
			$pp_img = $request_id."_".$passport;
			@move_uploaded_file($_FILES['passport']['tmp_name'], $uploads."/".$pp_img);
		}
		
		$pc_img = '';
		$ext_pc = substr($_FILES['pancard']['name'], strpos($_FILES['pancard']['name'],'.'), strlen($_FILES['pancard']['name'])-1);
		if($pancard!='' && filesize($_FILES['pancard']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_pc), $file_types)) {
			$pc_img = $request_id."_".$pancard;
			@move_uploaded_file($_FILES['pancard']['tmp_name'], $uploads."/".$pc_img);
		}
		
		$ap1_img = '';
		$ext_ap1 = substr($_FILES['addressproof1']['name'], strpos($_FILES['addressproof1']['name'],'.'), strlen($_FILES['addressproof1']['name'])-1);
		if($addressproof1!='' && filesize($_FILES['addressproof1']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap1), $file_types)) {
			$ap1_img = $request_id."_".$addressproof1;
			@move_uploaded_file($_FILES['addressproof1']['tmp_name'], $uploads."/".$ap1_img);
		}
		
		$ap2_img = '';
		$ext_ap2 = substr($_FILES['addressproof2']['name'], strpos($_FILES['addressproof2']['name'],'.'), strlen($_FILES['addressproof2']['name'])-1);
		if($addressproof2!='' && filesize($_FILES['addressproof2']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap2), $file_types)) {
			$ap2_img = $request_id."_".$addressproof2;
			@move_uploaded_file($_FILES['addressproof2']['tmp_name'], $uploads."/".$ap2_img);
		}
		
		if($kyc_type=='add') {
			$db->execute("INSERT INTO `apps_user_kyc`(`kyc_id`, `user_id`, `firstname`, `middlename`, `lastname`, `fathername`, `mothername`, `dob`, `gender`, `mobile`, `phone`, `email`, `permanent_address`, `correspondence_address`, `city`, `pincode`, `pancard_number`, `aadhaar_card_number`, `address_proof_type`, `address_proof_doc_no`, `business_name`, `business_address`, `business_type`, `business_type_detail`, `passport`, `pancard`, `addressproof1`, `addressproof2`, `added_date`, `added_by`) VALUES ('', '".$request_id."', '".$firstname."', '".$middlename."', '".$lastname."', '".$fathername."', '".$mothername."', '".$dob."', '".$_POST['gender']."', '".$mobile."', '".$phone."', '".$email."', '".$permanent_address."', '".$correspondence_address."', '".$city."', '".$pincode."', '".$pancard_number."', '".$aadhaar_card_number."', '".$_POST['address_proof_type']."', '".$address_proof_doc_no."', '".$business_name."', '".$business_address."', '".$_POST['business_type']."', '".$business_type_detail."', '".$pp_img."', '".$pc_img."', '".$ap1_img."', '".$ap2_img."', NOW(), '".$aMaster->uid."')");
			$db->execute("UPDATE `apps_user` SET `is_kyc`='y' WHERE user_id='".$request_id."' ");
			header("location:kyc.php?token=".$token."&error=3");	
			exit();		
		} else {
			$db->execute("UPDATE `apps_user_kyc` SET `firstname`='".$firstname."', `middlename`='".$middlename."', `lastname`='".$lastname."', `fathername`='".$fathername."', `mothername`='".$mothername."', `dob`='".$dob."', `gender`='".$_POST['gender']."', `mobile`='".$mobile."', `phone`='".$phone."', `email`='".$email."', `permanent_address`='".$permanent_address."', `correspondence_address`='".$correspondence_address."', `city`='".$city."', `pincode`='".$pincode."', `pancard_number`='".$pancard_number."', `aadhaar_card_number`='".$aadhaar_card_number."', `address_proof_type`='".$_POST['address_proof_type']."', `address_proof_doc_no`='".$address_proof_doc_no."', `business_name`='".$business_name."', `business_address`='".$business_address."', `business_type`='".$_POST['business_type']."', `business_type_detail`='".$business_type_detail."', `update_date`=NOW(), `update_by`='".$aMaster->uid."', `is_edit`='n' WHERE kyc_id='".$kyc_id."' ");
			
			if($pp_img!='') {
				$db->execute("UPDATE `apps_user_kyc` SET `passport`='".$pp_img."' WHERE kyc_id='".$kyc_id."' ");
			}
						
			if($pc_img!='') {
				$db->execute("UPDATE `apps_user_kyc` SET `pancard`='".$pc_img."' WHERE kyc_id='".$kyc_id."' ");
			}
			
			if($ap1_img!='') {
				$db->execute("UPDATE `apps_user_kyc` SET `addressproof1`='".$ap1_img."' WHERE kyc_id='".$kyc_id."' ");
			}
			
			if($ap2_img!='') {
				$db->execute("UPDATE `apps_user_kyc` SET `addressproof2`='".$ap2_img."' WHERE kyc_id='".$kyc_id."' ");
			}	
			
			$db->execute("UPDATE `apps_user` SET `is_kyc`='y' WHERE user_id='".$request_id."' ");
			
			header("location:kyc.php?token=".$token."&error=3");	
			exit();					
		}
	}	
}

$kyc = $db->queryUniqueObject("SELECT * FROM apps_user_kyc WHERE user_id='".$aMaster->user_id."' ");
if($kyc) {
	
	if($kyc->is_edit=='n') {
		header("location:kyc.php?token=".$token);	
		exit();		
	}
	
	$kyc_type = "edit";
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
	$aadhaar_card_number = $kyc->aadhaar_card_number;
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
	$fathername = "";
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
	$aadhaar_card_number = "";
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
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script> 
<script>
jQuery(document).ready(function(){
	jQuery('#dob').datepicker({
		format: 'dd-mm-yyyy'
	});
	
	jQuery("#business_type").change(function() {
		if(this.value == '0') {
			jQuery("#business_type_other").css('display','');	
		} else {
			jQuery("#business_type_other").css('display','none');	
		}
	});
	
	jQuery("#same_as_above").click(function() {
		var per_add = jQuery("#permanent_address").val();
		jQuery("#correspondence_address").val(per_add);
	});
	
	jQuery("#declare").click(function() {
		var thisCheck = jQuery(this);
		if(thisCheck.is(':checked')) {
   		jQuery('#submit').prop("disabled", false);
		} else {
      jQuery("#submit").prop("disabled", true);
    }
	});
	
	jQuery.validator.addMethod('filesize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param) 
	}, "Filesize is not more than 2MB");
	
	jQuery('#kycForm').validate({
		submitHandler : function(form) {
			if(confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
	  rules: {
	    firstname: {
	    	required: true
	    },
			lastname: {
	    	required: true
	    },
			mothername: {
	    	required: true
	    },
			dob: {
	    	required: true
	    },
			gender: {
	    	required: true
	    },
			mobile: {
	    	required: true
	    },
			email: {
	    	required: true
	    },
			permanent_address: {
	    	required: true
	    },
			correspondence_address: {
	    	required: true
	    },
			city: {
	    	required: true
	    },
			pincode: {
	    	required: true
	    },
			pancard_number: {
	    	required: true
	    },
			address_proof_type: {
	    	required: true
	    },
			address_proof_doc_no: {
	    	required: true
	    },
			business_name: {
	    	required: true
	    },
			business_address: {
	    	required: true
	    },
			business_type: {
	    	required: true
	    },
			business_type_detail: {
	    	required: true
	    },
			passport: {
	    	required: false,
				accept: "png|jpe?g|gif",
				filesize: 2097152
	    },
			pancard: {
	    	required: false,
				accept: "png|jpe?g|gif",
				filesize: 2097152
	    },
			addressproof1: {
	    	required: false,
				accept: "png|jpe?g|gif",
				filesize: 2097152
	    },
			addressproof2: {
	    	required: false,
				accept: "png|jpe?g|gif",
				filesize: 2097152
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
			<div class="page-title">My Account <small>/ KYC From</small></div>
			<div class="pull-right">
				<a href="kyc.php?token=<?php echo $token;?>" class="btn btn-primary"><i class="fa fa-user"></i> KYC</a>
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Please update your detail to submit KYC Form</h3>
					</div>
					<form action="" method="post" id="kycForm" class="form-horizontal" enctype="multipart/form-data">
					<input type="hidden" name="kyc_type" id="kyc_type" value="<?php echo $kyc_type;?>" />
					<input type="hidden" name="kyc_id" id="kyc_id" value="<?php echo $kyc_id;?>" />
					<div class="box-body padding-50 kyc-form">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-sm-4">
										<label>First Name</label>
										<div class="jrequired">
										<input type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>" class="form-control" placeholder="FIRST NAME">
										</div>
									</div>
									<div class="col-sm-4">
										<label>Middle Name</label>
										<div class="jrequired">
										<input type="text" name="middlename" id="middlename" value="<?php echo $middlename;?>" class="form-control" placeholder="MIDDLE NAME">
										</div>
									</div>
									<div class="col-sm-4">
										<label>Last Name</label>
										<div class="jrequired">
										<input type="text" name="lastname" id="lastname" value="<?php echo $lastname;?>" class="form-control" placeholder="LAST NAME">
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>Father Name</label>
										<div class="jrequired">
										<input type="text" name="fathername" id="fathername" value="<?php echo $fathername;?>" class="form-control" placeholder="FATHER NAME">
										</div>
									</div>
									<div class="col-sm-4">
										<label>Mother's Maiden Name</label>
										<div class="jrequired">
										<input type="text" name="mothername" id="mothername" value="<?php echo $mothername;?>" class="form-control" placeholder="MOTHER MAIDEN NAME">
										</div>
									</div>
									<div class="col-sm-2">
										<label>Date Of Birth</label>
										<div class="jrequired">
										<input type="text" name="dob" id="dob" value="<?php echo $dob;?>" class="form-control" placeholder="DATE OF BIRTH">
										</div>
									</div>
									<div class="col-sm-2">
										<label>Gender</label>
										<div class="jrequired">
										<select name="gender" id="gender" class="form-control">
											<option value=""></option>
											<option value="male" <?php if($gender=='male'){?>selected="selected"<?php } ?>>Male</option>
											<option value="female" <?php if($gender=='female'){?>selected="selected"<?php } ?>>Female</option>
										</select>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-3">
										<label>Mobile Number</label>
										<div class="jrequired">
										<input type="text" name="mobile" id="mobile" value="<?php echo $mobile;?>" class="form-control" placeholder="MOBILE NUMBER">
										</div>
									</div>
									<div class="col-sm-3">
										<label>Phone Number</label>
										<div class="jrequired">
										<input type="text" name="phone" id="phone" value="<?php echo $phone;?>" class="form-control" placeholder="PHONE NUMBER">
										</div>
									</div>
									<div class="col-sm-6">
										<label>Email</label>
										<div class="jrequired">
										<input type="text" name="email" id="email" value="<?php echo $email;?>" class="form-control" placeholder="EMAIL">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-6">
										<label>Permanent Address</label>
										<div class="jrequired">
										<textarea name="permanent_address" id="permanent_address" class="form-control" placeholder="PERMANENT ADDRESS" rows="3"><?php echo $permanent_address;?></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<label>Correspondence Address</label>
										<div class="jrequired">
										<textarea name="correspondence_address" id="correspondence_address" class="form-control" placeholder="CORRESPONDENCE ADDRESS" rows="3"><?php echo $correspondence_address;?></textarea>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label><br /><br /></label>
										<input type="checkbox" name="same_as_above" id="same_as_above"> Same as permenent address
									</div>
									<div class="col-sm-6">
										<label>City</label>
										<div class="jrequired">
										<input type="text" name="city" id="city" value="<?php echo $city;?>" class="form-control" placeholder="CITY">
										</div>
									</div>
									<div class="col-sm-2">
										<label>Pincode</label>
										<div class="jrequired">
										<input type="text" name="pincode" id="pincode" value="<?php echo $pincode;?>" class="form-control" placeholder="PINCODE">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<label>Pancard Number</label>
										<div class="jrequired">
										<input type="text" name="pancard_number" id="pancard_number" value="<?php echo $pancard_number;?>" class="form-control" placeholder="PANCARD NUMBER">
										</div>
									</div>
									<div class="col-sm-3">
										<label>Aadhaar Number</label>
										<div class="jrequired">
										<input type="text" name="aadhaar_number" id="aadhaar_number" value="<?php echo $aadhaar_card_number;?>" class="form-control" placeholder="AADHAAR NUMBER">
										</div>
									</div>
									<div class="col-sm-3">
										<label>Address Proof Doc Type</label>
										<div class="jrequired">
										<select name="address_proof_type" id="address_proof_type" class="form-control">
											<option value=""></option>											
											<option value='driving' <?php if($address_proof_type=='driving'){?>selected="selected"<?php } ?>>Driving License</option>
											<option value='passport' <?php if($address_proof_type=='passport'){?>selected="selected"<?php } ?>>Passport</option>
											<option value='voterid' <?php if($address_proof_type=='voterid'){?>selected="selected"<?php } ?>>Election ID Card</option> 
										</select>
										</div>
									</div>
									<div class="col-sm-3">
										<label>Address Proof Doc Ref No</label>
										<div class="jrequired">
										<input type="text" name="address_proof_doc_no" id="address_proof_doc_no" value="<?php echo $address_proof_doc_no;?>" class="form-control" placeholder="DOCUMENT NUMBER">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12"><h4>Business Details</h4></div>
								</div>
								<div class="form-group">									
									<div class="col-sm-6">
										<label>Business Type</label>
										<div class="jrequired">
										<select name="business_type" id="business_type" class="form-control">
											<option value=''>Select</option>
											<option value='1' <?php if($business_type=='1'){?>selected="selected"<?php } ?>>Retail Shop</option>
											<option value='2' <?php if($business_type=='2'){?>selected="selected"<?php } ?>>Super Market</option>
											<option value='3' <?php if($business_type=='3'){?>selected="selected"<?php } ?>>IT Company</option>
											<option value='4' <?php if($business_type=='4'){?>selected="selected"<?php } ?>>Courier</option>
											<option value='5' <?php if($business_type=='5'){?>selected="selected"<?php } ?>>Medical Shop</option>
											<option value='6' <?php if($business_type=='6'){?>selected="selected"<?php } ?>>Mobile Repairing</option>
											<option value='7' <?php if($business_type=='7'){?>selected="selected"<?php } ?>>Provision Store</option>
											<option value='8' <?php if($business_type=='8'){?>selected="selected"<?php } ?>>Mobile Showrooms</option>
											<option value='9' <?php if($business_type=='9'){?>selected="selected"<?php } ?>>PAN Shop</option>
											<option value='0' <?php if($business_type=='0'){?>selected="selected"<?php } ?>>Others</option>
										</select>
										</div>
									</div>
									<div class="col-sm-6" id="business_type_other" <?php if($business_type=='0'){?>style="display:block;"<?php } else {?>style="display:none;"<?php } ?>>
										<label>Other Business Specify</label>
										<div class="jrequired">
										<input type="text" name="business_type_detail" id="business_type_detail" value="<?php echo $business_type_detail;?>" class="form-control" placeholder="BUSINESS TYPE SPECIFY">
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-6">
										<label>Business Name</label>
										<div class="jrequired">
										<input type="text" name="business_name" id="business_name" value="<?php echo $business_name;?>" class="form-control" placeholder="BUSINESS NAME">
										</div>
									</div>
									<div class="col-sm-6">
										<label>Bussiness Address</label>
										<div class="jrequired">
										<textarea name="business_address" id="business_address" class="form-control" placeholder="BUSINESS ADDRESS" rows="3"><?php echo $business_address;?></textarea>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-12">
										<h4>Upload Documents</h4>
										<b class="text-red">Only jpg, jpeg, png, gif file allowed, Maximum upload Size upto: 2MB</b>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Photograph</label>
									<div class="col-sm-8">
										<div class="jrequired">
											<input type="file" name="passport" id="passport">
											<?php if($passport!='' && file_exists("../uploads/kyc/".$passport)) { ?>
											<a href="<?php echo "../uploads/kyc/".$passport;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$passport;?>" class="img-kyc" /></a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Pancard</label>
									<div class="col-sm-8">
										<div class="jrequired">
											<input type="file" name="pancard" id="pancard">
											<?php if($pancard!='' && file_exists("../uploads/kyc/".$pancard)) { ?>
											<a href="<?php echo "../uploads/kyc/".$pancard;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$pancard;?>" class="img-kyc" /></a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Aadhaar Card</label>
									<div class="col-sm-8">
										<div class="jrequired">
											<input type="file" name="addressproof1" id="addressproof1">
											<?php if($addressproof1!='' && file_exists("../uploads/kyc/".$addressproof1)) { ?>
											<a href="<?php echo "../uploads/kyc/".$addressproof1;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof1;?>" class="img-kyc" /></a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3">Address Proof (1)</label>
									<div class="col-sm-8">
										<div class="jrequired">
											<input type="file" name="addressproof2" id="addressproof2">
											<?php if($addressproof2!='' && file_exists("../uploads/kyc/".$addressproof2)) { ?>
											<a href="<?php echo "../uploads/kyc/".$addressproof2;?>" target="_blank"><img src="<?php echo "../uploads/kyc/".$addressproof2;?>" class="img-kyc" /></a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<input type="checkbox" name="declare" id="declare"> Declaration/घोषणा
									</div>
									<div class="col-sm-12 margin-top-10">
										<p>मैं एतद् द्वारा घोषणा करता/करती हूं कि मेरे द्वारा के वाय सी फार्म में भरी गई समस्त जानकारियां पूर्णतः सत्य हैं। यदि कोई जानकारी गलत पाई जाती है और उस आधार पर मेरी सदस्यता निरस्त की जाती है तो उसकी समस्त जवाबदारी स्वयं मेरी होगी।</p>
										<p>It is hereby declared that the information and particulars furnished above are true and correct to the best of my/our knowledge and if found incorrect information and account will be terminated that is my solo resposibility.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" disabled="disabled" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
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
<?php include("footer.php");?>