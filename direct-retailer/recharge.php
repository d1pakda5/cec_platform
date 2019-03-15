<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$is_true = false;
$opr = "";
$query = $db->query("SELECT opr.*, com.operator_id AS com_operator_id FROM operators opr LEFT JOIN usercommissions com ON opr.operator_id = com.operator_id WHERE com.uid = '".$_SESSION['retailer_uid']."' AND com.status = '1' AND opr.status = '1' ORDER BY opr.operator_name ASC");								
while($result = $db->fetchNextObject($query)) {
	$opr[] = array('id'=>$result->operator_id, 'name'=>$result->operator_name, 'type'=>$result->service_type);
}

$meta['title'] = "Recharge";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../js/recharge.js"></script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script> 
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#dob').datepicker({
		format: 'dd-mm-yyyy'
	});
	
	jQuery('.landlineBiller').change(function() {
		jQuery.get("ajax-landline-form.php",{xop: this.value, ajax: 'true'}, function(j){			
			jQuery("#ajaxLandlineForm").html(j);
		});
	});
	
	jQuery('.electricityBiller').change(function() {	
		if(this.value == 49||this.value == 64) {
			jQuery("#billBuDiv").css('display','none');
			jQuery("#billSubDiv").css('display','none');
			jQuery("#pcNumberDiv").css('display','none');
			jQuery("#customerDetail").css('display','none');
			jQuery("#billCycleDiv").css('display','');
			if(this.value == 64) 
			{
			    jQuery("#bill_cycle").attr('placeholder','AGRA,SURAT,AHMEDABAD,BHIWANDI');
			}
		} else if(this.value == 58) {
			jQuery("#billCycleDiv").css('display','none');
			jQuery("#billSubDiv").css('display','none');
			jQuery("#billBuDiv").css('display','');
			jQuery("#pcNumberDiv").css('display','');
			jQuery("#customerDetail").css('display','none');
			jQuery.ajax({ 
				url	: "ajax-billing-unit.php",
				type	: "POST",
				data	: "operator="+jQuery(this).val(),
				async	: false,
				success	: function(data) {
					jQuery("select#billing_unit").html(data);
				}
			});
		} else if(this.value == 55 || this.value == 56) {
			jQuery("#billCycleDiv").css('display','none');
			jQuery("#billBuDiv").css('display','none');
			jQuery("#pcNumberDiv").css('display','none');
			jQuery("#customerDetail").css('display','none');
			jQuery("#billSubDiv").css('display','');
			jQuery.ajax({ 
				url	: "ajax-sub-division.php",
				type	: "POST",
				data	: "operator="+jQuery(this).val(),
				async	: false,
				success	: function(data) {
					jQuery("select#sub_division").html(data);
				}
			});
		} else{
			jQuery("#billCycleDiv").css('display','none');
			jQuery("#billBuDiv").css('display','none');
			jQuery("#billSubDiv").css('display','none');
			jQuery("#pcNumberDiv").css('display','none');
			jQuery("#customerDetail").css('display','none');
		}
	});
});
function getSurcharge(formid, operatorid) {
	jQuery.ajax({ 
		url	: "../ajax/get-surcharge.php",
		type	: "POST",
		data	: "operator="+operatorid+"&uid="+<?php echo $aRetailer->mdist_id;?>,
		async	: false,
		success	: function(data) {
			var obj = jQuery.parseJSON(data);
			if(obj['is_surcharge']=='y') {
				jQuery("#"+formid+" #surChargeDiv").css('display','');
				jQuery("#"+formid+" input[name='scharge']").val(obj['amount']);
			} else {
				jQuery("#"+formid+" #surChargeDiv").css('display','none');
			}
		}
	});
}
function getVerified(formid) {
	var dt = jQuery("#"+formid).serialize();
	jQuery.ajax({ 
		url	:	"../ajax/get-verified.php",
		type	:	"POST",
		data	: dt,
		async	: false,
		success	: function(data) {
			jQuery('#rechargeVerify .modal-body').html(data);
			jQuery('#rechargeVerify').modal('show', {backdrop: 'static'});			
		}
	});
}
function find_plans()
{
	var mobile = jQuery("#account").val();
	jQuery.ajax({ 
		url	:	"jolo_ajax_plan.php",
		type	:"GET",
		data	: "mobile="+mobile,
		async	: false,
		success	: function(data) {
			var response=JSON.parse(data);
			var operator_code=response.operator_code;	
			var circle_code=response.circle_code;
			
			var html="";
			var type="TUP";
			window.localStorage.setItem('operator_code', operator_code);
			window.localStorage.setItem('circle_code', circle_code);
			jQuery.ajax({ 
					url	:	"jolo_ajax_final_plan.php",
					type	:"GET",
					data	: "opt="+operator_code+"&cir="+circle_code+"&typ="+type,
					async	: false,
					success	: function(data) {
						try {
							    jQuery.parseJSON(data);
							} 
							catch(error)
							 {
										html+='<tr>';
										html+='<td colspan="4" style="text-align:center">';
										html+=data;
										html+='</td></tr>';
										$("#plan_table_TUP").html(html);
								}
						var result=JSON.parse(data);
						for(i=0;i<result.length;i++)
						{
							var data=result[i];
							console.log(data);
							html+='<tr>';
							html+='<td>';
							html+=data.Detail;
							html+='</td><td>';
							html+=data.Validity;
							html+='</td><td>';
							html+=data.Amount;
							html+='</td><td>';
							html+='<button class="btn btn-success" amount="'+data.Amount+'" onclick="choose_plan(this)">Select</button>';
							html+='</td></tr>';

						}	
							$("#plan_table_TUP").html(html);

					}
				});
		}
	});

}

