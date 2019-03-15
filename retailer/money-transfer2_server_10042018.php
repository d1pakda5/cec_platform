<?php
session_start();
include('../config.php');
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('common.php');
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$meta['title'] = "Money Transfer 2";
include('header.php');
?>
<style>
.btn .btn-primary {
	color:#fff;
}
h3.box-title {
	font-size:18px!important;
}
.form-title {
	font-size:16px;
	width:100%;
	float:left;
	padding-left:10px;
	padding-bottom:10px;
	margin-top:25px;

	margin-bottom:25px;

	border-bottom:1px solid #ddd;

}

body.modal-open .datepicker {

  z-index: 1200 !important;

}

</style>

<script type="text/javascript" src="../js/sweetalert.min.js"></script>

<link rel="stylesheet" href="../css/sweetalert.css">

<script type="text/javascript">

$(document).ready(function(){

	//Customer Search

	$("#submitSearchCustomer").click(function() {

		var mobile = $('#mobile').val().trim();

		if(mobile=="") {

			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$.ajax({ 

					url : "dmt-yesbank.php",

					type : "GET",

					data : "request=searchCustomer&mobile="+mobile,

					async : false,

					success	: function(data) {	

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='200') {

							swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"},

							function() {

								$("#customerDetails").val(data);

								$("#customerKycStatus").val(obj["DATA"]["SENDER_CUSTTYPE"]);

								$.get("yesbank/list-beneficiary.php",{js_data: obj, ajax: 'true'}, function(jd){

									$("#boxCustomerRegistration").hide();	

									$("#boxBeneficiaryForm").hide();

									$("#boxBeneficiaryList").html(jd);

									$("#boxBeneficiaryList").show();

								});

							});

						} else if (obj["RESP_CODE"]=='224') {

							$('#frmCustomerRegister').find("#mobile").val(obj["RQST_MOBILE"]);

							$("#boxBeneficiaryList").hide();

							$("#boxCustomerRegistration").show();

							swal({ title: "ERROR", text: obj["RESP_MSG"], type: "warning"});

						} else {

							swal({ title: "ERROR", text: obj["RESP_MSG"], type: "error"});

						}

					}

				});

			}

		}

	});

	

	//Customer Registration along with beneficiary

	$("#submitCustomerRegister").click(function() {

		var mobile = $('#frmCustomerRegister').find("#mobile").val().trim();

		var fname = $('#frmCustomerRegister').find('#fname').val().trim();

		var lname = $('#frmCustomerRegister').find('#lname').val().trim();

		var dob = $('#frmCustomerRegister').find('#dob').val().trim();

		var city = $('#frmCustomerRegister').find('#city').val().trim();

		var state = $('#frmCustomerRegister').find('#state').val().trim();

		var pincode = $('#frmCustomerRegister').find('#pincode').val().trim();

		var ben_mobile = $('#frmCustomerRegister').find('#ben_mobile').val().trim();

		var ben_name = $('#frmCustomerRegister').find('#ben_name').val().trim();

		var ben_account = $('#frmCustomerRegister').find('#ben_account').val().trim();

		var ben_ifsc = $('#frmCustomerRegister').find('#ben_ifsc').val().trim();

		if(mobile=="" || fname=="" || lname=="" || dob=="" || city=="" || state=="" || pincode=="" || ben_mobile=="" || ben_name=="" || ben_account=="" || ben_ifsc=="") {

			var txtError = "Field cannot be blank";

			if(mobile=="") {

				var txtError = "Please enter customer mobile number";

			} else if (fname=="") {

				var txtError = "Please enter customer first name";

			} else if (lname=="") {

				var txtError = "Please enter customer last name";

			} else if (dob=="") {

				var txtError = "Please enter customer date of birth";

			} else if (city=="") {

				var txtError = "Please enter customer city";

			} else if (state=="") {

				var txtError = "Please select customer state";

			} else if (pincode=="") {

				var txtError = "Please enter pincode";

			} else if (ben_mobile=="") {

				var txtError = "Please enter beneficiary mobile number";

			} else if (ben_name=="") {

				var txtError = "Please enter beneficiary name";

			} else if (ben_account=="") {

				var txtError = "Please enter beneficiary bank account no";

			} else if (ben_ifsc=="") {

				var txtError = "Please enter IFSC Code";

			}

			swal({ title: "Error", text: txtError, type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Please enter a valid 10 digit mobile number", type: "error"});

				return false;

			} else if(parseInt(ben_mobile.length)!='10') {

				swal({ title: "Error", text: "Please enter a valid 10 digit beneficiary mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$.ajax({ 

					url : "dmt-yesbank.php",

					type : "GET",

					data : "request=customerRegistration&mobile="+mobile+"&fname="+fname+"&lname="+lname+"&dob="+dob+"&city="+city+"&state="+state+"&pincode="+pincode+"&ben_mobile="+ben_mobile+"&ben_name="+ben_name+"&ben_account="+ben_account+"&ben_ifsc="+ben_ifsc,

					async : false,

					success	: function(data) {

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='200') {							

							swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"},

							function() {

								$('#otpRequestFor').val('CUSTVERIFICATION');

								$('#otpBenId').val(obj["benId"]);

								$('#otpRefCode').val(obj["Rcode"]);

								$('#otpCode').val('');

								$('#modalOtpVerify').modal('show');	

							});						

						} else if (obj["RESP_CODE"]=='224') {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						} else {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						}

					}

				});

			}

		}

	});

	

	//Beneficiary Registration

	$("#submitBeneficiaryRegister").click(function() {

		var mobile = $('#mobile').val().trim();

		var benRegMobile = $('#benRegMobile').val().trim();

		var benRegName = $('#benRegName').val().trim();

		var benRegAccount = $('#benRegAccount').val().trim();

		var benRegIfsc = jQuery('#benRegIfsc').val().trim();

		if(mobile=="" || benRegMobile=="" || benRegName=="" || benRegAccount=="" || benRegIfsc=="") {

			swal({ title: "Error", text: "Mobile/Name/Account/IFSC cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$.ajax({ 

					url : "dmt-yesbank.php",

					type : "GET",

					data : "request=beneficiaryAdd&mobile="+mobile+"&ben_mobile="+benRegMobile+"&ben_name="+benRegName+"&ben_account="+benRegAccount+"&ifsc="+benRegIfsc,

					async : false,

					success	: function(data) {

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='200') {	

						  swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"},

							function() {

								$('#otpRequestFor').val('BENVERIFICATION');

								$('#otpBenId').val(obj["benId"]);

								$('#otpRefCode').val(obj["Rcode"]);

								$('#otpCode').val('');

								$('#modalOtpVerify').modal('show');	

							});						

						} else if (obj["RESP_CODE"]=='224') {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						} else {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						}

					}

				});

			}

		}

	});

	

	//OTP Verification

	$("#submitOtpVerification").click(function() {

		var mobile = $('#mobile').val().trim();

		var otpBenId = $('#otpBenId').val().trim();

		var otpRequestFor = $('#otpRequestFor').val().trim();

		var otpRefCode = $('#otpRefCode').val().trim();

		var otpCode = $('#otpCode').val().trim();

		if(mobile=="" || otpBenId=="" || otpRequestFor=="" || otpRefCode=="" || otpCode=="") {

			swal({ title: "Error", text: "Mobile or Referece Code or OTP cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Invalid mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$.ajax({ 

					url		: "dmt-yesbank.php",

					type	: "GET",

					data	: "request=otpVerify&mobile="+mobile+"&otp_ref_code="+otpRefCode+"&otp="+otpCode+"&ben_id="+otpBenId+"&request_for="+otpRequestFor,

					async : false,

					success	: function(data) {

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='200') {						

							swal({ title: "Success", text: obj["RESP_MSG"], type: "success"},

							function () {

								if(otpRequestFor=='CUSTVERIFICATION') {

									$('#boxCustomerRegistration').hide();

								} else if (otpRequestFor=='BENVERIFICATION') {

									$('#boxBeneficiaryForm').hide();

								}

								$('#modalOtpVerify').modal('hide');

							});

						} else if (obj["RESP_CODE"]=='224') {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						} else {

							swal({ title: "Error", text: obj["Message"], type: "error"});

						}

					}

				});

			}

		}

	});

	//Resend OTP

	$("#submitOtpResend").click(function() {

		var mobile = $('#mobile').val().trim();

		var otpRequestFor = $('#otpRequestFor').val().trim();

		if(mobile=="" || otpRequestFor=="") {

			swal({ title: "Error", text: "Mobile/OTP reference number cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Invalid mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$.ajax({ 

					url		: "dmt-yesbank.php",

					type	: "GET",

					data	: "request=otpGenerate&mobile="+mobile+"&request_for="+otpRequestFor,

					async : false,

					success	: function(data) {

						//console.log(data);

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='200') {							

							swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"},

							function() {

								$('#otpRequestFor').val(otpRequestFor);

								$('#otpRefCode').val(obj["Rcode"]);

								$('#submitOtpResend').attr("disabled", 'disabled');								

							});							

						} else if (obj["RESP_CODE"]=='224') {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						} else {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						}

					}

				});

			}

		}

	});

	

	$("#findIfscBen, #findIfscBenReg").click(function() {

		var mobile = $('#mobile').val().trim();

		if(mobile=="") {

			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});

		} else {

			$('#modalIfsc').modal('show');

		}

	});

	

	//Search IFSC Code

	$("#submitIfscSearch").click(function() {

		var mobile = $('#mobile').val().trim();

		var bank = $('#bank_name').val().trim();

		var branch = $('#branch_name').val().trim();

		if(mobile=="" || bank=="" || branch=="") {

			swal({ title: "Error", text: "Mobile/Bank/Branch cannot be blank", type: "error"});

		} else {

			if(bank.length <= '3' || branch.length <= '3') {

				swal({ title: "Error", text: "Bank or Branch name required min 4 charcter", type: "error"});

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});

				$.ajax({ 

					url		: "https://api.techm.co.in/api/bank/search/likeBranchName",

					type	: "POST",

					dataType: 'json',

					contentType: "application/json; charset=utf-8",

					data	: '{ "bankName": "'+bank+'", "branchName" : "'+branch+'" }',

					async : false,

					success	: function(data) {

						swal.close();			

						var html = "";						

						var data2 = data.data;

						html +='<table style="overflow:scroll" class="table table-condensed table-striped"><thead><tr><th>IFSC</th><th>Bank Name</th><th>Branch Name</th><th>City</th><th>Address</th></tr></thead><tbody>';

						for(var i=0;i<data2.length;i++) {

							var ifsc = data2[i].IFSC;

					    var branch = data2[i].BRANCH;

					    var city = data2[i].CITY;

					    var bankname = data2[i].BANK;

					    var address = data2[i].ADDRESS;

							html += '<tr style="cursor:pointer" ifsc="'+ifsc+'" onclick="insertIfsc(this);">';

							html += '<td>'+ifsc+'</td>';

							html += '<td>'+bankname+'</td>';

							html += '<td>'+branch+'</td>';

							html += '<td>'+city+'</td>';

							html += '<td>'+address+'</td></tr>';

						}

						html += '</tbody></table>';

						$("#modalIfscResult .modal-body").html(html);

						$('#modalIfscResult').modal('show');

					}

				});

			}

		}

	});

	

	//Search Transaction Histroy

	$("#submitTransactionHistroy").click(function() {

		var mobile = $('#mobile').val().trim();

		var fromDate = $('#fromDate').val().trim();

		var toDate = $('#toDate').val().trim();

		if(mobile=="") {

			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});

		} else {

			$("#modalDateBetween").modal('hide');			

			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});			

			$.ajax({ 

				url		: "dmt-yesbank.php",

				type	: "GET",

				data	: "request=transactionHistory&mobile="+mobile+"&from="+fromDate+"&to="+toDate,

				async : false,

				success	: function(data) {

					var obj = $.parseJSON(data);

					if(obj["RESP_CODE"]=='200') {

						swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"},

						function() {

							$.ajax({ 

								url		: "yesbank/list-transaction.php",

								type	: "POST",

								data	: "js_data="+data,

								async : false,

								success	: function(jd) {

									$("#boxSenderTransaction").show();		

									$("#boxSenderTransaction").html(jd);

								}

							});

						});

					} else if (obj["RESP_CODE"]=='224') {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					} else {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					}

				}

			});

		}

	});

	

	//Money Transfer

	$("#submitMoneyRemittance").click(function() {		

		var mobile = $('#mobile').val().trim();

		var benId = $('#benRemId').val().trim();

		var benMobile = $('#benRemMobile').val().trim();

		var benName = $('#benRemName').val().trim();

		var benAccount = $('#benRemAccount').val().trim();

		var benBank = $('#benRemBank').val().trim();

		var benIfsc = $('#benRemIfsc').val().trim();

		var benType = $("input[name='ben_rem_type']").val().trim();

		var benAmount = $('#benRemAmount').val().trim();

		var kycStatus = $('#customerKycStatus').val().trim();

		if(mobile=="" || benId=="" || benMobile=="" || benName=="" || benAccount=="" || benBank=="" || benIfsc=="" || benType=="" || benAmount=="" || kycStatus=="") {

			swal({ title: "Error", text: "Mobile or other fields cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Invalid mobile number", type: "error"});

				return false;

			} else {

				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

				$('#benRemitanceModal').modal('hide');

				$.ajax({ 

					url		: "dmt-yesbank.php",

					type	: "GET",

					data	: "request=moneyRemittance&mobile="+mobile+"&ben_id="+benId+"&ben_mobile="+benMobile+"&ben_name="+benName+"&ben_account="+benAccount+"&ben_bank="+benBank+"&ben_ifsc="+benIfsc+"&ben_type="+benType+"&amount="+benAmount+"&kyc_status="+kycStatus,

					async : false,

					success	: function(data) {

						$("#frmMoneyRemittance").trigger('reset');	

						var obj = $.parseJSON(data);

						if(obj["RESP_CODE"]=='300') {							

							swal({ title: "Success", text: obj["RESP_MSG"], type: "success"});					

						} else if (obj["RESP_CODE"]=='224') {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						} else {

							swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

						}

					}

				});

			}

		}

	});

	

	//Beneficiary Delete

	$("#submitBeneficiaryDelete").click(function() {

		var mobile = $('#mobile').val().trim();

		var benId = $('#benDelId').val().trim();

		var benRefCode = $('#benDelReferenceCode').val().trim();

		var benOtp = $('#benDelOtp').val().trim();

		if(mobile=="" || benId=="" || benRefCode=="" || benOtp=="") {

			swal({ title: "Error", text: "Mobile/OTP cannot be blank", type: "error"});

		} else {

			$("#modalBenDelete").modal('hide');		

			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});			

			$.ajax({ 

				url		: "dmt-yesbank.php",

				type	: "GET",

				data	: "request=beneficiaryDelete&mobile="+mobile+"&ben_id="+benId+"&ben_ref_code="+benRefCode+"&otp="+benOtp,

				async : false,

				success	: function(data) {

					var obj = $.parseJSON(data);

					if(obj["RESP_CODE"]=='200') {

						swal({ title: "SUCCESS", text: obj["RESP_MSG"], type: "success"});

					} else if (obj["RESP_CODE"]=='224') {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					} else {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					}

				}

			});

		}

	});

	

	var dob = $('#dob').datepicker({

		format: 'dd-mm-yyyy'

	}).on('changeDate', function(ev) {

  	dob.hide();

	}).data('datepicker');

	

	var frDate = $('#fromDate').datepicker({

		format: 'dd-mm-yyyy'

	}).on('changeDate', function(ev) {

  	frDate.hide();

	}).data('datepicker');

	

	var toDate = $('#toDate').datepicker({

		format: 'dd-mm-yyyy'

	}).on('changeDate', function(ev) {

  	toDate.hide();

	}).data('datepicker');

	

});



