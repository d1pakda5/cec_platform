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
</style>
<script type="text/javascript" src="../js/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert.css">
<script type="text/javascript">
function senderValidate() {
	var mobile = $('#mobile').val();
	if(mobile == "") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif", showConfirmButton: false});
		jQuery.ajax({ 
			url	: "../dmt/sender-validation.php",
			type	: "GET",
			data	: "mobile="+mobile,
			success	: function(j) {
				jQuery("#boxFetchBen").html(j);
				swal.close();
				//if(data=='1') {
	//				jQuery.get("../dmt/fetch-beneficiary.php",{dt: data, ajax: 'true'}, function(j){			
	//					jQuery("#boxFetchBen").html(j);
	//				});
	//			} else if(data=='2') {
	//				jQuery.get("../dmt/sender-registration.php",{mobile: mobile, ajax: 'true'}, function(j){			
	//					jQuery("#boxSenderReg").html(j);
	//				});
	//			} else if(data=='3') {
	//				jQuery("#boxError").html(data);
	//			} else if(data=='4') {
	//				jQuery("#boxError").html(data);
	//			} else if(data=='5') {
	//				jQuery("#boxError").html(data);
	//			} else {
	//				jQuery("#boxError").html("Some error try after some time.");
	//			}
			}
		});	
	}
}
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">S-Paisa <small>/ Money Transfer</small></div>
		</div>
		<div class="row min-height-480">
			<?php
			if($aRetailer->is_money == 'i') {?>
			<div class="col-sm-12">
				<div class="alert alert-danger">
					<b><i class="fa fa-minus-circle"></i> This service is inactive in your account. To activate money transfer service in your account contact your distributor now.</b>
				</div>
			</div>
			<?php } else { ?>
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Money Transfer</h3>
					</div>
					<div class="box-body">	
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-2 control-label">Sender :</label>
								<div class="col-sm-4">
									<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" />
								</div>
								<div class="col-sm-3">
									<button type="submit" onclick="senderValidate()" id="btnValidate" class="btn btn-success">Validate</button>
								</div>
							</div>							
						</div>					
					</div>
				</div>
				<div id="boxError">
				</div>
				<div id="boxSenderReg">					
				</div>
				<div id="boxFetchBen">					
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>