function plan_details(ele)
{
	var type=$(ele).attr("code");
	var operator_code=window.localStorage.getItem('operator_code');
	var circle_code=window.localStorage.getItem('circle_code');
	
	var html="";
	var i;
			jQuery.ajax({ 
					url	:	"jolo_ajax_final_plan.php",
					type	:"GET",
					data	: "opt="+operator_code+"&cir="+circle_code+"&typ="+type,
					async	: false,
					success	: function(data) {
						try {
							    jQuery.parseJSON(data);
							} 
							catch(error)
							 {
										html+='<tr>';
										html+='<td colspan="4" style="text-align:center">';
										html+=data;
										html+='</td></tr>';
										$("#plan_table_"+type).html(html);
								}
						var result=JSON.parse(data);
						
						
						for(i=0;i<result.length;i++)
						{
							var data=result[i];
							html+='<tr>';
							html+='<td>';
							html+=data.Detail;
							html+='</td><td>';
							html+=data.Validity;
							html+='</td><td>';
							html+=data.Amount;
							html+='</td><td>';
							html+='<button class="btn btn-success" amount="'+data.Amount+'" onclick="choose_plan(this)">Select</button>';
							html+='</td></tr>';
					
							
						}
						$("#plan_table_"+type).html(html);
					
				}
				});		
}

