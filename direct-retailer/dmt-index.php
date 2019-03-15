<script type="text/javascript" src="../js/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert.css">
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#btnSenderValidate").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
		} else {
			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif", showConfirmButton: false});
			jQuery.ajax({ 
				url		: "../dmt-library/dmt-request.php",
				type	: "GET",
				data	: "test=0&request=senderValidation&mobile="+mobile,
				async : false,
				success	: function(data) {
					var obj = jQuery.parseJSON(data);
					
					if(obj["ResponseCode"]=='0') {
						jQuery.get("dmt/list-beneficiary.php",{js_data: data, ajax: 'true'}, function(jd){			
							jQuery("#boxBeneficiaryList").html(jd);
						});
						swal.close();
					} else if (obj["ResponseCode"]=='1') {
						jQuery("#smobile").val(obj["MobileNo"]);
						jQuery("#boxSenderRegistration").show();
						jQuery("#boxResponseJson").html(data);
						swal.close();
					} else {
						swal({ title: "Error", text: obj["Message"], type: "error"});
					}
				}
			});
		}
	});
	
	jQuery("#findIfsc").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
		} else {
			
		}
	}
});

function getBalance() {
	//Get Senders balance
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif", showConfirmButton: false});
		jQuery.ajax({ 
			url		: "../dmt-library/dmt-request.php",
			type	: "GET",
			data	: "test=0&request=senderBalance&mobile="+mobile,
			async : false,
			success	: function(data) {
				var obj = jQuery.parseJSON(data);
				if(obj["ResponseCode"]=='0') {					
					jQuery("#senderWalletBalance").html(obj["Balance"]);
					swal.close();
				} else if (obj["ResponseCode"]=='1') {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				} else {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				}
			}
		});
	}
}

function getTransaction() {
	//Get Senders balance
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif"});
		jQuery("#boxBeneficiaryList").hide();
		jQuery("#boxBeneficiaryForm").hide();
		jQuery.ajax({ 
			url		: "../dmt-library/dmt-request.php",
			type	: "GET",
			data	: "test=0&request=senderTransaction&mobile="+mobile,
			async : false,
			success	: function(data) {
				var obj = jQuery.parseJSON(data);
				console.log(obj);
				if(obj["ResponseCode"]=='0') {
					jQuery.get("dmt/list-transaction.php",{js_data: data, ajax: 'true'}, function(jd){
						jQuery("#boxSenderTransaction").show();			
						jQuery("#boxSenderTransaction").html(jd);
					});
					swal.close();
				} else if (obj["ResponseCode"]=='1') {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				} else {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				}
			}
		});
	}
}

function getBackBeneList() {
	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif"});
	jQuery("#boxSenderTransaction").hide();
	jQuery("#boxBeneficiaryForm").hide();
	jQuery("#boxBeneficiaryList").show();
	swal.close();
}

function getBeneficiaryForm() {
	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/hourglass.gif"});
	jQuery("#boxSenderTransaction").hide();
	jQuery("#boxBeneficiaryList").hide();
	jQuery("#boxBeneficiaryForm").show();
	swal.close();
}
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Money Transfer</div>
		</div>
		<div class="row">
			<?php
			if($aRetailer->is_money=='i') {?>
			<div class="col-sm-12 min-height-480">
				<?php include("dmt-activation.php");?>
			</div>
			<?php } else { ?>
			<div class="col-sm-12 min-height-480">
				<!-- Sender Validation -->
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
									<button type="submit" id="btnSenderValidate" class="btn btn-success">Validate</button>
								</div>
							</div>							
						</div>					
					</div>
				</div>
				
				<!-- start response dump -->
				<div id="boxResponseJson">
				</div>
				<!-- end of response dump -->
				
				<!-- start sender registration -->
				<div id="boxSenderRegistration" style="display:none;">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Sender</h3>
						</div>
						<div class="box-body">	
							<form id="frmSenderRegister" class="form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">First Name :</label>
									<div class="col-sm-5">
										<input type="text" name="fname" id="fname" class="form-control" placeholder="Enter First Name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Last Name :</label>
									<div class="col-sm-5">
										<input type="text" name="lname" id="lname" class="form-control" placeholder="Enter Last Name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Mobile Number :</label>
									<div class="col-sm-5">
										<input type="text" name="smobile" id="smobile" class="form-control" readonly="" placeholder="Enter Mobile Number" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">&nbsp;</label>
									<div class="col-sm-5">
										<input type="submit" name="submit" id="btnSenderRegister" class="btn btn-success" />
									</div>
								</div>							
							</form>
						</div>
					</div>				
				</div>
				<!-- end of sender registration -->
				
				<!-- start beneficiary add -->
				<div id="boxBeneficiaryForm" style="display:none;">
					<div class="dmt box">
						<div class="box-header">
							<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Beneficiary</h3>
							<div class="pull-right">
								<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-success">Back</a>
							</div>
						</div>
						<div class="box-body">	
							<form id="frmBeneficiaryRegister" class="form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">Beneficiary Name :</label>
									<div class="col-sm-5">
										<input type="text" name="ben_name" id="ben_name" class="form-control" placeholder="Enter Beneficiary Name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Bank Account :</label>
									<div class="col-sm-5">
										<input type="text" name="bank_account" id="bank_account" class="form-control" placeholder="Enter Bank Account Number" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">IFSC :</label>
									<div class="col-sm-5">
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="ifsc" id="ifsc" class="form-control" placeholder="Enter IFSC Code" />
											</div>
											<div class="col-sm-4">
												<button type="text" name="findIfsc" id="findIfsc" class="btn btn-block btn-default">Find IFSC</button>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">&nbsp;</label>
									<div class="col-sm-5">
										<input type="submit" name="submit" id="btnSenderRegister" class="btn btn-success" />
									</div>
								</div>							
							</form>
						</div>
					</div>					
				</div>
				<!-- end of beneficiary add -->
				
				<!-- start beneficiary list -->
				<div id="boxBeneficiaryList">					
				</div>
				<!-- end of beneficiary list -->
				
				<!-- start response dump -->
				<div id="boxSenderTransaction">
				</div>
				<!-- end of response dump -->
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>