function generateOtps(rType,requestFor,benId) {
    var requestFor=requestFor;
	var mobile = $('#mobile').val().trim();

	var rCode = "";

	if(mobile=="" || rType=="" || requestFor=="") {

		swal({ title: "Error", text: "Mobile or Referece Code or OTP cannot be blank", type: "error"});

		return false;

	} else {

		if(parseInt(mobile.length)!='10') {

			swal({ title: "Error", text: "Invalid mobile number", type: "error"});

			return false;

		} else {

			$.ajax({ 

				url		: "dmt-yesbank.php",

				type	: "GET",

				data	: "request=otpGenerate&mobile="+mobile+"&type="+rType+"&request_for="+requestFor+"&ben_id="+benId,

				async : false,

				success	: function(data) {

					var obj = $.parseJSON(data);

					if(obj["RESP_CODE"]=='200') {						

						swal({ title: "Success", text: obj["RESP_MSG"], type: "success"});

						rCode = obj["Rcode"];

					} else if (obj["RESP_CODE"]=='224') {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					} else {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					}

				}				

			});

			return rCode;

		}

	}

}



function benDeleteModal(benId,benName,benAccount,benIfsc) {

	var rCode = generateOtps('9','BENDELETE',benId);	

	if(rCode!='') {

		var mobile = $('#mobile').val().trim();

		if(mobile=="") {

			swal({ title: "Error", text: "Mobile or Referece Code or OTP cannot be blank", type: "error"});

			return false;

		} else {

			if(parseInt(mobile.length)!='10') {

				swal({ title: "Error", text: "Invalid mobile number", type: "error"});

				return false;

			} else {	

				$("#benDelId").val(benId);

				$("#benDelName").val(benName);

				$("#benDelAccount").val(benAccount);

				$("#benDelIfsc").val(benIfsc);

				$("#benDelReferenceCode").val(rCode);

				$('#modalBenDelete').modal('show');

			}

		}

	} else {

		swal({ title: "Error", text: "Request cannot be completed, Please try again", type: "error"});

	}

}



