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
$query = $db->query("SELECT opr.*, com.operator_id AS com_operator_id FROM operators opr LEFT JOIN apps_commission com ON opr.operator_id = com.operator_id AND com.uid = '".$aRetailer->mdist_id."' AND com.status = '1' AND opr.status = '1' ORDER BY opr.operator_name ASC");								
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
		if(this.value == 49) {
			jQuery("#billBuDiv").css('display','none');
			jQuery("#billSubDiv").css('display','none');
			jQuery("#pcNumberDiv").css('display','none');
			jQuery("#customerDetail").css('display','none');
			jQuery("#billCycleDiv").css('display','');
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
		$qry = $db->query("SELECT * FROM notifications WHERE ( user_type = '5' OR user_type = '0' ) AND status = '1' ORDER BY notification_date DESC");
		if($db->numRows($qry) > 0) { ?>
		<div class="alert alert-notification">	
			<i class="fa fa-lg fa-bell-o text-red"></i>	
			<div class="col-sm-10 pull-right">				
				<marquee scrollamount="3" direction="scroll" onmouseover="this.setAttribute('scrollamount', 0, 0);" onmouseout="this.setAttribute('scrollamount', 3, 0);">
					<?php
					while($result = $db->fetchNextObject($qry)) { ?>
						<span class="text-alert" style="margin-right:40px;"><i class="fa fa-clock-o"></i> <?php echo $result->notification_content;?></span>
					<?php } ?>
				</marquee>
			</div>
		</div>
		<?php } ?>
		<div class="row">
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
						<li><a href="#insurance" data-toggle="tab">Insurance</a></li>					
					</ul>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="tab-content">
					<div class="tab-pane active" id="prepaid-mobile" style="position:relative;">
						<div class="panel">	
							<div class="panel-heading">
								<h3 class="panel-title">PREPAID MOBILE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="mobileForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select">
												<option value=""></option>
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="RECHARGE" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">DTH RECHARGE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="dthForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select">
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Customer ID" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="RECHARGE" class="btn btn-lg btn-success pull-right" />
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
								<h3 class="panel-title">DATACARD RECHARGE</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="dataForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select">
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
										<label>CUSTOMER ID</label>
										<div class="jrequired">
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Customer ID" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="RECHARGE" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">POSTPAID PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="postpaidForm">
									<div class="form-group">
										<label>OPERATOR</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control operator-select">
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Mobile Number" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">LANDLINE/BRODBAND BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="landlineForm">
									<div class="form-group">
										<label>BILLER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control landlineBiller">
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
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">ELECTRICITY BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="electricityForm">
									<div class="form-group">
										<label>SERVICE PROVIDER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control electricityBiller">
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Consumer/Service/Customer Number" />
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
										<label>BILL AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div id="customerDetail" style="display:none;">
									<h4 class="text-green">Customer Details</h4>
									<div class="form-group">
										<label>Customer Name</label>
										<div class="jrequired">
											<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter Customer Name" />
										</div>
									</div>
									<div class="form-group">
										<label>Customer Mobile</label>
										<div class="jrequired">
											<input type="text" name="customer_mobile" id="customer_mobile" class="form-control" placeholder="Enter Customer Mobile" />
										</div>
									</div>
									<div class="form-group">
										<label>Customer City</label>
										<div class="jrequired">
											<input type="text" name="customer_city" id="customer_city" class="form-control" placeholder="Enter Customer City" />
										</div>
									</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">GAS BILL PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="gasForm">
									<div class="form-group">
										<label>SERVICE PROVIDER</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control">
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" />
										</div>
									</div>
									<div class="form-group">
										<label>AMOUNT</label>
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success pull-right" />
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
								<h3 class="panel-title">INSURANCE PREMIUM PAYMENT</h3>
							</div>
							<div class="panel-body">
								<form action="process-recharge-test.php" method="post" id="insuranceForm">
									<div class="form-group">
										<label>COMPANY</label>
										<div class="jrequired">
											<select name="operator" id="operator" class="form-control">
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
											<input type="text" name="account" id="account" class="form-control" placeholder="Enter Account Number" />
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
										<div class="jrequired">
											<input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" />
										</div>
									</div>
									<div class="form-group">
										<label>PIN</label>
										<div class="row">
											<div class="col-sm-6 jrequired">
												<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter PIN" />
											</div>
											<div class="col-sm-6 jrequired">
												<input type="submit" name="submit" value="PAY BILL" class="btn btn-success pull-right" />
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
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
<?php include('footer.php'); ?>