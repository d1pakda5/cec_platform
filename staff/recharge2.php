<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['offline_recharge'])) { 
	include('permission.php');
	exit(); 
}
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$recharge_id = isset($_GET['recharge_id']) && $_GET['recharge_id'] != '' ? mysql_real_escape_string($_GET['recharge_id']) : 0;
$is_true = false;
$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, user.user_id, user.uid, user.company_name, user.mobile FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$recharge_id."' ");
$param_info = $db->queryUniqueObject("SELECT * FROM apps_recharge_details WHERE recharge_id = '".$recharge_id."' "); 

$meta['title'] = "Offline Recharge";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<!--<script type="text/javascript" src="../js/recharge.js"></script>-->
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script> 
<script type="text/javascript">

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
		
		<div class="row recharge-pane">
			
			<div class="col-sm-6 col-xs-6">
				<div class="box">
								<form action="process-recharge2.php" method="post" id="mobileForm">
									<div class="box-body padding-50">
					            <div class="form-group">
									<label class="col-sm-4 control-label">Recharge Id :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="recharge_id" id="recharge_id" readonly="" value="<?php echo $recharge_info->recharge_id;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Company Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="operator_name" id="operator_name" readonly="" value="<?php echo $recharge_info->operator_name;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">operator ID :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="operator" id="operator"  value="<?php echo $recharge_info->operator_id?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile/Account :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="account" id="account" readonly="" value="<?php echo $recharge_info->account_no;?>" class="form-control">
									</div>
								</div>
								<?php if($recharge_info->operator_id == '58') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Billing Unit :</label>
									<div class="col-sm-8 jrequired">
									    <input type="text" name="billing_unit" id="billing_unit" readonly="" value="<?php echo $param_info->billing_unit;?>" class="form-control"> 
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">P.C. Number :</label>
										<div class="col-sm-8 jrequired">
									<input type="text" name="pc_number" id="pc_number" readonly="" value="<?php echo $param_info->pc_number;?>" class="form-control"> 
									</div>
									</div>
								<?php } ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Amount :</label>
								<div class="col-sm-8 jrequired">
										<input type="text" name="amount" id="amount"  value="<?php echo round( $recharge_info->amount,0);?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">API :</label>
										<div class="col-sm-8 jrequired">
									<select name="api" id="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT api_id,api_name FROM api_list WHERE status = '1' ORDER BY api_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->api_id;?>" <?php if(isset($_GET['api']) && $_GET['api']==$result->api_id) {?> selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
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
											<label class="col-sm-4 control-label">PIN :</label>
									
											<div class="col-xs-8 jrequired">
												<input type="password" name="pin" id="pin" class="form-control" placeholder="Enter Your 4 Digit PIN" autocomplete="off" />
											</div>
											<br/>
											<div class="col-xs-4 pull-right jrequired">
												<input type="submit" name="submit" value="RECHARGE" class="btn btn-success btn-block" />
											</div>
										
									</div>
								</form>
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
								$query = $db->query("SELECT rch.*, opr.operator_name FROM apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id WHERE rch.uid = '20018105' ORDER BY rch.request_date DESC LIMIT 10");
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