function benValidate(benId,benMobile,benName,benAccount,benBankName,benIfsc) {

	var mobile = $('#mobile').val().trim();

	var kycStatus = $('#customerKycStatus').val().trim();

	if(mobile=="" || benId=="" || benMobile=="" || benName=="" || benAccount=="" || benBankName=="" || benIfsc=="") {

		swal({ title: "Error", text: "Fields cannot be blank", type: "error"});

	} else {

		swal({

			title: "Are you sure?",

			text: "Do you really want to validate beneficiary account!",

			type: "success",

			showCancelButton: true,

			confirmButtonColor: "#5cb85c",

			confirmButtonText: "Yes, Confirm!",

			closeOnConfirm: false

		},

		function(){

			swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

			$.ajax({ 

				url		: "dmt-yesbank.php",

				type	: "GET",

				data	: "request=beneficiaryValidate&mobile="+mobile+"&ben_id="+benId+"&ben_mobile="+benMobile+"&ben_name="+benName+"&ben_account="+benAccount+"&ben_bank="+benBankName+"&ben_ifsc="+benIfsc+"&kyc_status="+kycStatus,

				async : false,

				success	: function(data) {

					var obj = $.parseJSON(data);

					if(obj["RESP_CODE"]=='300') {

						swal({ title: "Success", text: obj["RESP_MSG"], type: "success"});

						$('#val_'+benId).hide();	

					} else if (obj["RESP_CODE"]=='224') {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					} else {

						swal({ title: "Error", text: obj["RESP_MSG"], type: "error"});

					}

				}

			});

		});

	}

}



