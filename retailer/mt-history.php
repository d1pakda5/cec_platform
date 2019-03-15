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

function getTransaction() {
	//Get Senders balance
	var mobile = jQuery('#mobile').val().trim();
	if(mobile=="") {
		swal({ title: "Error", text: "Mobile number cannot be blank", type: "error"});
	} else {
		swal({ title: "Processing", text: "Please wait a moment..", imageUrl: "../images/preloader.gif", showConfirmButton: false});
		jQuery.ajax({ 
			url		: "process-spaisa.php",
			type	: "GET",
			data	: "request=senderTransaction&mobile="+mobile,
			async : false,
			success	: function(data) {
				jQuery("#boxSenderTransaction").html(data);
				swal.close();
			}
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
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Money Transfer <small>/ History</small></div>
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
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Get the history of money transfer</h3>
					</div>
					<div class="box-body">	
						<div class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-2 control-label">Sender :</label>
								<div class="col-sm-4">
									<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" />
								</div>
								<div class="col-sm-3">
									<button type="submit" class="btn btn-success" onclick="getTransaction()">Get History</button>
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
				
				<!-- start response dump -->
				<div id="boxSenderTransaction">
				</div>
				<!-- end of response dump -->
			</div>
			<?php } ?>
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
<?php include('footer.php');?>