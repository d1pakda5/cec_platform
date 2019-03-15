<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$requestid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;

// Fetch KYC
$kyc = $db->queryUniqueObject("SELECT * FROM userskyc WHERE id='".$requestid."' ");
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
	header("location:rpt-kyc.php");	
	exit();
}

// Fetch User
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$kyc->uid."' ");
if(!$user) {
	header("location:rpt-kyc.php");	
	exit();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>KYC-<?php echo $uid;?></title>
<link rel="stylesheet" href="../css/bootstrap.css">

<style>
body {
	font-size:12px;
}
</style>
</head>
<body>
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12" style="padding-top:50px;">
				<h4>KNOW YOUR CUSTOMER (KYC)</h4>
			</div>
			<div class="col-sm-12">
				<table class="table table-bordered">
					<tr>
						<td colspan="4"><strong>CUSTOMER DETAIL</strong></td>
					</tr>
					<tr>
						<td width="18%">UID</td>
						<td><?php echo $uid;?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td width="18%">FULL NAME</td>
						<td width="32%"><?php echo $firstname;?></td>
						<td width="18%"><?php echo $middlename;?></td>
						<td width="32%"><?php echo $lastname;?></td>
					</tr>
					<tr>
						<td>FATHERS NAME</td>
						<td><?php echo $fathersname;?></td>
						<td>MOTHERS NAME</td>
						<td><?php echo $mothersname;?></td>
					</tr>
					<tr>
						<td>DATE OF BIRTH</td>
						<td><?php echo $dob;?></td>
						<td>GENDER</td>
						<td><?php echo $gender;?></td>
					</tr>
					<tr>
						<td>MOBILE</td>
						<td><?php echo $mobile;?></td>
						<td>PHONE</td>
						<td><?php echo $phone;?></td>
					</tr>
					<tr>
						<td>FAX</td>
						<td><?php echo $fax;?></td>
						<td>EMAIL</td>
						<td><?php echo $email;?></td>
					</tr>
					<tr>
						<td>PERMANENT ADD</td>
						<td><?php echo $addressperm;?></td>
						<td>CORRESP. ADD</td>
						<td><?php echo $addresscorres;?></td>
					</tr>
					<tr>
						<td>CITY</td>
						<td><?php echo $city;?></td>
						<td>STATE</td>
						<td><?php echo $state;?></td>
					</tr>
					<tr>
						<td>PINCODE</td>
						<td><?php echo $pincode;?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td>PAN NO</td>
						<td><?php echo $pancard;?></td>
						<td>COMPANY TYPE</td>
						<td><?php echo strtoupper($companytype);?></td>
					</tr>
					<tr>
						<td>GSTIN</td>
						<td><?php echo $gstin;?></td>
						<td>AADHAAR NO</td>
						<td><?php echo $aadhaar;?></td>
					</tr>
					<tr>
						<td>REF. DOC TYPE</td>
						<td><?php echo strtoupper($adrprooftype);?></td>
						<td>REF. DOC NO</td>
						<td><?php echo $adrprooftypeno;?></td>
					</tr>
				</table>
				<table class="table table-bordered">
					<tr>
						<td colspan="4"><strong>BUSSINESS DETAIL</strong></td>
					</tr>
					<tr>
						<td width="18%">BUSINESS NAME</td>
						<td width="32%"><?php echo strtoupper($businessname);?></td>
						<td width="18%">BUSINESS TYPE</td>
						<td width="32%">
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
						</td>
					</tr>
					<tr>
						<td>BUSINESS ADDRESS</td>
						<td colspan="3"><?php echo $businessaddress;?></td>
					</tr>
				</table>				
			</div>
		</div>
	</div>
	<div class="container-fluid text-center" style="margin-bottom:30px;">
		<a href="javascript:window.print()" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
	</div>
</div>
</body>
</html>