function benRemittance(benId,benMobile,benName,benAccount,benBank,benIfsc) {	

	var mobile = $('#mobile').val().trim();

	if(mobile=="") {

		swal({ title: "Error", text: "Mobile cannot be blank", type: "error"});

		return false;

	} else {

		if(parseInt(mobile.length)!='10') {

			swal({ title: "Error", text: "Invalid mobile number", type: "error"});

			return false;

		} else {	

			$("#benRemId").val(benId);

			$("#benRemMobile").val(benMobile);

			$("#benRemName").val(benName);

			$("#benRemAccount").val(benAccount);

			$("#benRemBank").val(benBank);

			$("#benRemIfsc").val(benIfsc);

			$('#benRemitanceModal').modal('show');

		}

	}

}



function getBackBeneList() {

	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});

	jQuery("#boxSenderTransaction").hide();

	jQuery("#boxBeneficiaryForm").hide();

	jQuery("#boxEkycForm").hide();

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



function enableButton(code) {

	jQuery('a[id^=rem_]').addClass('disabled');

	jQuery('a[id^=val_]').addClass('disabled');

	jQuery('a[id^=del_]').addClass('disabled');

	jQuery('#rem_'+code).removeClass('disabled');

	jQuery('#val_'+code).removeClass('disabled');

	jQuery('#del_'+code).removeClass('disabled');	

}



