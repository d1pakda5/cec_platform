<?php
session_start();
if(!isset($_SESSION['mdistributor'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");

$requestid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

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
			$photofile = "photo-".$uid."-".getCleanFile($photofile);
			@move_uploaded_file($_FILES['photofile']['tmp_name'], $foldrpath."/".$photofile);
			$photofile = substr($foldrpath."/".$photofile,3);
		}
		
		
		$ext_pc = substr($pancardfile, strpos($pancardfile,'.'), strlen($pancardfile)-1);
		if($pancardfile!='' && filesize($_FILES['pancardfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_pc), $file_types)) {
			$pancardfile = "pancard-".$uid."-".getCleanFile($pancardfile);
			@move_uploaded_file($_FILES['pancardfile']['tmp_name'], $foldrpath."/".$pancardfile);
			$pancardfile = substr($foldrpath."/".$pancardfile,3);
		}
		
		
		$ext_ap1 = substr($aadhaarfile, strpos($aadhaarfile,'.'), strlen($aadhaarfile)-1);
		if($aadhaarfile!='' && filesize($_FILES['aadhaarfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap1), $file_types)) {
			$aadhaarfile = "aadhaar-".$uid."-".getCleanFile($aadhaarfile);
			@move_uploaded_file($_FILES['aadhaarfile']['tmp_name'], $foldrpath."/".$aadhaarfile);
			$aadhaarfile = substr($foldrpath."/".$aadhaarfile,3);
		}
		
		
		$ext_ap2 = substr($addressfile, strpos($addressfile,'.'), strlen($addressfile)-1);
		if($addressfile!='' && filesize($_FILES['addressfile']['tmp_name']) <= $max_filesize && in_array(strtolower($ext_ap2), $file_types)) {
			$addressfile = "address-".$uid."-".getCleanFile($addressfile);
			@move_uploaded_file($_FILES['addressfile']['tmp_name'], $foldrpath."/".$addressfile);
			$addressfile = substr($foldrpath."/".$addressfile,3);
		}
		
		$db->execute("INSERT INTO `userskyc`(`uid`, `firstname`, `middlename`, `lastname`, `fathersname`, `mothersname`, `dob`, `gender`, `mobile`, `phone`, `fax`, `email`, `addressperm`, `addresscorres`, `city`, `state`, `pincode`, `pancard`, `companytype`, `aadhaar`, `gstin`, `adrprooftype`, `adrprooftypeno`, `businessname`, `businessaddress`, `businesstype`, `businesstypename`, `photofile`, `pancardfile`, `aadhaarfile`, `addressfile`, `submitdate`, `submitedby`, `status`) VALUES ('".$uid."', '".$firstname."', '".$middlename."', '".$lastname."', '".$fathersname."', '".$mothersname."', '".$dob."', '".$_POST['gender']."', '".$mobile."', '".$phone."', '".$fax."', '".$email."', '".$addressperm."', '".$addresscorres."', '".$city."', '".$_POST['state']."', '".$pincode."', '".$pancard."', '".$_POST['companytype']."', '".$aadhaar."', '".$gstin."', '".$_POST['adrprooftype']."', '".$adrprooftypeno."', '".$businessname."', '".$businessaddress."', '".$_POST['businesstype']."', '".$businesstypename."', '".$photofile."', '".$pancardfile."', '".$aadhaarfile."', '".$addressfile."', NOW(), '".$uid."', '0')");
		$db->execute("UPDATE `apps_user` SET `is_kyc`='1' WHERE uid='".$uid."' ");		
		header("location:userkyc.php?uid=".$uid."&error=3");	
		exit();
	}
}

// Fetch User
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$requestid."' ");
if(!$user) {
	header("location:index.php");	
	exit();
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
			photofile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			pancardfile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152},
			aadhaarfile: {required: false,accept: "png|jpe?g|gif",filesize: 2097152},
			addressfile: {required: true,accept: "png|jpe?g|gif",filesize: 2097152}
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
			<div class="page-title">KYC <small>/ Add</small></div>
			<div class="pull-right">
				<a href="view-user-profile.php?id=<?php echo $user->user_id;?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to Profile</a>
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
					<div class="box-body no-padding kyc-form">
						<div class="row">
							<div class="col-md-8">
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>UID</label>
										<input type="text" name="uid" id="uid" readonly="" value="<?php echo $user->uid;?>" class="form-control" placeholder="Enter UID" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>First Name</label>
										<input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter first name" />
									</div>
									<div class="col-sm-4">
										<label>Middle Name</label>
										<input type="text" name="middlename" id="middlename" class="form-control" placeholder="Enter middle name" />
									</div>
									<div class="col-sm-4">
										<label>Last Name</label>
										<input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter last name" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-6">
										<label>Father Name</label>
										<input type="text" name="fathersname" id="fathersname" class="form-control" placeholder="Enter fathers name" />
									</div>
									<div class="col-sm-6">
										<label>Mother's Maiden Name</label>
										<input type="text" name="mothersname" id="mothersname" class="form-control" placeholder="Enter mothers name" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-3">
										<label>Date Of Birth</label>
										<input type="text" name="dob" id="dob" class="form-control" placeholder="Enter date of birth" />
									</div>
									<div class="col-sm-3">
										<label>Gender</label>
										<select name="gender" id="gender" class="form-control">
											<option value="">Select</option>
											<option value="male">Male</option>
											<option value="female">Female</option>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Email</label>
										<input type="text" name="email" id="email" class="form-control" placeholder="Enter email" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>Mobile Number</label>
										<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter mobile" />
									</div>
									<div class="col-sm-4">
										<label>Phone Number</label>
										<input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone" />
									</div>
									<div class="col-sm-4">
										<label>Fax Number</label>
										<input type="text" name="fax" id="fax" class="form-control" placeholder="Enter fax" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-6">
										<label>Permanent Address</label>
										<textarea name="addressperm" id="addressperm" class="form-control" placeholder="Permanent address"></textarea>
									</div>
									<div class="col-sm-6">
										<label>Correspondence Address</label>
										<textarea name="addresscorres" id="addresscorres" class="form-control" placeholder="Correspondence address"></textarea>
										<p class="form-control-static"><input type="checkbox" name="same_as_above" id="same_as_above"> Same as permenent address</p>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>City</label>
										<input type="text" name="city" id="city" class="form-control" placeholder="Enter city" />
									</div>
									<div class="col-sm-5">
										<label>States</label>
										<select name="state" id="state" class="form-control">
											<option value="">Select</option>
											<?php $qry = $db->query("SELECT * FROM states ORDER BY states");
											while($rlt = $db->fetchNextObject($qry)) { ?>
											<option value="<?php echo $rlt->states;?>"><?php echo $rlt->states;?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-3">
										<label>Pincode</label>
										<input type="text" name="pincode" id="pincode" class="form-control" placeholder="Enter pin code" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>Pancard Number</label>
										<input type="text" name="pancard" id="pancard" class="form-control" placeholder="Enter pancard number" />
									</div>
									<div class="col-sm-4">
										<label>Company Type</label>
										<select name="companytype" id="companytype" class="form-control">
											<option value=''>Select</option>
											<option value='Private Ltd'>Private Ltd</option>
											<option value='Public Ltd'>Public Ltd</option>
											<option value='One Person Company'>One Person Company</option>
											<option value='LLP'>LLP</option>
											<option value='Proprietorship'>Proprietorship</option>
											<option value='Partnership'>Partnership</option>
											<option value='NGO'>NGO</option>
											<option value='Society'>Society</option>
										</select>
									</div>
									<div class="col-sm-4">
										<label>Aadhaar Number</label>
										<input type="text" name="aadhaar" id="aadhaar" class="form-control" placeholder="Enter aadhaar number" />
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-sm-4">
										<label>GSTIN ID</label>
										<input type="text" name="gstin" id="gstin" class="form-control" placeholder="Enter your GST number" />
									</div>
									<div class="col-sm-4">
										<label>Address Proof Document Type</label>
										<select name="adrprooftype" id="adrprooftype" class="form-control">
											<option value="">Select</option>											
											<option value='driving'>Driving License</option>
											<option value='passport'>Passport</option>
											<option value='voterid'>Election ID Card</option> 
										</select>
									</div>
									<div class="col-sm-4">
										<label>Document Ref No Details</label>
										<div class="jrequired">
										<input type="text" name="adrprooftypeno" id="adrprooftypeno" class="form-control" placeholder="Enter ref no">
										</div>
									</div>
								</div>
							</div>
							
							<!-- start of business details -->
							<div class="col-md-4">
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business Name</label>
										<input type="text" name="businessname" id="businessname" class="form-control" placeholder="Enter business name">
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<label>Business address</label>
										<textarea name="businessaddress" id="businessaddress" class="form-control" placeholder="Business address"></textarea>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-12">								
										<label>Business Type</label>
										<select name="businesstype" id="businesstype" class="form-control">
											<option value=''>Select</option>
											<option value='1'>Retail Shop</option>
											<option value='2'>Super Market</option>
											<option value='3'>IT Company</option>
											<option value='4'>Courier</option>
											<option value='5'>Medical Shop</option>
											<option value='6'>Mobile Repairing</option>
											<option value='7'>Provision Store</option>
											<option value='8'>Mobile Showrooms</option>
											<option value='9'>PAN Shop</option>
											<option value='0'>Others</option>
										</select>
									</div>
								</div>
								<div class="form-group">	
									<div class="col-sm-12" id="businesstypeother">
										<label>Other Business Specify</label>
										<input type="text" name="businesstypename" id="businesstypename" class="form-control" placeholder="Enter business detail" />
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
											<input type="checkbox" name="declare" id="declare"> I have checked all the document of KYC
										</label>
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
	initialPreviewAsData: false
});
$("#pancardFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	initialPreviewAsData: false
});
$("#aadhaarFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	initialPreviewAsData: false
});
$("#addressFile").fileinput({
	showUpload: false,
	showCaption: false,
	browseClass: "btn btn-default",
	allowedFileExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
	overwriteInitial: false,
	initialPreviewAsData: false
});
</script>
<?php include("footer.php");?>