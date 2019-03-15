<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
if(!isset($_GET['token']) || $_GET['token']!=$token) {
	exit("Token not match");
}
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$request_id = $_SESSION['apiuser'];
$_uid = $_SESSION['apiuser_uid'];

if(isset($_POST['submit'])) {	
	if($_POST['firstname']=='' || $_POST['lastname']=='' || $_POST['fathersname']=='' || $_POST['mothersname']=='' || $_POST['dob']=='' || $_POST['gender']=='' || $_POST['email']=='' || $_POST['mobile']=='' || $_POST['addressperm']=='' || $_POST['addresscorres']=='' || $_POST['city']=='' || $_POST['state']=='' || $_POST['pincode']=='' || $_POST['pancard']=='' || $_POST['adrprooftype']=='' || $_POST['adrprooftypeno']=='' || $_POST['businessname']=='' || $_POST['businessaddress']=='' || $_POST['businesstype']=='') {
		$error = 1;
	} else {
		
		$action = trim(htmlentities(addslashes($_POST['action']),ENT_QUOTES));
		$uid = trim(htmlentities(addslashes($_POST['uid']),ENT_QUOTES));		
		$firstname = htmlentities(addslashes($_POST['firstname']),ENT_QUOTES);
		$middlename = htmlentities(addslashes($_POST['middlename']),ENT_QUOTES);
		$lastname = htmlentities(addslashes($_POST['lastname']),ENT_QUOTES);
		$fathersname = htmlentities(addslashes($_POST['fathersname']),ENT_QUOTES);
		$mothersname = htmlentities(addslashes($_POST['fathersname']),ENT_QUOTES);
		$dob = htmlentities(addslashes($_POST['dob']),ENT_QUOTES);
		$email = htmlentities(addslashes($_POST['email']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		$phone = htmlentities(addslashes($_POST['phone']),ENT_QUOTES);
		$fax = htmlentities(addslashes($_POST['fax']),ENT_QUOTES);
		$addressperm = htmlentities(addslashes($_POST['addressperm']),ENT_QUOTES);
		$addresscorres = htmlentities(addslashes($_POST['addresscorres']),ENT_QUOTES);
		$city = htmlentities(addslashes($_POST['city']),ENT_QUOTES);
		$pincode = htmlentities(addslashes($_POST['pincode']),ENT_QUOTES);
		$pancard = htmlentities(addslashes($_POST['pancard']),ENT_QUOTES);
		$aadhaar = htmlentities(addslashes($_POST['aadhaar']),ENT_QUOTES);
		$gstin = htmlentities(addslashes($_POST['gstin']),ENT_QUOTES);
		$adrprooftypeno = htmlentities(addslashes($_POST['adrprooftypeno']),ENT_QUOTES);
		$businessname = htmlentities(addslashes($_POST['businessname']),ENT_QUOTES);
		$businessaddress = htmlentities(addslashes($_POST['businessaddress']),ENT_QUOTES);
		$businesstypename = htmlentities(addslashes($_POST['businesstypename']),ENT_QUOTES);
		$photofile = $_FILES['photofile']['name'];
		$pancardfile = $_FILES['pancardfile']['name'];
		$aadhaarfile = $_FILES['aadhaarfile']['name'];
		$addressfile = $_FILES['addressfile']['name'];
		
		/*
		* Upload Files
		*/
		$file_types = array('.jpg','.gif','.png','.jpeg');
		$max_filesize = 2097152; // Maximum filesize in BYTES (currently 2MB).		
		$foldr = $uid;
		$path = "../kycdocs/";
		$foldrpath = $path.$foldr;
		chmod($path,0777);
		if(!is_dir($foldrpath)) {
			mkdir($foldrpath, 0777);
		}
		
		
		$ext_pp = substr($photofile, strpos($photofile,'.'), strlen($photofile)-1);
		if(photofile!='' && filesize($_FILES['photofile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_pp), $file_types)) {
			$photofile = "photo-".$uid."-".$photofile;
			@move_uploaded_file($_FILES['photofile']['tmp_name'], $foldrpath."/".$photofile);
			$photofile = substr($foldrpath."/".$photofile,3);
		}
		
		
		$ext_pc = substr($pancardfile, strpos($pancardfile,'.'), strlen($pancardfile)-1);
		if($pancardfile!='' && filesize($_FILES['pancardfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_pc), $file_types)) {
			$pancardfile = "pancard-".$uid."-".$pancardfile;
			@move_uploaded_file($_FILES['pancardfile']['tmp_name'], $foldrpath."/".$pancardfile);
			$pancardfile = substr($foldrpath."/".$pancardfile,3);
		}
		
		
		$ext_ap1 = substr($aadhaarfile, strpos($aadhaarfile,'.'), strlen($aadhaarfile)-1);
		if($aadhaarfile!='' && filesize($_FILES['aadhaarfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap1), $file_types)) {
			$aadhaarfile = "aadhaar-".$uid."-".$aadhaarfile;
			@move_uploaded_file($_FILES['aadhaarfile']['tmp_name'], $foldrpath."/".$aadhaarfile);
			$aadhaarfile = substr($foldrpath."/".$aadhaarfile,3);
		}
		
		
		$ext_ap2 = substr($addressfile, strpos($addressfile,'.'), strlen($addressfile)-1);
		if($addressfile!='' && filesize($_FILES['addressfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap2), $file_types)) {
			$addressfile = "address-".$uid."-".$addressfile;
			@move_uploaded_file($_FILES['addressfile']['tmp_name'], $foldrpath."/".$addressfile);
			$addressfile = substr($foldrpath."/".$addressfile,3);
		}
		
		if($action=='add') {
			$db->execute("INSERT INTO `userskyc`(`uid`, `firstname`, `middlename`, `lastname`, `fathersname`, `mothersname`, `dob`, `gender`, `mobile`, `phone`, `fax`, `email`, `addressperm`, `addresscorres`, `city`, `state`, `pincode`, `pancard`, `companytype`, `aadhaar`, `gstin`, `adrprooftype`, `adrprooftypeno`, `businessname`, `businessaddress`, `businesstype`, `businesstypename`, `photofile`, `pancardfile`, `aadhaarfile`, `addressfile`, `submitdate`, `submitedby`, `status`) VALUES ('".$uid."', '".$firstname."', '".$middlename."', '".$lastname."', '".$fathersname."', '".$mothersname."', '".$dob."', '".$_POST['gender']."', '".$mobile."', '".$phone."', '".$fax."', '".$email."', '".$addressperm."', '".$addresscorres."', '".$city."', '".$_POST['state']."', '".$pincode."', '".$pancard."', '".$_POST['companytype']."', '".$aadhaar."', '".$gstin."', '".$_POST['adrprooftype']."', '".$adrprooftypeno."', '".$businessname."', '".$businessaddress."', '".$_POST['businesstype']."', '".$businesstypename."', '".$photofile."', '".$pancardfile."', '".$aadhaarfile."', '".$addressfile."', NOW(), '".$uid."', '0')");
			$db->execute("UPDATE `apps_user` SET `is_kyc`='1' WHERE uid='".$uid."' ");
			
				
		} else {
			
			$db->execute("UPDATE `userskyc` SET `firstname`='".$firstname."', `middlename`='".$middlename."', `lastname`='".$lastname."', `fathersname`='".$fathersname."', `mothersname`='".$mothersname."', `dob`='".$dob."', `gender`='".$_POST['gender']."', `mobile`='".$mobile."', `phone`='".$phone."', `fax`='".$fax."', `email`='".$email."', `addressperm`='".$addressperm."', `addresscorres`='".$addresscorres."', `city`='".$city."', `state`='".$_POST['state']."', `pincode`='".$pincode."', `pancard`='".$pancard."', `companytype`='".$_POST['companytype']."', `aadhaar`='".$aadhaar."', `gstin`='".$gstin."', `adrprooftype`='".$_POST['adrprooftype']."', `adrprooftypeno`='".$adrprooftypeno."', `businessname`='".$businessname."', `businessaddress`='".$businessaddress."', `businesstype`='".$_POST['businesstype']."', `businesstypename`='".$businesstypename."', `updatedate`=NOW(), `updatedby`='".$uid."', `canedit`='0', `status`='0' WHERE uid='".$uid."' ");
			
			if($photofile!='') {
				$db->execute("UPDATE `userskyc` SET `photofile`='".$photofile."' WHERE uid='".$uid."' ");
			}
						
			if($pancardfile!='') {
				$db->execute("UPDATE `userskyc` SET `pancardfile`='".$pancardfile."' WHERE uid='".$uid."' ");
			}
			
			if($aadhaarfile!='') {
				$db->execute("UPDATE `userskyc` SET `aadhaarfile`='".$aadhaarfile."' WHERE uid='".$uid."' ");
			}
			
			if($addressfile!='') {
				$db->execute("UPDATE `userskyc` SET `addressfile`='".$addressfile."' WHERE uid='".$uid."' ");
			}	
			
			$db->execute("UPDATE `apps_user` SET `is_kyc`='1' WHERE uid='".$uid."' ");
		}
		
		header("location:kyc.php?token=".$token."&error=3");	
		exit();
	}
}
// Fetch User
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$_uid."' ");
// Fetch KYC
$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE uid='".$_uid."' ");
if($kyc) {
	$action = "edit";
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
} else {
	$action = "add";
	$id = "";
	$uid = $_uid;
	$firstname = "";
	$middlename = "";
	$lastname = "";
	$fathersname = "";
	$mothersname = "";
	$dob = "";
	$gender = "";
	$mobile = $user->mobile;
	$phone = $user->phone;
	$fax = "";
	$email = $user->email;
	$addressperm = $user->address;
	$addresscorres = "";
	$city = $user->city;
	$state = $user->states;
	$pincode = $user->pincode;
	$pancard = $user->panno;
	$companytype = "";
	$aadhaar = $user->aadharno;
	$gstin = $user->gstin;
	$adrprooftype = "";
	$adrprooftypeno = "";
	$businessname = $user->company_name;
	$businessaddress = "";
	$businesstype = "";
	$businesstypename = "";
	$photofile = "";
	$pancardfile = "";
	$aadhaarfile = "";
	$addressfile = "";
}
$meta['title'] = "KYC";
include("header.php");
?>
<style>
.kyc-form {
	background:#fbfcfe;
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
.img-kyc {
	border:1px solid #ddd;
	padding:5px;
	margin-top:15px;
	width:150px;
}
.text-error {
	color:#FF0000;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<link href="../js/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<script src="../js/fileinput/fileinput.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function(){
	jQuery('#dob').datepicker({
		format: 'dd-mm-yyyy'
	});
	jQuery("#businesstype").change(function() {
		if(this.value == '0') {
			jQuery("#businesstypeother").css('display','');	
		} else {
			jQuery("#businesstypeother").css('display','none');
		}
	});	
	jQuery("#same_as_above").click(function() {
		var per_add = jQuery("#addressperm").val();
		jQuery("#addresscorres").val(per_add);
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
	  rules: {
			uid: {required: true},
	    firstname: {required: true},
			lastname: {required: true},
			fathersname: {required: true},
			mothersname: {required: true},
			dob: {required: true},
			gender: {required: true},
			email: {required: true},
			mobile: {required: true},
			addressperm: {required: true},
			addresscorres: {required: true},
			city: {required: true},
			state: {required: true},
			pincode: {required: true},
			pancard: {required: true},
			aadhaar: {required: true},
			adrprooftype: {required: true},
			adrprooftypeno: {required: true},
			businessname: {required: true},
			businessaddress: {required: true},
			businesstype: {required: true},
			<?php if($photofile!='') {?>
			photofile: {required: false,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } else { ?>
			photofile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } ?>
			<?php if($pancardfile!='') {?>
			pancardfile: {required: false,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } else { ?>
			pancardfile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } ?>
			<?php if($aadhaarfile!='') {?>
			aadhaarfile: {required: false,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } else { ?>
			aadhaarfile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } ?>
			<?php if($addressfile!='') {?>
			addressfile: {required: false,accept: "png|jpe?g|gif",filesize: 2097152}
			<?php } else { ?>
			addressfile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			<?php } ?>
	  },
		highlight: function(element) {
			jQuery(element).closest('div[class^="col-sm-"]').addClass('text-error');
		},
		unhighlight: function (element, errorClass) {
      jQuery(element).closest('div[class^="col-sm-"]').removeClass('text-error');
    },
	});
});
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Account <small>/ KYC</small></div>
			<div class="pull-right">
				<a href="profile.php?token=<?php echo $token;?>" class="btn btn-primary"><i class="fa fa-user"></i> Profile</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Your KYC has been submitted successfully, Our team will verify your KYC soon.
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
					<form action="" method="post" id="kycForm" enctype="multipart/form-data">
					<input type="hidden" name="action" id="action" value="<?php echo $action;?>" />
					<div class="box-body no-padding kyc-form">
						<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<div class="col-sm-4">
										<label>UID</label>
										<input type="text" name="uid" id="uid" readonly="" value="<?php echo $uid;?>" class="form-control" placeholder="Enter UID" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>First Name</label>
										<input type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>" class="form-control" placeholder="Enter first name" />
									</div>
									<div class="col-sm-4">
										<label>Middle Name</label>
										<input type="text" name="middlename" id="middlename" value="<?php echo $middlename;?>" class="form-control" placeholder="Enter middle name" />
									</div>
									<div class="col-sm-4">
										<label>Last Name</label>
										<input type="text" name="lastname" id="lastname" value="<?php echo $lastname;?>" class="form-control" placeholder="Enter last name" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-6">
										<label>Father Name</label>
										<input type="text" name="fathersname" id="fathersname" value="<?php echo $fathersname;?>" class="form-control" placeholder="Enter fathers name" />
									</div>
									<div class="col-sm-6">
										<label>Mother's Maiden Name</label>
										<input type="text" name="mothersname" id="mothersname" value="<?php echo $mothersname;?>" class="form-control" placeholder="Enter mothers name" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<label>Date Of Birth</label>
										<input type="text" name="dob" id="dob" value="<?php echo $dob;?>" class="form-control" placeholder="Enter date of birth" />
									</div>
									<div class="col-sm-3">
										<label>Gender</label>
										<select name="gender" id="gender" class="form-control">
											<option value="">Select</option>
											<option value="male" <?php if($gender=='male'){?>selected="selected"<?php } ?>>Male</option>
											<option value="female" <?php if($gender=='female'){?>selected="selected"<?php } ?>>Female</option>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Email</label>
										<input type="text" name="email" id="email" value="<?php echo $email;?>" class="form-control" placeholder="Enter email" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>Mobile Number</label>
										<input type="text" name="mobile" id="mobile" value="<?php echo $mobile;?>" class="form-control" placeholder="Enter mobile" />
									</div>
									<div class="col-sm-4">
										<label>Phone Number</label>
										<input type="text" name="phone" id="phone" value="<?php echo $phone;?>" class="form-control" placeholder="Enter phone" />
									</div>
									<div class="col-sm-4">
										<label>Fax Number</label>
										<input type="text" name="fax" id="fax" value="<?php echo $fax;?>" class="form-control" placeholder="Enter fax" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-6">
										<label>Permanent Address</label>
										<textarea name="addressperm" id="addressperm" class="form-control" placeholder="Permanent address"><?php echo $addressperm;?></textarea>
									</div>
									<div class="col-sm-6">
										<label>Correspondence Address</label>
										<textarea name="addresscorres" id="addresscorres" class="form-control" placeholder="Correspondence address"><?php echo $addresscorres;?></textarea>
										<p class="form-control-static"><input type="checkbox" name="same_as_above" id="same_as_above"> Same as permenent address</p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>City</label>
										<input type="text" name="city" id="city" value="<?php echo $city;?>" class="form-control" placeholder="Enter city" />
									</div>
									<div class="col-sm-5">
										<label>States</label>
										<select name="state" id="state" class="form-control">
											<option value="">Select</option>
											<?php $qry = $db->query("SELECT * FROM states ORDER BY states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>" <?php if($state==$rlt->states) {?>selected="selected"<?php } ?>><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-3">
										<label>Pincode</label>
										<input type="text" name="pincode" id="pincode" value="<?php echo $pincode;?>" class="form-control" placeholder="Enter pin code" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>Pancard Number</label>
										<input type="text" name="pancard" id="pancard" value="<?php echo $pancard;?>" class="form-control" placeholder="Enter pancard number" />
									</div>
									<div class="col-sm-4">
										<label>Company Type</label>
										<select name="companytype" id="companytype" class="form-control">
											<option value=''>Select</option>
											<option value='Private Ltd'<?php if($companytype=='Private Ltd'){?> selected="selected"<?php } ?>>Private Ltd</option>
											<option value='Public Ltd'<?php if($companytype=='Public Ltd'){?> selected="selected"<?php } ?>>Public Ltd</option>
											<option value='One Person Company'<?php if($companytype=='One Person Company'){?> selected="selected"<?php } ?>>One Person Company</option>
											<option value='LLP'<?php if($companytype=='LLP'){?> selected="selected"<?php } ?>>LLP</option>
											<option value='Proprietorship'<?php if($companytype=='Proprietorship'){?> selected="selected"<?php } ?>>Proprietorship</option>
											<option value='Partnership'<?php if($companytype=='Partnership'){?> selected="selected"<?php } ?>>Partnership</option>
											<option value='NGO'<?php if($companytype=='NGO'){?> selected="selected"<?php } ?>>NGO</option>
											<option value='Society'<?php if($companytype=='Society'){?> selected="selected"<?php } ?>>Society</option>
										</select>
									</div>
									<div class="col-sm-4">
										<label>Aadhaar Number</label>
										<input type="text" name="aadhaar" id="aadhaar" value="<?php echo $aadhaar;?>" class="form-control" placeholder="Enter aadhaar number" />
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-4">
										<label>GSTIN ID</label>
										<input type="text" name="gstin" id="gstin" value="<?php echo $gstin;?>" class="form-control" placeholder="Enter your GST number" />
									</div>
									<div class="col-sm-4">
										<label>Address Proof Document Type</label>
										<select name="adrprooftype" id="adrprooftype" class="form-control">
											<option value="">Select</option>											
											<option value='driving'<?php if($adrprooftype=='driving'){?> selected="selected"<?php } ?>>Driving License</option>
											<option value='passport'<?php if($adrprooftype=='passport'){?> selected="selected"<?php } ?>>Passport</option>
											<option value='voterid'<?php if($adrprooftype=='voterid'){?> selected="selected"<?php } ?>>Election ID Card</option> 
										</select>
									</div>
									<div class="col-sm-4">
										<label>Document Ref No Details</label>
										<div class="jrequired">
										<input type="text" name="adrprooftypeno" id="adrprooftypeno" value="<?php echo $adrprooftypeno;?>" class="form-control" placeholder="Enter ref no">
										</div>
									</div>
								</div>
							</div>
							<!-- start of business details -->
							<div class="col-md-4">
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business Name</label>
										<input type="text" name="businessname" id="businessname" value="<?php echo $businessname;?>" class="form-control" placeholder="Enter business name">
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business address</label>
										<textarea name="businessaddress" id="businessaddress" class="form-control" placeholder="Business address"><?php echo $businessaddress;?></textarea>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-12">								
										<label>Business Type</label>
										<select name="businesstype" id="businesstype" class="form-control">
											<option value=''>Select</option>
											<option value='1' <?php if($businesstype=='1'){?>selected="selected"<?php } ?>>Retail Shop</option>
											<option value='2' <?php if($businesstype=='2'){?>selected="selected"<?php } ?>>Super Market</option>
											<option value='3' <?php if($businesstype=='3'){?>selected="selected"<?php } ?>>IT Company</option>
											<option value='4' <?php if($businesstype=='4'){?>selected="selected"<?php } ?>>Courier</option>
											<option value='5' <?php if($businesstype=='5'){?>selected="selected"<?php } ?>>Medical Shop</option>
											<option value='6' <?php if($businesstype=='6'){?>selected="selected"<?php } ?>>Mobile Repairing</option>
											<option value='7' <?php if($businesstype=='7'){?>selected="selected"<?php } ?>>Provision Store</option>
											<option value='8' <?php if($businesstype=='8'){?>selected="selected"<?php } ?>>Mobile Showrooms</option>
											<option value='9' <?php if($businesstype=='9'){?>selected="selected"<?php } ?>>PAN Shop</option>
											<option value='0' <?php if($businesstype=='0'){?>selected="selected"<?php } ?>>Others</option>
										</select>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-12" id="businesstypeother"<?php if($businesstype=='0'){?> style="display:block;"<?php } else {?> style="display:none;"<?php } ?>>
										<label>Other Business Specify</label>
										<input type="text" name="businesstypename" id="businesstypename" value="<?php echo $businesstypename;?>" class="form-control" placeholder="Enter business detail" />
									</div>
								</div>
							</div>
							<!-- end of business details -->
							
							<!-- start of attachments -->
							<div class="col-md-12">
								<div class="col-sm-12" style="margin-top:30px; margin-bottom:15px;">
									<label><strong>Upload Documents [ <span class="text-red">jpg, jpeg, png, gif</span> ] Maximum file size <span class="text-red">2MB</span></strong></label>
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<label>Photograph</label>
										<input type="file" name="photofile" id="photoFile">
									</div>
									<div class="col-sm-3">
										<label>Pancard</label>
										<input type="file" name="pancardfile" id="pancardFile">
									</div>
									<div class="col-sm-3">
										<label>Aadhaar Card</label>
										<input type="file" name="aadhaarfile" id="aadhaarFile">
									</div>
									<div class="col-sm-3">
										<label>Address Proof</label>
										<input type="file" name="addressfile" id="addressFile">
									</div>
								</div>
							</div>
							<!-- end of attachments -->
							<div class="col-md-12" style="margin-bottom:40px;">
								<div class="form-group">
									<div class="col-md-12">
										<label>
											<input type="checkbox" name="declare" id="declare"> Declaration/घोषणा
										</label>
									</div>
									<div class="col-sm-12 margin-top-20">
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
								<button type="submit" name="submit" id="submit" disabled="disabled" class="btn btn-success btn-lg">Submit KYC</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$("#photoFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	<?php if($photofile!='') { ?>
	initialPreviewAsData: true,
	initialPreview: [
			"../<?php echo $photofile;?>"
	]
	<?php } else { ?>
	initialPreviewAsData: false
	<?php } ?>
});
$("#pancardFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	<?php if($pancardfile!='') { ?>
	initialPreviewAsData: true,
	initialPreview: [
			"../<?php echo $pancardfile;?>"
	]
	<?php } else { ?>
	initialPreviewAsData: false
	<?php } ?>
});
$("#aadhaarFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	<?php if($aadhaarfile!='') { ?>
	initialPreviewAsData: true,
	initialPreview: [
			"../<?php echo $aadhaarfile;?>"
	]
	<?php } else { ?>
	initialPreviewAsData: false
	<?php } ?>
});
$("#addressFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	<?php if($addressfile!='') { ?>
	initialPreviewAsData: true,
	initialPreview: [
			"../<?php echo $addressfile;?>"
	]
	<?php } else { ?>
	initialPreviewAsData: false
	<?php } ?>
});
</script>
<?php include("footer.php");?>