function insertIfsc(ele) {

	var ifsc = $(ele).attr("ifsc");

	$("#ben_ifsc").val(ifsc);

	$("#benRegIfsc").val(ifsc);

	$('#modalIfscResult').modal('hide');

}



function getDateBetween() {

	$('#modalDateBetween').modal('show');

}



function getTransactionDetail(data) {

	

}

</script>

<link rel="stylesheet" href="../js/datepicker/datepicker.css">

<script src="../js/datepicker/bootstrap-datepicker.js"></script>

<div id="myModal" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" onclick="getBackBeneList()"  data-dismiss="modal">&times;</button>

        <h4 class="modal-title">E-KYC</h4>

      </div>

      <div class="modal-body">

        <iframe id="htmlbind" style="width: 400;height: 600px;margin: 10px 25% auto;"> </iframe>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-default" onclick="getBackBeneList()"  data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>



<div class="content">

	<div class="container">

		<div class="page-header">

			<div class="page-title">Money Transfer</div>

			<div class="pull-right"><img src="../images/yesbank.png" /></div>

		</div>

		<div class="row">

			<?php

			if($aRetailer->is_money!='a') {?>

			<div class="col-sm-12 min-height-480">

				<?php include("dmt-activation.php");?>

			</div>

			<?php } else { ?>

			<div class="col-sm-12 min-height-480">			

				<!-- Search Customer -->

				<div class="box">

					<div class="box-header">

						<h3 class="box-title"><i class="fa fa-angle-right"></i> Money Transfer</h3>

					</div>

					<div class="box-body">	

						<div class="form-horizontal">

							<div class="row">

								<div class="col-sm-6">

									<div class="form-group">

										<label class="col-sm-4 control-label">Customer :</label>

										<div class="col-sm-6">

											<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" />

										</div>

										<div class="col-sm-2">

											<button type="submit" id="submitSearchCustomer" class="btn btn-success">Fetch Customer</button>

										</div>

									</div>	

								</div>

							</div>

						</div>					

					</div>

				</div>				

				<!-- start response dump -->

				<div style="display:none;">

					<textarea type="text" id="customerDetails"></textarea>

					<input type="text" name="customer_kyc_status" id="customerKycStatus"/>

				</div>

				<!-- end of response dump -->				

				<!-- start beneficiary list -->

				<div id="boxBeneficiaryList" style="display:none;">	

				</div>

				<!-- end of beneficiary list -->				

				<!-- start sender registration -->

				<div id="boxCustomerRegistration" style="display:none;">

					<div class="box">

						<div class="box-header">

							<h3 class="box-title"><i class="fa fa-user"></i> Add Customer along with Beneficiary</h3>

						</div>

						<div class="box-body">	

							<form id="frmCustomerRegister" class="form-horizontal">

								<div class="row">

									<div class="col-sm-6">

										<div class="form-title">Customer Detail:</div>					

										<div class="form-group">

											<label class="col-sm-4 control-label">Mobile Number :</label>

											<div class="col-sm-8">

												<input type="text" name="mobile" id="mobile" class="form-control" readonly="" placeholder="Enter Mobile Number" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">First Name :</label>

											<div class="col-sm-8">

												<input type="text" name="fname" id="fname" class="form-control" placeholder="Enter First Name" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Last Name :</label>

											<div class="col-sm-8">

												<input type="text" name="lname" id="lname" class="form-control" placeholder="Enter Last Name" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Date of Birth :</label>

											<div class="col-sm-8">

												<input type="text" name="dob" id="dob" class="form-control" readonly="" placeholder="Date of Birth" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">City :</label>

											<div class="col-sm-8">

												<input type="text" name="city" id="city" class="form-control" placeholder="Enter city" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">State :</label>

											<div class="col-sm-8">

												<select name="state" id="state" class="form-control">

													<option value="">Select State</option>

													<?php $query = $db->query("SELECT * FROM states");

													while($result = $db->fetchNextObject($query)) { ?>

													<option value="<?php echo $result->states;?>"><?php echo $result->states;?></option>

													<?php } ?>

											</select>

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Pincode :</label>

											<div class="col-sm-8">

												<input type="text" name="pincode" id="pincode" class="form-control" placeholder="Enter pincode" />

											</div>

										</div>

									</div>

									<div class="col-sm-6">

										<div class="form-title">Beneficiary Detail :</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Beneficiary Mobile:</label>

											<div class="col-sm-8">

												<input type="text" name="ben_mobile" id="ben_mobile" class="form-control" placeholder="Enter Mobile Number" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Beneficiary Name:</label>

											<div class="col-sm-8">

												<input type="text" name="ben_name" id="ben_name" class="form-control" placeholder="Enter Beneficiary Name" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Account No:</label>

											<div class="col-sm-8">

												<input type="text" name="ben_account" id="ben_account" class="form-control" placeholder="Enter Account No" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Bank IFSC:</label>

											<div class="col-sm-8">

												<div class="row">

													<div class="col-sm-8">

														<input type="text" name="ben_ifsc" id="ben_ifsc" class="form-control" placeholder="Enter IFSC Code" />

													</div>

													<div class="col-sm-4">

														<button type="button" id="findIfscBen" class="btn btn-warning" data-backdrop="static" data-keyboard="false">Find IFSC</button>

													</div>

												</div>

											</div>

										</div>

									</div>

								</div>

								<div class="row">

									<div class="col-sm-6">

										<div class="form-group">

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">&nbsp;</label>

											<div class="col-sm-5">

												<button type="button" id="submitCustomerRegister" class="btn btn-primary btn-lg">Register</button>

											</div>

										</div>

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

						<div class="box-heading">

							<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Beneficiary</h3>

							<div class="pull-right">

								<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-xs btn-primary">Back</a>

							</div>

						</div>

						<div class="box-body">	

							<form id="frmBeneficiaryRegister" class="form-horizontal">

								<div class="row">

									<div class="col-sm-6">

										<div class="form-group">

											<label class="col-sm-4 control-label">Beneficiary Mobile:</label>

											<div class="col-sm-8">

												<input type="text" name="ben_name" id="benRegMobile" class="form-control" placeholder="Enter Beneficiary Mobile" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Beneficiary Name:</label>

											<div class="col-sm-8">

												<input type="text" name="ben_name" id="benRegName" class="form-control" placeholder="Enter Beneficiary Name" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">Bank Account :</label>

											<div class="col-sm-8">

												<input type="text" name="ben_account" id="benRegAccount" class="form-control" placeholder="Enter Bank Account Number" />

											</div>

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">IFSC :</label>

											<div class="col-sm-8">

												<div class="row">

													<div class="col-sm-8">

														<input type="text" name="ben_ifsc" id="benRegIfsc" class="form-control" placeholder="Enter IFSC Code" />

													</div>

													<div class="col-sm-4">

														<button type="button" name="findIfscBenReg" id="findIfscBenReg" class="btn btn-block btn-default" data-backdrop="static" data-keyboard="false">Find IFSC</button>

													</div>

												</div>

											</div>

										</div>

										<div class="form-group">

										</div>

										<div class="form-group">

											<label class="col-sm-4 control-label">&nbsp;</label>

											<div class="col-sm-5">

												<button type="button" id="submitBeneficiaryRegister" class="btn btn-primary btn-lg">Submit</button>

											</div>

										</div>

									</div>

								</div>						

							</form>

						</div>

					</div>					

				</div>

				<!-- end of beneficiary add -->

				

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

			<div class="modal-body" style="overflow:scroll;height:550px">

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

<div class="modal fade" id="modalOtpVerify" role="dialog" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<!-- Modal content-->

		<div class="modal-content">

			<div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title">OTP Verification</h4>

      </div>

			<div class="modal-body">

				<div class="row">

					<div class="col-sm-12">

						<form id="frmOtpVerify" class="form-horizontal">

							<div class="form-group">

								<label class="col-sm-4 control-label">ID :</label>

								<div class="col-sm-6">

									<input type="text" name="otp_ben_id" id="otpBenId" readonly="" class="form-control" placeholder="Enter ID" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">OTP Request :</label>

								<div class="col-sm-6">

									<input type="text" name="otp_request_for" id="otpRequestFor" readonly="" class="form-control" placeholder="Enter Request" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">OTP Reference Code :</label>

								<div class="col-sm-6">

									<input type="text" name="otp_ref_code" id="otpRefCode" readonly="" class="form-control" placeholder="Enter Reference" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">OTP :</label>

								<div class="col-sm-6">

									<input type="text" name="otp_code" id="otpCode" class="form-control" placeholder="Enter OTP" />

								</div>

							</div>

						</form>

					</div>

				</div>

			</div>

			<div class="modal-footer">

				<button type="button" id="submitOtpResend" class="btn btn-warning">Resend OTP</button>

				<button type="button" id="submitOtpVerification" class="btn btn-primary">Confirm</button>

			</div>

		</div>

	</div>

</div>

<!-- Money Transfer Modal -->

<div class="modal fade" id="benRemitanceModal" role="dialog" data-backdrop="static" data-keyboard="false">

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

								<label class="col-sm-4 control-label">Beneficiary ID :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_id" id="benRemId" readonly="" class="form-control" placeholder="Enter Beneficiary ID" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary Name :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_name" id="benRemName" readonly="" class="form-control" placeholder="Enter Beneficiary Name" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary Mobile :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_mobile" id="benRemMobile" readonly="" class="form-control" placeholder="Enter Beneficiary Mobile" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Bank Account No :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_account" id="benRemAccount" readonly="" class="form-control" placeholder="Bank Account Number" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Bank Name :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_bank" id="benRemBank" readonly="" class="form-control" placeholder="Bank Name" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">IFSC Code :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_ifsc" id="benRemIfsc" readonly="" class="form-control" placeholder="Enter IFSC Code" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Transaction Type :</label>

								<div class="col-sm-7">

									<label class="radio-inline">

										<input type="radio" name="ben_rem_type" id="benRemTypeImps" value="IMPS" checked="checked" /> IMPS

									</label>

									<label class="radio-inline">

										<input type="radio" name="ben_rem_type" id="benRemTypeNeft" value="NEFT" /> NEFT

									</label>

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Amount :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_rem_amount" id="benRemAmount" class="form-control" placeholder="Enter Amount" />

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

<!-- Search Transaction date -->

<div class="modal fade" id="modalBenDelete" role="dialog" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<!-- Modal content-->

		<div class="modal-content">

			<div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title">Delete Beneficiary</h4>

      </div>

			<div class="modal-body">

				<div class="row">

					<div class="col-sm-12">

						<form id="frmBenDelete" class="form-horizontal">

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary ID :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_id" id="benDelId" readonly="" class="form-control" placeholder="Beneficiary ID" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary Name :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_name" id="benDelName" readonly="" class="form-control" placeholder="Beneficiary Name" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary Account :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_account" id="benDelAccount" readonly="" class="form-control" placeholder="Beneficiary Account" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Beneficiary IFSC :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_ifsc" id="benDelIfsc" readonly="" class="form-control" placeholder="IFSC Code" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">Reference Code :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_reference_code" id="benDelReferenceCode" readonly="" class="form-control" placeholder="Enter Refrence Code" />

								</div>

							</div>

							<div class="form-group">

								<label class="col-sm-4 control-label">OTP :</label>

								<div class="col-sm-7">

									<input type="text" name="ben_del_otp" id="benDelOtp" class="form-control" placeholder="Enter OTP" />

								</div>

							</div>

						</form>

					</div>

				</div>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

				<button type="button" id="submitBeneficiaryDelete" class="btn btn-primary">Delete</button>

			</div>

		</div>

	</div>

</div>

<!-- Search Transaction date -->

<div class="modal fade" id="modalDateBetween" role="dialog" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<!-- Modal content-->

		<div class="modal-content">

			<div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title">Select Dates</h4>

      </div>

			<div class="modal-body">

				<div class="row">

					<div class="col-sm-12">

						<form id="frmTransactionHistory" class="form-horizontal">

							<div class="form-group">

								<label class="col-sm-2 control-label">From :</label>

								<div class="col-sm-3">

									<input type="text" name="from_date" id="fromDate" readonly="" class="form-control datepicker" placeholder="From Date" />

								</div>

								<label class="col-sm-2 control-label">To Date :</label>

								<div class="col-sm-3">

									<input type="text" name="to_date" id="toDate" readonly="" class="form-control datepicker" placeholder="To Date" />

								</div>

							</div>

						</form>

					</div>

				</div>

			</div>

			<div class="modal-footer">

				<button type="button" id="submitTransactionHistroy" class="btn btn-primary">Search Transaction</button>

			</div>

		</div>

	</div>

</div>

<!-- Transaction detail Modal -->

<div class="modal fade" id="modalTransactionDetail" role="dialog" data-backdrop="static" data-keyboard="false">

	<div class="modal-dialog">

		<!-- Modal content-->

		<div class="modal-content">

			<div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title">Transaction Details</h4>

      </div>

			<div class="modal-body">

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>

</html>

<?php include('footer.php');?>