function choose_plan(ele) 
{
	var amount=$(ele).attr("amount");
	$("#amount").val(amount);
	$('#rechargePlan').modal('hide');

}
</script>
<div class="content">
	<div class="container">
		<?php if($error == 4) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Recharge successful!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Already refunded!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Insert a valid Transaction Id!!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<?php
		$qry = $db->query("SELECT * FROM notifications WHERE ntype='s' AND status='1' AND (user_type='5' OR user_type='0') AND notification_date_to >= CURDATE() ORDER BY notification_date DESC");
		if($db->numRows($qry) > 0) { ?>
		<div class="alert alert-notification">	
			<div class="row">
				<div class="col-xs-12 pull-right">			
					<marquee scrollamount="3" direction="scroll" onmouseover="this.setAttribute('scrollamount', 0, 0);" onmouseout="this.setAttribute('scrollamount', 3, 0);">
						<?php
						while($result = $db->fetchNextObject($qry)) { ?>
							<span class="text-alert" style="margin-right:40px;"><i class="fa fa-bullhorn"></i> <?php echo str_replace(array("<br>","<br/>"), "", $result->notification_content);?></span>
						<?php } ?>
					</marquee>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="row recharge-pane">
			<div class="col-sm-12">
				<div class="tab-recharge">
					<ul class="nav nav-tabs">
			  		<li class="active"><a href="#prepaid-mobile" data-toggle="tab">Mobile</a></li>
			  		<li><a href="#dth" data-toggle="tab">DTH</a></li>
						<li><a href="#datacard" data-toggle="tab">Datacard</a></li>
						<li><a href="#postpaid" data-toggle="tab">Postpaid</a></li>
						<li><a href="#landline" data-toggle="tab">Landline</a></li>
						<li><a href="#electricity" data-toggle="tab">Electricity</a></li>
						<li><a href="#gas" data-toggle="tab">Gas</a></li>
						<li><a href="#water" data-toggle="tab">Water</a></li>
						<li><a href="#insurance" data-toggle="tab">Insurance</a></li>					
					</ul>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="tab-content">
					<div class="tab-pane active" id="prepaid-mobile" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> PREPAID MOBILE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="mobileForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select" onchange="getSurcharge('mobileForm',this.value)">
												<option value="">select operator</option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '1') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>MOBILE NUMBER</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<button type="button" name="plans" id="plans" onclick="find_plans()" data-remote="false" class="btn btn-warning btn-block ireff_prepaid"><i class="fa fa-bookmark-o"></i> Plans</button>
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-8 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4 jrequired">
											    <?php if($_SESSION['retailer_uid']=='20032374')
                								{?>
                								<input type="submit" disabled name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php } else {
                								?>
                								<input type="submit" name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php }?>
												
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="dth" style="position: relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> DTH RECHARGE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="dthForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select" onchange="getSurcharge('dthForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '2') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>CUSTOMER ID</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Customer ID" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-12 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-8 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-sm-4">
												<?php if($_SESSION['retailer_uid']=='20032374')
                								{?>
                								<input type="submit" disabled name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php } else {
                								?>
                								<input type="submit" name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>					
					<div class="tab-pane" id="datacard" style="position: relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> DATACARD RECHARGE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="dataForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select" onchange="getSurcharge('dataForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '3') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>MOBILE NUMBER</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<!--<button type="button" name="plans" id="plans" data-remote="false" class="btn btn-warning btn-block ireff_prepaid"><i class="fa fa-bookmark-o"></i> Plans</button>-->
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4 jrequired">
											<?php if($_SESSION['retailer_uid']=='20032374')
                								{?>
                								<input type="submit" disabled name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php } else {
                								?>
                								<input type="submit" name="submit" value="RECHARGE" class="btn btn-success btn-block" />
                
                								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="postpaid" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> POSTPAID PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="postpaidForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select" onchange="getSurcharge('postpaidForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '4') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>MOBILE</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<button type="button" name="plans" id="plans" data-remote="false" class="btn btn-default btn-block" onclick="getVerified('postpaidForm')"><i class="fa fa-check"></i> Verify</button>
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4 jrequired">
											    <?php if($_SESSION['retailer_uid']=='20032374')
            								{?>
            								<input type="submit" disabled name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php } else {
            								?>
            								<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php }?>
												
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="landline" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> LANDLINE/BRODBAND BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="landlineForm">
									<div class="form-group">
										<label>BILLER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control landlineBiller" onchange="getSurcharge('landlineForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '5') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									
									<div id="ajaxLandlineForm">
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<button type="button" name="plans" id="plans" data-remote="false" class="btn btn-default btn-block" onclick="getVerified('landlineForm')"><i class="fa fa-check"></i> Verify</button>
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4 jrequired">
											<?php if($_SESSION['retailer_uid']=='20032374')
            								{?>
            								<input type="submit" disabled name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php } else {
            								?>
            								<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="electricity" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> ELECTRICITY BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="electricityForm">
									<div class="form-group">
										<label>SERVICE PROVIDER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control electricityBiller" onchange="getSurcharge('electricityForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '6') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label id="elcLabel">Consumer/Service/Customer Account Number</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Consumer/Service/Customer Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group" id="billCycleDiv" style="display:none;">
										<label>BILL CYCLE</label>
										<div class="jrequired">
											<input type="text" name="bill_cycle" id="bill_cycle" class="form-control" placeholder="Enter Bill Cycle" />
										</div>
									</div>
									<div class="form-group" id="billBuDiv" style="display:none;">
										<label>BILLING UNIT</label>
										<div class="jrequired">
											<select name="billing_unit" id="billing_unit" class="form-control">
												<option value=""></option>
											</select>
										</div>
									</div>
									<div class="form-group" id="pcNumberDiv" style="display:none;">
										<label>P C NUMBER</label>
										<div class="jrequired">
											<select name="pc_number" id="pc_number" class="form-control">
												<option></option>
												<option value="00">00</option>
												<option value="01">01</option>
												<option value="02">02</option>
												<option value="03">03</option>
												<option value="04">04</option>
												<option value="05">05</option>
												<option value="06">06</option>
												<option value="07">07</option>
												<option value="08">08</option>
											</select>
										</div>
									</div>
									<div class="form-group" id="billSubDiv" style="display:none;">
										<label>SUB DIVISION</label>
										<div class="jrequired">
											<select name="sub_division" id="sub_division" class="form-control">
												<option value=""></option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<button type="button" name="plans" id="plans" data-remote="false" class="btn btn-default btn-block" onclick="getVerified('electricityForm')"><i class="fa fa-check"></i> Verify</button>
											</div>
										</div>
									</div>									
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4 jrequired">
											<?php if($_SESSION['retailer_uid']=='20032374')
            								{?>
            								<input type="submit" disabled name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php } else {
            								?>
            								<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="gas" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> GAS BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="gasForm">
									<div class="form-group">
										<label>SERVICE PROVIDER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control" onchange="getSurcharge('gasForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '7') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>ACCOUNT NUMBER</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-12 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4">
											<?php if($_SESSION['retailer_uid']=='20032374')
            								{?>
            								<input type="submit" disabled name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php } else {
            								?>
            								<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="water" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> WATER BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="gasForm">
									<div class="form-group">
										<label>SERVICE PROVIDER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control" onchange="getSurcharge('gasForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '11') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>ACCOUNT NUMBER</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-12 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="insurance" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bars"></i> INSURANCE PREMIUM PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge.php" method="post" id="insuranceForm">
									<div class="form-group">
										<label>COMPANY</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control" onchange="getSurcharge('insuranceForm',this.value)">
												<option value=""></option>
												<?php
												foreach ($opr as $key=>$data) {
													if($data['type'] == '8') { ?>
												<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label>ACCOUNT NUMBER</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" autocomplete="off" />
										</div>
									</div>
									<div class="form-group">
										<label>DATE OF BIRTH</label>
										<div class="jrequired">
											<input type="text" name="dob" id="dob" class="form-control" readonly="" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="row">
											<div class="col-xs-12 jrequired">
												<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" autocomplete="off" />
											</div>
										</div>
									</div>
									<span id="surChargeDiv" style="display:none;">
										<div class="form-group">
											<label>Surcharge</label>
											<div class="row">
												<div class="col-xs-12 jrequired">
													<input type="text" name="scharge" id="scharge" readonly="" class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="alert alert-warning">
												<span class="text-red">Please collect additonal surcharge amount from your customer</span>
											</div>
										</div>
									</span>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<div class="col-xs-4">
											<?php if($_SESSION['retailer_uid']=='20032374')
            								{?>
            								<input type="submit" disabled name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php } else {
            								?>
            								<input type="submit" name="submit" value="PAY BILL" class="btn btn-success btn-block" />
            
            								<?php }?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Last 10 Recharge Details</h3>
					</div>
					<div class="box-body no-padding min-height-300">
						<table class="table table-condensed table-basic">
							<thead>
								<tr>
									<th>Date</th>
									<th>Operator</th>
									<th>Mobile/Acc</th>
									<th>A</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$array['status'] = getRechargeStatusList();
								$query = $db->query("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.uid = '".$_SESSION['retailer_uid']."' ORDER BY rch.request_date DESC LIMIT 10");
								if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
								while($result = $db->fetchNextObject($query)) {
								?>
								<tr>
									<td><?php echo $result->request_date;?></td>
									<td><?php echo $result->operator_name;?></td>
									<td><?php echo $result->account_no;?></td>
									<td><?php echo round($result->amount,2);?></td>
									<td><?php echo getRechargeStatusLabelUser($result->status);?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Recharge Plan Modal -->
<div class="modal fade" id="rechargePlan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 72%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title Enquiry" id="myModalLabel">Choose Your Plans</h5>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
			    <li class="active"><a data-toggle="tab" href="#home" code="TUP" onclick="plan_details(this)">Top-up</a></li>
			    <li><a data-toggle="tab" href="#menu1" code="FTT" onclick="plan_details(this)">Full Talk-time</a></li>
			    <li><a data-toggle="tab" href="#menu2" code="2G" onclick="plan_details(this)">2G Data</a></li>
			    <li><a data-toggle="tab" href="#menu3" code="3G" onclick="plan_details(this)">3G/4G Data</a></li>
			    <li><a data-toggle="tab" href="#menu4" code="SMS" onclick="plan_details(this)">SMS Pack</a></li>
			    <li><a data-toggle="tab" href="#menu5" code="LSC" onclick="plan_details(this)">Local/STD/ISD Call</a></li>
			    <li><a data-toggle="tab" href="#menu6" code="OTR" onclick="plan_details(this)">Other</a></li>
			    <li><a data-toggle="tab" href="#menu7" code="RMG" onclick="plan_details(this)">National/International Roaming</a></li>
			  </ul>

			  <div class="tab-content">
			    <div id="home" class="tab-pane fade in active">
			     <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					  <tbody id="plan_table_TUP">
					  	
					  </tbody>
				</table>
			    </div>
			    <div id="menu1" class="tab-pane fade">
			     <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_FTT">
					  	
					  </tbody>
				</table>
			    </div>
			    <div id="menu2" class="tab-pane fade">
			     <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_2G">
					  	
					  </tbody>
				</table>
			    </div>
			    <div id="menu3" class="tab-pane fade">
			      <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_3G">
					  	
					  </tbody>
				</table>
			    </div>
			     <div id="menu4" class="tab-pane fade">
			      <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_SMS">
					  	
					  </tbody>
				</table>
			    </div>
			     <div id="menu5" class="tab-pane fade">
			      <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_LSC">
					  	
					  </tbody>
				</table>
			    </div>
			     <div id="menu6" class="tab-pane fade">
			      <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_OTR">
					  	
					  </tbody>
				</table>
			    </div>
			     <div id="menu7" class="tab-pane fade">
			      <table class="table table-condensed table-bordered">
					  <tr>
					    <th>Details</th>
					    <th>Validity</th>
					    <th>Amount</th>
					    <th style="width: 2%">pick</th>
					  </tr>
					 <tbody id="plan_table_RMG">
					  	
					  </tbody>
				</table>
			    </div>
			  </div>
			</div>
		</div>
	</div>
</div>
<!-- Recharge verify Modal -->
<div class="modal fade" id="rechargeVerify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<img src="../images/loader.gif" />
			</div>
		</div>
	</div>
</div>
<script>
jQuery(function($){
	$('.ireff_prepaid').click(function(ev){
		$('#rechargePlan').modal('show');
	});
});
</script>
<?php include('footer.php'); ?>