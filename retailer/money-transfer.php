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
								jQuery("#boxSenderRegistrationKyc").hide();
								jQuery("#boxBeneficiaryForm").hide();
								jQuery("#boxBeneficiaryList").show();	
								jQuery("#panbtn").show();
								jQuery("#boxBeneficiaryList").html(jd);
							});
							swal.close();							
						} else if (obj["ResponseCode"]=='1') {							
							jQuery("#smobile").val(obj["MobileNo"]);
							jQuery("#boxSenderRegistrationKyc").show();
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
	//Pan Add
	jQuery("#panAdd").click(function() {
		var mobile = jQuery('#panmobile').val().trim();
		var pan_card = jQuery('#pan_card').val().trim();
		var fname = jQuery('#panfname').val().trim();
		var lname = jQuery('#panlname').val().trim();

		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
			return false;
		}
		else if(pan_card=="") {
			swal({ title: "Error", text: "Pan number cannot be blank", type: "error"});
			return false;
		} 
		else if(fname=="") {
			swal({ title: "Error", text: "First Name cannot be blank", type: "error"});
			return false;
		}
			else if(lname=="") {
			swal({ title: "Error", text: "Last Name cannot be blank", type: "error"});
			return false;
		}else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=panAdd&mobile="+mobile+"&pan_card="+pan_card+"&fname="+fname+"&lname="+lname,
					async : false,
					success	: function(data) {
						var obj = jQuery.parseJSON(data);
			
						if(obj["ResponseCode"]=='0') {
							
							swal("Pan Card Updated Successfully");			
							jQuery("#boxPanAdd").hide();
						
						} else {
						    swal(obj["Response"]);
						    swal.close();
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
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
				 swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=beneficiaryAdd&mobile="+mobile+"&ben_name="+ben_name+"&ben_account="+ben_account+"&ifsc="+ifsc,
					async : false,
					success	: function(data) {
					   //alert(data);
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
	//Ekyc Registration
	jQuery("#submitEkycRegister").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var ben_name = jQuery('#ekyc_ben_name').val().trim();
		var ben_account = jQuery('#ekyc_ben_account').val().trim();
		var ifsc = jQuery('#ekyc_ben_ifsc').val().trim();
		if(mobile=="" || ben_name=="" || ben_account=="" || ifsc=="") {
			swal({ title: "Error", text: "Mobile/Name/Account/IFSC cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});b
				return false;
			} else {
			 //swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=ekycAdd&mobile="+mobile+"&ben_name="+ben_name+"&ben_account="+ben_account+"&ifsc="+ifsc,
					async : false,
					success	: function(data) {
					   // alert(data);
					    var obj = jQuery.parseJSON(data);
					    $('#myModal').modal('show');
					    $("#htmlbind").css('display','block');
					    $("#htmlbind").attr('src',obj.html);
						
						if(obj.response["ResponseCode"]=='0') {	
						    
						swal.close();
				// 			jQuery('#otc_ref_no').val(obj["RequestNo"]);
				// 			jQuery('#otc_code').val('');
				// 			jQuery('#modalOtcConfirm').modal('show');							
						} else if (obj["ResponseCode"]=='1') {
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
						}
					}
				});
			}
		}
	});
	//Ekyc Registration
	jQuery("#submitEkycRegister1").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		var ben_name = jQuery('#ekyc_ben_name1').val().trim();
		var ben_account = jQuery('#ekyc_ben_account1').val().trim();
		var ifsc = jQuery('#ekyc_ben_ifsc1').val().trim();
		if(mobile=="" || ben_name=="" || ben_account=="" || ifsc=="") {
			swal({ title: "Error", text: "Mobile/Name/Account/IFSC cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Please enter a valid mobile number", type: "error"});b
				return false;
			} else {
			 //swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery.ajax({ 
					url : "process-spaisa.php",
					type : "GET",
					data : "request=ekycAdd&mobile="+mobile+"&ben_name="+ben_name+"&ben_account="+ben_account+"&ifsc="+ifsc,
					async : false,
					success	: function(data) {
					   // alert(data);
					    var obj = jQuery.parseJSON(data);
					    $('#myModal').modal('show');
					    $("#htmlbind").css('display','block');
					    $("#htmlbind").attr('src',obj.html);
						
						if(obj.response["ResponseCode"]=='0') {	
						    
						swal.close();
				// 			jQuery('#otc_ref_no').val(obj["RequestNo"]);
				// 			jQuery('#otc_code').val('');
				// 			jQuery('#modalOtcConfirm').modal('show');							
						} else if (obj["ResponseCode"]=='1') {
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
						} else {
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
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
	jQuery("#kycfindIfsc").click(function() {
		var mobile = jQuery('#mobile').val().trim();
		if(mobile=="") {
			swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
		} else {
			jQuery('#modalIfsc').modal('show');
		}
	});
		jQuery("#kycfindIfsc1").click(function() {
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
					url		: "https://api.techm.co.in/api/bank/search/likeBranchName",
					type	: "POST",
					dataType: 'json',
					contentType: "application/json; charset=utf-8",
					data	: '{ "bankName": "'+bank+'", "branchName" : "'+branch+'" }',
					async : false,
					success	: function(data) {
						swal.close();
			
						var html="";
						
						var data2=data.data;
						html+='<table style="overflow:scroll" class="table table-condensed table-striped"><thead><tr><th>IFSC</th><th>Bank Name</th><th>Branch Name</th><th>City</th><th>Address</th></tr></thead><tbody>';
						for(var i=0;i<data2.length;i++)
						{
						var ifsc=data2[i].IFSC;
					    var branch=data2[i].BRANCH;
					    var city=data2[i].CITY;
					    var bankname=data2[i].BANK;
					    var address=data2[i].ADDRESS;
					     
					     html+='<tr style="cursor:pointer" ifsc="'+ifsc+'" onclick="insertIfsc(this);">';
                    	 html+='<td>'+ifsc+'</td>';
                    	 html+='<td>'+bankname+'</td>';
                    	 html+='<td>'+branch+'</td>';
                    	 html+='<td>'+city+'</td>';
                    	 html+='<td>'+address+'</td></tr>';
						}
						html+='</tbody></table>';
						jQuery("#modalIfscResult .modal-body").html(html);
						jQuery('#modalIfscResult').modal('show');
												
				// 	   else {
				// 			swal({ title: "Error", text: obj["Message"], type: "error"});
				// 		}
					}
				});
			}
		}
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
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery('#modalRemittance').modal('hide');
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=moneyRemittance&mobile="+mobile+"&mr_uid="+mr_uid+"&mr_ben_name="+mr_ben_name+"&mr_ben_code="+mr_ben_code+"&mr_ben_account="+mr_ben_account+"&mr_ben_ifsc="+mr_ben_ifsc+"&mr_ben_type="+mr_ben_type+"&mr_amount="+mr_amount,
					async : false,
					success	: function(data) {
						jQuery("#frmMoneyRemittance").trigger('reset');	
				// 		swal.close();
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal({ title: "Success", text: obj["Message"], type: "success"});					
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
	
	//ReInitialize Money Remittance
	jQuery("#submitReInitiateRemittance").click(function() {		
		var mobile = jQuery('#mobile').val().trim();
		var rmr_transid = jQuery('#rmr_transid').val().trim();
		var rmr_option = jQuery('#rmr_option').val().trim();
		var rmr_ben_name = jQuery('#rmr_ben_name').val().trim();
		var rmr_ben_code = jQuery('#rmr_ben_code').val().trim();
		var rmr_ben_account = jQuery('#rmr_ben_account').val().trim();
		var rmr_ben_ifsc = jQuery('#rmr_ben_ifsc').val().trim();
		var rmr_ben_type = jQuery("input[name='rmr_ben_type']").val().trim();
		var rmr_amount = jQuery('#rmr_amount').val().trim();
		if(mobile=="" || rmr_transid=="" || rmr_option=="" || rmr_ben_name=="" || rmr_ben_code=="" || rmr_ben_account=="" || rmr_ben_ifsc=="" || rmr_ben_type=="" || rmr_amount=="") {
			swal({ title: "Error", text: "Mobile/Bank/Branch cannot be blank", type: "error"});
			return false;
		} else {
			if(parseInt(mobile.length)!='10') {
				swal({ title: "Error", text: "Invalid mobile number", type: "error"});
				return false;
			} else {
				swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
				jQuery('#modalReInitiate').modal('hide');
				jQuery.ajax({ 
					url		: "process-spaisa.php",
					type	: "GET",
					data	: "request=reInitiateRemittance&mobile="+mobile+"&trans_ref_no="+rmr_transid+"&ben_name="+rmr_ben_name+"&ben_code="+rmr_ben_code+"&ben_account="+rmr_ben_account+"&ifsc="+rmr_ben_ifsc+"&ben_type="+rmr_ben_type+"&amount="+rmr_amount,
					async : false,
					success	: function(data) {
						jQuery("#frmReInitiate").trigger('reset');	
						//swal.close();
						var obj = jQuery.parseJSON(data);
						if(obj["ResponseCode"]=='0') {							
							swal({ title: "Success", text: obj["Message"], type: "success"});					
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
	
	//Trigger Reset ReInitializze Form
	jQuery('#modalReInitiate').on('hidden.bs.modal', function(){
		jQuery("#frmReInitiate").trigger('reset');
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
	jQuery("#boxSenderTransaction").empty();
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
					jQuery.ajax({ 
						url		: "dmt/list-transaction.php",
						type	: "POST",
						data	: "js_data="+data,
						async : false,
						success	: function(jds) {
						  //  alert(jds);
							jQuery("#boxSenderTransaction").show();		
							jQuery("#boxSenderTransaction").html(jds);
						}
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
function getEkycForm() {
	swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
	jQuery("#boxSenderTransaction").hide();
	jQuery("#boxBeneficiaryList").hide();
	jQuery("#boxEkycForm").show();
	swal.close();
}

function benDelete(ben_code, ben_ifsc) {
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="" || ben_code=="" || ben_ifsc=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif"});
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

function getReInitiate(transid,amount) {
	var opts = "";	
	opts += "<option value='0'>Select Beneficiary</option>";
	var table = jQuery("#tblBeneficiary tbody");
	//alert(table);
	table.find('tr').each(function (i) {
		var $tds = $(this).find('td'),
			benName = $tds.eq(1).text(),
			benCode = $tds.eq(2).text();
		opts += "<option value='"+benCode+"'>"+benName+"</option>";
	});
	jQuery("#rmr_option").html(opts);
	jQuery('#rmr_amount').val(amount);
	jQuery('#rmr_transid').val(transid);
	jQuery('#modalReInitiate').modal('show');
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

function insertIfsc(ele) {
    var ifsc=$(ele).attr("ifsc");
// 	alert(ifsc);
	jQuery("#ben_ifsc").val(ifsc);
	jQuery("#ekyc_ben_ifsc").val(ifsc);
	jQuery("#ekyc_ben_ifsc1").val(ifsc);
	jQuery('#modalIfscResult').modal('hide');
}

function getBen(a) {
	var $tds = $("#"+a).find('td'),
		rmr_ben_name = $tds.eq(1).text(),
		rmr_ben_type = $tds.eq(6).text(),
		rmr_ben_account = $tds.eq(3).text(),
		rmr_ben_account_type = $tds.eq(4).text(),
		rmr_ben_code = $tds.eq(2).text()
		rmr_ben_ifsc = $tds.eq(5).text();
	jQuery('#rmr_ben_name').val(rmr_ben_name);
	jQuery('#rmr_ben_type').val(rmr_ben_type);
	jQuery('#rmr_ben_code').val(rmr_ben_code);
	jQuery('#rmr_ben_account').val(rmr_ben_account);
	jQuery('#rmr_ben_account_type').val(rmr_ben_account_type);
	jQuery('#rmr_ben_ifsc').val(rmr_ben_ifsc);
}
</script>
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


<html>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Money Transfer</div>
		</div>
		<div class="row">
			<?php
			if($aRetailer->is_money!='a') {?>
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
			
				
				
				<div id="panbtn" class="box-header" style="display:none;">
				    <div class="box-body">	
						<div class="form-horizontal">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> PAN Card Update</h3>
						&nbsp;	&nbsp;	&nbsp;	&nbsp;	&nbsp;	&nbsp;	&nbsp;
						<button class="btn btn-success btn-sm" data-toggle="collapse" data-target="#boxPanAdd">ENTER DETAILS HERE</button>
				        </div>
				    </div>
				</div>
				
				<div class="box-body">	
						<div class="form-horizontal">
				            <div id="boxPanAdd" class="form-group collapse" >
							    <div class="col-sm-12">
								<label class="col-sm-2 control-label">Sender :</label>
								<div class="col-sm-4">
									<input type="text" name="panmobile" id="panmobile" class="form-control" placeholder="Enter Mobile Number" />
								</div>
								</div>
								<div class="col-sm-12">
								<label class="col-sm-2 control-label">PAN NO :</label>
								<div class="col-sm-4">
									<input type="text" name="pan_card" id="pan_card" class="form-control" placeholder="Enter PAN Number" />
								</div>
								</div>
								<div class="col-sm-12">
								<label class="col-sm-2 control-label">First Name :</label>
								<div class="col-sm-4">
									<input type="text" name="panfname" id="panfname" class="form-control" placeholder="Enter first name" />
								</div>
								</div>
								<div class="col-sm-12">
								<label class="col-sm-2 control-label">Last Name :</label>
								<div class="col-sm-4">
									<input type="text" name="panlname" id="panlname" class="form-control" placeholder="Enter first name" />
								</div>
								<div class="col-sm-3">
									<button type="submit" id="panAdd" class="btn btn-success">Update PAN</button>
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
				<div id="boxSenderRegistrationKyc" style="display:none;">
					<div class="dmt box">
						<div class="box-header">
							<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Ekyc</h3>
							<div class="pull-right">
								<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-success">Back</a>
							</div>
						</div>
						<div class="box-body">	
							<form id="frmBeneficiaryRegister" class="form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">Beneficiary Name :</label>
									<div class="col-sm-5">
										<input type="text" name="ekyc_ben_name" id="ekyc_ben_name" class="form-control" placeholder="Enter Beneficiary Name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Bank Account :</label>
									<div class="col-sm-5">
										<input type="text" name="ekyc_ben_account" id="ekyc_ben_account" class="form-control" placeholder="Enter Bank Account Number" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">IFSC :</label>
									<div class="col-sm-5">
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="ekyc_ben_ifsc" id="ekyc_ben_ifsc" class="form-control" placeholder="Enter IFSC Code" />
											</div>
											<div class="col-sm-4">
												<button type="button" name="kycfindIfsc" id="kycfindIfsc" class="btn btn-block btn-default" data-backdrop="static" data-keyboard="false">Find IFSC</button>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">&nbsp;</label>
									<div class="col-sm-5">
										<button type="button" id="submitEkycRegister" class="btn btn-primary">Submit</button>
									</div>
								</div>							
							</form>
						</div>
					</div>					
				</div>
				<!-- end of sender registration -->
				
				
				
				
				
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
				
					<!-- start Ekyc add -->
				<div id="boxEkycForm" style="display:none;">
					<div class="dmt box">
						<div class="box-header">
							<h3 class="box-title"><i class="fa fa-angle-right"></i> Add Ekyc</h3>
							<div class="pull-right">
								<a href="javascript:void(0)" onclick="getBackBeneList()" class="btn btn-success">Back</a>
							</div>
						</div>
						<div class="box-body">	
							<form id="frmBeneficiaryRegister" class="form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">Beneficiary Name :</label>
									<div class="col-sm-5">
										<input type="text" name="ekyc_ben_name1" id="ekyc_ben_name1" class="form-control" placeholder="Enter Beneficiary Name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Bank Account :</label>
									<div class="col-sm-5">
										<input type="text" name="ekyc_ben_account1" id="ekyc_ben_account1" class="form-control" placeholder="Enter Bank Account Number" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">IFSC :</label>
									<div class="col-sm-5">
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="ekyc_ben_ifsc1" id="ekyc_ben_ifsc1" class="form-control" placeholder="Enter IFSC Code" />
											</div>
											<div class="col-sm-4">
												<button type="button" name="kycfindIfsc" id="kycfindIfsc1" class="btn btn-block btn-default" data-backdrop="static" data-keyboard="false">Find IFSC</button>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">&nbsp;</label>
									<div class="col-sm-5">
										<button type="button" id="submitEkycRegister1" class="btn btn-primary">Submit</button>
									</div>
								</div>							
							</form>
						</div>
					</div>					
				</div>
				<!-- end of Ekyc add -->
				
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
<div class="modal fade" id="modalReInitiate" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">ReInitiate Money Remittance</h4>
      </div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<form id="frmReInitiate" class="form-horizontal">
							<input type="hidden" name="rmr_transid" id="rmr_transid" class="form-control" placeholder="Enter Beneficiary Name" />
							<input type="hidden" name="rmr_ben_name" id="rmr_ben_name" class="form-control" placeholder="Enter Beneficiary Name" />
							<div class="form-group">
								<label class="col-sm-4 control-label">Beneficiary Name :</label>
								<div class="col-sm-7">
									<select name="rmr_option" id="rmr_option" class="form-control" onchange="getBen(this.value);">
										<option value="">Select Beneficiary</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Beneficiary Code :</label>
								<div class="col-sm-7">
									<input type="text" name="rmr_ben_code" id="rmr_ben_code" readonly="" class="form-control" placeholder="Enter Beneficiary Code" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Account Number :</label>
								<div class="col-sm-7">
									<input type="text" name="rmr_ben_account" id="rmr_ben_account" readonly="" class="form-control" placeholder="Enter Account Number" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">IFSC Code :</label>
								<div class="col-sm-7">
									<input type="text" name="rmr_ben_ifsc" id="rmr_ben_ifsc" readonly="" class="form-control" placeholder="Enter IFSC Code" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Transaction Type :</label>
								<div class="col-sm-7">
									<label class="radio-inline">
										<input type="radio" name="rmr_ben_type" id="rmr_ben_type_imps" value="IMPS" checked="checked" /> IMPS
									</label>
									<label class="radio-inline">
										<input type="radio" name="rmr_ben_type" id="rmr_ben_type_neft" value="NEFT" /> NEFT
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Amount :</label>
								<div class="col-sm-7">
									<input type="text" name="rmr_amount" id="rmr_amount" readonly="" class="form-control" placeholder="Enter Amount" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" id="submitReInitiateRemittance" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>
</html>
<?php include('footer.php');?>