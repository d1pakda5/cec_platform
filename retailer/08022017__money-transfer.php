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
<script type="text/javascript" src="../js/sweetalert.min.js"></script>
<link rel="stylesheet" href="../css/sweetalert.css">
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#submitSenderValidate").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=senderValidation&mobile="+mobile,
					async : false,
					success	: function(data) {
						var obj = jQuery.parseJSON(data);					
						if(obj["ResponseCode"]=='0') {
							jQuery.get("dmt/list-beneficiary.php",{js_data: data, ajax: 'true'}, function(jd){
								jQuery("#boxSenderRegistration").hide();	
								jQuery("#boxBeneficiaryForm").hide();
								jQuery("#boxBeneficiaryList").show();		
								jQuery("#boxBeneficiaryList").html(jd);
							});
							swal.close();							
						} else if (obj["ResponseCode"]=='1') {							
							jQuery("#smobile").val(obj["MobileNo"]);
							jQuery("#boxSenderRegistration").show();
							swal.close();
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	//Sender Registration
	jQuery("#submitSenderRegister").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var fname = jQuery('#fname').val().trim();
		var lname = jQuery('#lname').val().trim();
		if(mobile=="" || fname=="" || lname=="") {
			swal({ title: "Error", text: "Mobile/First/Last cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=senderRegistration&mobile="+mobile+"&fname="+fname+"&lname="+lname,
					async : false,
					success	: function(data) {
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal.close();
							jQuery('#otc_ref_no').val(obj["RequestNo"]);
							jQuery('#otc_code').val('');
							jQuery('#modalOtcConfirm').modal('show');							
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	//Beneficiary Registration
	jQuery("#submitBeneficiaryRegister").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var ben_name = jQuery('#ben_name').val().trim();
		var ben_account = jQuery('#ben_account').val().trim();
		var ifsc = jQuery('#ben_ifsc').val().trim();
		if(mobile=="" || ben_name=="" || ben_account=="" || ifsc=="") {
			swal({ title: "Error", text: "Mobile/Name/Account/IFSC cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=beneficiaryAdd&mobile="+mobile+"&ben_name="+ben_name+"&ben_account="+ben_account+"&ifsc="+ifsc,
					async : false,
					success	: function(data) {
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal.close();
							jQuery('#otc_ref_no').val(obj["RequestNo"]);
							jQuery('#otc_code').val('');
							jQuery('#modalOtcConfirm').modal('show');							
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	jQuery("#submitOtcConfirm").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var otc_ref_no = jQuery('#otc_ref_no').val().trim();
		var otc_code = jQuery('#otc_code').val().trim();
		if(mobile=="" || otc_ref_no=="" || otc_code=="") {
			swal({ title: "Error", text: "Mobile/OTC reference number cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Invalid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=otcConfirm&mobile="+mobile+"&otc_ref_no="+otc_ref_no+"&otc="+otc_code,
					async : false,
					success	: function(data) {
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {						
							swal({ title: "Success", text: obj["Message"], type: "success"});
							jQuery('#modalOtcConfirm').modal('hide');
							window.location.reload();
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	jQuery("#submitOtcResend").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var otc_ref_no = jQuery('#otc_ref_no').val().trim();
		if(mobile=="" || otc_ref_no=="") {
			swal({ title: "Error", text: "Mobile/OTC reference number cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Invalid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=otcResend&mobile="+mobile+"&otc_ref_no="+otc_ref_no,
					async : false,
					success	: function(data) {
						console.log(data);
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal.close();
							jQuery('#otc_ref_no').val(obj["RequestNo"]);
							jQuery('#otc_code').val('');
							jQuery('#modalOtcConfirm').modal('show');							
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	jQuery("#findIfsc").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
		} else {
			jQuery('#modalIfsc').modal('show');
		}
	});
	
	jQuery("#submitIfscSearch").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var bank = jQuery('#bank_name').val().trim();
		var branch = jQuery('#branch_name').val().trim();
		if(mobile=="" || bank=="" || branch=="") {
			swal({ title: "Error", text: "Mobile/Bank/Branch cannot be blank", type: "error"});
		} else {
			if(bank.length <= '3' || branch.length <= '3') {
				swal({ title: "Error", text: "Bank or Branch name required min 4 charcter", type: "error"});
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=bankIfsc&mobile="+mobile+"&bank="+bank+"&branch="+branch,
					async : false,
					success	: function(data) {
						swal.close();
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							jQuery.get("dmt/list-ifsc.php",{js_data: data, ajax: 'true'}, function(jd){	
								jQuery("#modalIfscResult .modal-body").html(jd);
								jQuery('#modalIfscResult').modal('show');
							});							
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	jQuery("#submitMoneyRemittanceX").click(function() {
		//mr_uid=20000082&mr_ben_name=SamirK&mr_ben_code=FIcEW&mr_ben_account=100703130020262&mr_ben_ifsc=SVCB0007007&mr_ben_type=IMPS&mr_amount=2000
		alert( jQuery("#frmMoneyRemittance").serialize());
	});
	
	jQuery("#submitMoneyRemittance").click(function() {		
		var mobile = jQuery('#mobile').val().trim();
		var mr_uid = jQuery('#mr_uid').val().trim();
		var mr_ben_name = jQuery('#mr_ben_name').val().trim();
		var mr_ben_code = jQuery('#mr_ben_code').val().trim();
		var mr_ben_account = jQuery('#mr_ben_account').val().trim();
		var mr_ben_ifsc = jQuery('#mr_ben_ifsc').val().trim();
		var mr_ben_type = jQuery("input[name='mr_ben_type']").val().trim();
		var mr_amount = jQuery('#mr_amount').val().trim();
		if(mobile=="" || mr_uid=="" || mr_ben_name=="" || mr_ben_code=="" || mr_ben_account=="" || mr_ben_ifsc=="" || mr_ben_type=="" || mr_amount=="") {
			swal({ title: "Error", text: "Mobile/Bank/Branch cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Invalid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
				jQuery('#modalRemittance').modal('hide');
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=moneyRemittance&mobile="+mobile+"&mr_uid="+mr_uid+"&mr_ben_name="+mr_ben_name+"&mr_ben_code="+mr_ben_code+"&mr_ben_account="+mr_ben_account+"&mr_ben_ifsc="+mr_ben_ifsc+"&mr_ben_type="+mr_ben_type+"&mr_amount="+mr_amount,
					async : false,
					success	: function(data) {
						jQuery("#frmMoneyRemittance").trigger('reset');	
						//swal.close();
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal({ title: "Success", text: obj["Message"], type: "error"});					
						} else if (obj["ResponseCode"]=='1') {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
							swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	
	//Modal Testing
	jQuery("#testOtcModal").click(function() {
		jQuery('#modalRemittance').modal('show');
	});
	
});

function getBalance() {
	//Get Senders balance
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
		jQuery.ajax({ 
			url		: "process-spaisa.php",
			type	: "GET",
			data	: "request=senderBalance&mobile="+mobile,
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
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
		jQuery("#boxBeneficiaryList").hide();
		jQuery("#boxBeneficiaryForm").hide();
		jQuery.ajax({ 
			url		: "process-spaisa.php",
			type	: "GET",
			data	: "request=senderTransaction&mobile="+mobile,
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
	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
	jQuery("#boxSenderTransaction").hide();
	jQuery("#boxBeneficiaryForm").hide();
	jQuery("#boxBeneficiaryList").show();
	swal.close();
}

function getBeneficiaryForm() {
	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
	jQuery("#boxSenderTransaction").hide();
	jQuery("#boxBeneficiaryList").hide();
	jQuery("#boxBeneficiaryForm").show();
	swal.close();
}

function benDelete(ben_code, ben_ifsc) {
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="" || ben_code=="" || ben_ifsc=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
		jQuery.ajax({ 
			url		: "process-spaisa.php",
			type	: "GET",
			data	: "request=beneficiaryDelete&mobile="+mobile+"&ifsc="+ben_ifsc+"&ben_code="+ben_code,
			async : false,
			success	: function(data) {
				var obj = jQuery.parseJSON(data);
				console.log(obj);
				if(obj["ResponseCode"]=='0') {
					swal.close();
					jQuery('#otc_ref_no').val(obj["RequestNo"]);
					jQuery('#otc_code').val('');
					jQuery('#modalOtcConfirm').modal('show');
				} else if (obj["ResponseCode"]=='1') {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				} else {
					swal({ title: "Error", text: obj["Message"], type: "error"});
				}
			}
		});
	}
}

function benValidate(ben_type, ben_code, ben_ifsc, uid) {
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="" || ben_type=="" || ben_code=="" || ben_ifsc=="" || uid=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({
			title: "Are you sure?",
			text: "Do you really want to validate beneficiary account!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes, Confirm!",
			closeOnConfirm: false
		},
		function(){
			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
			jQuery.ajax({ 
				url		: "process-spaisa.php",
				type	: "GET",
				data	: "request=beneficiaryValidate&mobile="+mobile+"&ben_type="+ben_type+"&ifsc="+ben_ifsc+"&ben_code="+ben_code+"&uid="+uid,
				async : false,
				success	: function(data) {
					var obj = jQuery.parseJSON(data);
					//console.log(obj);
					if(obj["ResponseCode"]=='0') {
						swal({ title: "Success", text: obj["Message"], type: "success"});
					} else if (obj["ResponseCode"]=='1') {
						swal({ title: "Error", text: obj["Message"], type: "error"});
					} else {
						swal({ title: "Error", text: obj["Message"], type: "error"});
					}
				}
			});
		});
	}
}
function benRemittance(ben_name,ben_type,ben_code,ben_account,ben_account_type,ben_ifsc,uid) {
	jQuery('#mr_ben_name').val(ben_name);
	jQuery('#mr_ben_type').val(ben_type);
	jQuery('#mr_ben_code').val(ben_code);
	jQuery('#mr_ben_account').val(ben_account);
	jQuery('#mr_ben_account_type').val(ben_account_type);
	jQuery('#mr_ben_ifsc').val(ben_ifsc);
	jQuery('#mr_uid').val(uid);
	jQuery('#modalRemittance').modal('show');
}
function enableButton(code) {
	jQuery('a[id^=rem_]').addClass('disabled');
	jQuery('a[id^=val_]').addClass('disabled');
	jQuery('a[id^=del_]').addClass('disabled');
	jQuery('#rem_'+code).removeClass('disabled');
	jQuery('#val_'+code).removeClass('disabled');
	jQuery('#del_'+code).removeClass('disabled');	
}
function insertIfsc(ifsc) {
	//alert(ifsc);
	jQuery("#boxBeneficiaryForm #ben_ifsc").val(ifsc);
	jQuery('#modalIfscResult').modal('hide');
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
									<button type="submit" id="submitSenderValidate" class="btn btn-success">Validate</button>
									<!--<button type="button" id="testOtcModal" class="btn btn-primary">Confirm</button>-->
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
							<h3 class="box-title"><i class="fa fa-user"></i> Add Sender</h3>
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
										<button type="button" id="submitSenderRegister" class="btn btn-primary" data-dismiss="modal">Register</button>
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
										<input type="text" name="ben_account" id="ben_account" class="form-control" placeholder="Enter Bank Account Number" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">IFSC :</label>
									<div class="col-sm-5">
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="ben_ifsc" id="ben_ifsc" class="form-control" placeholder="Enter IFSC Code" />
											</div>
											<div class="col-sm-4">
												<button type="button" name="findIfsc" id="findIfsc" class="btn btn-block btn-default" data-backdrop="static" data-keyboard="false">Find IFSC</button>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">&nbsp;</label>
									<div class="col-sm-5">
										<button type="button" id="submitBeneficiaryRegister" class="btn btn-primary">Submit</button>
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
<div class="modal fade" id="modalIfsc" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Search IFSC Code</h4>
      </div>
			<div class="modal-body">
				<div class="row">
				<div class="col-sm-12">
					<form id="frmIfscSearch" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-4 control-label">Bank Name :</label>
							<div class="col-sm-6">
								<input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter Bank Name" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Branch/City Name :</label>
							<div class="col-sm-6">
								<input type="text" name="branch_name" id="branch_name" class="form-control" placeholder="Enter Branch/City Name" />
							</div>
						</div>
					</form>
				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="submitIfscSearch" class="btn btn-primary" data-dismiss="modal">Search</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalIfscResult" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Result IFSC Code</h4>
      </div>
			<div class="modal-body" style="overflow:auto;">
				<div class="row">
					<div class="col-sm-12">
						No Result found
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalOtcConfirm" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Confirm OTC</h4>
      </div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<form id="frmOtcConfirm" class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-4 control-label">OTC Reference No :</label>
								<div class="col-sm-6">
									<input type="text" name="otc_ref_no" id="otc_ref_no" readonly="" class="form-control" placeholder="Enter Reference" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">OTC :</label>
								<div class="col-sm-6">
									<input type="text" name="otc_code" id="otc_code" class="form-control" placeholder="Enter OTC" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="submitOtcResend" class="btn btn-primary">Resend OTC</button>
				<button type="button" id="submitOtcConfirm" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalRemittance" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Money Remittance</h4>
      </div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<form id="frmMoneyRemittance" class="form-horizontal">
							<input type="hidden" name="mr_uid" id="mr_uid" class="form-control" placeholder="Enter Beneficiary Name" />
							<div class="form-group">
								<label class="col-sm-4 control-label">Beneficiary Name :</label>
								<div class="col-sm-7">
									<input type="text" name="mr_ben_name" id="mr_ben_name" readonly="" class="form-control" placeholder="Enter Beneficiary Name" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Beneficiary Code :</label>
								<div class="col-sm-7">
									<input type="text" name="mr_ben_code" id="mr_ben_code" readonly="" class="form-control" placeholder="Enter Beneficiary Code" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Account Number :</label>
								<div class="col-sm-7">
									<input type="text" name="mr_ben_account" id="mr_ben_account" readonly="" class="form-control" placeholder="Enter Account Number" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">IFSC Code :</label>
								<div class="col-sm-7">
									<input type="text" name="mr_ben_ifsc" id="mr_ben_ifsc" readonly="" class="form-control" placeholder="Enter IFSC Code" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Transaction Type :</label>
								<div class="col-sm-7">
									<label class="radio-inline">
										<input type="radio" name="mr_ben_type" id="mr_ben_type_imps" value="IMPS" checked="checked" /> IMPS
									</label>
									<label class="radio-inline">
										<input type="radio" name="mr_ben_type" id="mr_ben_type_neft" value="NEFT" /> NEFT
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Amount :</label>
								<div class="col-sm-7">
									<input type="text" name="mr_amount" id="mr_amount" class="form-control" placeholder="Enter Amount" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id="submitMoneyRemittance" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php');?>