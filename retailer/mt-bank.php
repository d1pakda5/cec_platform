<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$meta['title'] = "Money Transfer";
include('header.php');
?>
<style>
.btn-sm {
	padding:2px;
}
.boxBenRegisterForm {
	display:none;
}
</style>
<script type="text/javascript" src="../js/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert.css">
<script type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#btnBank').click(function () {
		var mobile = jQuery('#mobile').val();
		var bankName = jQuery('#bankName').val();
		var branchName = jQuery('#branchName').val();
		if(mobile=="" || bankName=="" || branchName=="") {
			if(mobile=="") {
				swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
			} else if(bankName=="") {
				swal({ title: "Error", text: "Bank name cannot be blank", type: "error"});
			} else if(branchName=="") {
				swal({ title: "Error", text: "Branch name cannot be blank", type: "error"});
			} 
		} else {
			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif", showConfirmButton: false});
			jQuery.ajax({ 
				url	: "../dmt/get-bank-details.php",
				type	: "GET",
				data	: "mobile="+mobile+"&bank_name="+bankName+"&branch_name="+branchName,
				success	: function(j) {
					jQuery("#boxBankInfo").html(j);
					swal.close();
				}
			});
		}
	});
});
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">S-Paisa <small>/ Money Transfer</small></div>
		</div>
		<div class="row min-height-480">
			<?php if($aRetailer->is_money == 'i') {?>
			<div class="col-sm-12">
				<div class="alert alert-danger">
					<b><i class="fa fa-minus-circle"></i> This service is inactive in your account. To activate money transfer service in your account contact your distributor now.</b>
				</div>
			</div>
			<?php } else { ?>
			<div class="col-sm-12">
				<!-- Start of Customer Validation -->
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Money Transfer</h3>
					</div>
					<div class="box-body">	
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-2 control-label">Customer :</label>
								<div class="col-sm-4">
									<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Bank Name :</label>
								<div class="col-sm-4">
									<input type="text" name="bank_name" id="bankName" class="form-control" placeholder="Enter Bank Name" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Branch Name :</label>
								<div class="col-sm-4">
									<input type="text" name="branch_name" id="branchName" class="form-control" placeholder="Enter Branch Name" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-3 col-sm-offset-2">
									<button type="submit" id="btnBank" class="btn btn-success">Fetch</button>
								</div>
							</div>							
						</div>					
					</div>
				</div>
				<!-- End of Customer Validation -->
				
				<!-- Start of Customer Information -->
				<div id="boxBankInfo">		
				</div>
				<!-- End of Customer Information -->
				
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>