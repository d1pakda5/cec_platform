<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['service_type'] == '' || $_POST['operator_name'] == '' || $_POST['operator_code'] == '') {
		$error = 1;		
	} else {
		
		$operator_name = htmlentities(addslashes($_POST['operator_name']),ENT_QUOTES);
		$operator_code = htmlentities(addslashes($_POST['operator_code']),ENT_QUOTES);
		$operator_longcode = htmlentities(addslashes($_POST['operator_longcode']),ENT_QUOTES);
		$hsn_sac_code = htmlentities(addslashes($_POST['hsn_sac_code']),ENT_QUOTES);
		$min_amount = htmlentities(addslashes($_POST['min_amount']),ENT_QUOTES);
		$max_amount = htmlentities(addslashes($_POST['max_amount']),ENT_QUOTES);	
		$sur_amount = htmlentities(addslashes($_POST['sur_amount']),ENT_QUOTES);
		$code_egpay = htmlentities(addslashes($_POST['code_egpay']),ENT_QUOTES);
		$service_type_egpay = htmlentities(addslashes($_POST['service_type_egpay']),ENT_QUOTES);
		$code_arroh = htmlentities(addslashes($_POST['code_arroh']),ENT_QUOTES);
		$code_achariya = htmlentities(addslashes($_POST['code_achariya']),ENT_QUOTES);
		$code_modem = htmlentities(addslashes($_POST['code_modem']),ENT_QUOTES);
		$code_modem_rp = htmlentities(addslashes($_POST['code_modem_rp']),ENT_QUOTES);
		$code_roundpay = htmlentities(addslashes($_POST['code_roundpay']),ENT_QUOTES);
		$code_rechargeatz = htmlentities(addslashes($_POST['code_rechargeatz']),ENT_QUOTES);
		$code_exioms = htmlentities(addslashes($_POST['code_exioms']),ENT_QUOTES);
		$service_type_exioms = htmlentities(addslashes($_POST['service_type_exioms']),ENT_QUOTES);
		$code_aarav = htmlentities(addslashes($_POST['code_aarav']),ENT_QUOTES);
		$service_type_aarav = htmlentities(addslashes($_POST['service_type_aarav']),ENT_QUOTES);
		$code_ambika = htmlentities(addslashes($_POST['code_ambika']),ENT_QUOTES);
		$code_cyberplat = htmlentities(addslashes($_POST['code_cyberplat']),ENT_QUOTES);
		$code_offline = htmlentities(addslashes($_POST['code_offline']),ENT_QUOTES);
		$is_auto_switch = isset($_POST['is_auto_switch']) ? 'y' : 'n';
		
		$exists = $db->queryUniqueObject("SELECT * FROM operators WHERE ( operator_name = '".$operator_name."' OR operator_longcode = '".$operator_longcode."' OR operator_code = '".$operator_code."' ) AND operator_id != '".$request_id."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("UPDATE `operators` SET `operator_name`='".$operator_name."', `operator_code`='".$operator_code."', `operator_longcode`='".$operator_longcode."', `service_type`='".$_POST['service_type']."', `billing_type`='".$_POST['billing_type']."', `hsn_sac_code`='".$hsn_sac_code."', `minimum_amount`='".$min_amount."', `maximum_amount`='".$max_amount."', `code_egpay`='".$code_egpay."', `service_type_egpay`='".$service_type_egpay."', `code_arroh`='".$code_arroh."', `code_achariya`='".$code_achariya."', `code_modem`='".$code_modem."', `code_modem_rp`='".$code_modem_rp."', `code_roundpay`='".$code_roundpay."', `code_rechargeatz`='".$code_rechargeatz."', `code_exioms`='".$code_exioms."', `service_type_exioms`='".$service_type_exioms."', `code_aarav`='".$code_aarav."', `service_type_aarav`='".$service_type_aarav."', `code_ambika`='".$code_ambika."', `code_cyberplat`='".$code_cyberplat."', `code_offline`='".$code_offline."', `api_id`='".$_POST['api']."', `commission_type`='".$_POST['commission_type']."', `is_surcharge`='".$_POST['is_surcharge']."', `surcharge_type`='".$_POST['surcharge_type']."', `surcharge_value`='".$sur_amount."', `is_auto_switch`='".$is_auto_switch."',`safe_id`='".$_POST['safe_api']."', `is_express`='".$_POST['is_express']."', `status`='".$_POST['status']."' WHERE operator_id = '".$request_id."' ");
			$error = 3;
		}		
	}
}

$operator = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id = '".$request_id."' ");
if(!$operator) {
	header("location:operator.php");
	exit();
}

$meta['title'] = "Operator";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#surcharge_no").click(function(){
		$("#sur_amount").prop('readonly', true);
  	});
	$("#surcharge_yes").click(function(){
		$("#sur_amount").prop('readonly', false);
  	});
	$('#operatorForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update operator?")) {
    		form.submit();
    	}
		},
	  rules: {
			service_type: {
				required: true
			},
			operator_name: {
	      required:true
	    },
			operator_code: {
				required: true
			},
			operator_longcode: {
				required: true
			},	      	
			status: {
				required: true
			}
	  },
		highlight: function(element) {
			$(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Operators <small>/ Edit</small></div>
			<div class="pull-right">
				<a href="operator.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some fields are empty!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-plus-square"></i> Edit operator</h3>
			</div>
			<form action="" method="post" id="operatorForm" class="form-horizontal">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Service Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="service_type" id="service_type" class="col-md-6 form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM service_type");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->service_type_id;?>" <?php if($result->service_type_id==$operator->service_type) {?>selected="selected"<?php } ?>><?php echo $result->service_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Operator Name :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="operator_name" id="operator_name" value="<?php echo $operator->operator_name;?>" class="form-control" placeholder="OPERATOR NAME">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Operator Code :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-3">
										<input type="text" name="operator_longcode" id="operator_longcode" value="<?php echo $operator->operator_longcode;?>" class="form-control" placeholder="LONG CODE">
									</div>
									<div class="col-sm-9">
										<input type="text" name="operator_code" id="operator_code" value="<?php echo $operator->operator_code;?>" class="form-control" placeholder="OPERATOR CODE">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Amount Between:</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-6">
										<input type="text" name="min_amount" id="min_amount" value="<?php echo $operator->minimum_amount;?>" class="form-control" placeholder="MINIMUM AMOUNT">
									</div>
									<div class="col-sm-6">
										<input type="text" name="max_amount" id="max_amount" value="<?php echo $operator->maximum_amount;?>" class="form-control" placeholder="MAXIMUM AMOUNT">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Commission Type :</label>
							<div class="col-sm-8 jrequired">
								<label class="radio-inline">
									<input type="radio" name="commission_type" id="comm_type_per" value="p" <?php if($operator->commission_type=='p') {?>checked="checked"<?php } ?>> Percentage
								</label>
								<label class="radio-inline">
									<input type="radio" name="commission_type" id="comm_type_flat" value="f" <?php if($operator->commission_type=='f') {?>checked="checked"<?php } ?>> Flat in (Rs)
								</label>
							</div>
						</div>	
						<div class="form-group">
							<label class="col-sm-4 control-label">Surcharge :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-3">
										<label class="radio-inline">
											<input type="radio" name="is_surcharge" id="surcharge_no" value="n" <?php if($operator->is_surcharge=='n') {?>checked="checked"<?php } ?>> No
										</label>
										<label class="radio-inline">
											<input type="radio" name="is_surcharge" id="surcharge_yes" value="y" <?php if($operator->is_surcharge=='y') {?>checked="checked"<?php } ?>> Yes
										</label>
									</div>
									<div class="col-sm-9">
										<input type="text" name="sur_amount" id="sur_amount" value="<?php echo $operator->surcharge_value;?>" <?php if($operator->is_surcharge=='n') {?>readonly=""<?php } ?> class="form-control">
									</div>
								</div>
							</div>
						</div>	
						<div class="form-group">
							<label class="col-sm-4 control-label">Surcharge Type :</label>
							<div class="col-sm-8 jrequired">
								<label class="radio-inline">
									<input type="radio" name="surcharge_type" id="surcharge_type_no" value="f" <?php if($operator->surcharge_type=='f') {?>checked="checked"<?php } ?>> Fixed
								</label>
								<label class="radio-inline">
									<input type="radio" name="surcharge_type" id="surcharge_type_yes" value="p" <?php if($operator->surcharge_type=='p') {?>checked="checked"<?php } ?>> Percentage
								</label>
							</div>
						</div>				
						<div class="form-group">
							<label class="col-sm-4 control-label">Is Safe Mode :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-3">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="is_auto_switch" <?php if($operator->is_auto_switch=='y'){?>checked="checked"<?php }?> /> Yes
											</label>
										</div>
									</div>
									<div class="col-sm-9">
									    <label class="radio-inline">
        									<input type="radio" name="safe_api" id="cyberplat" value="10"<?php if($operator->safe_api=='10'){?> checked="checked"<?php }?>>Cyberplat
        								</label>
        								<label class="radio-inline">
        									<input type="radio" name="safe_api" id="ambika" value="9"<?php if($operator->safe_api=='9'){?> checked="checked"<?php }?>> Ambika
        								</label>
										
									</div>
								</div>								
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label">Is Express Mode :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-6">
										<select name="is_express" id="is_express" class="form-control">
											<option value=""></option>
											<option value="0"<?php if($operator->is_express=='0') {?> selected="selected"<?php } ?>>*** No Express</option>
											<?php
											$query = $db->query("SELECT * FROM operators WHERE operator_name LIKE '%EXPRESS%' AND status='1' ");
											while($result = $db->fetchNextObject($query)) { ?>
											<option value="<?php echo $result->operator_id;?>"<?php if($operator->is_express==$result->operator_id) {?> selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
											<?php } ?>
										</select>
									</div>
								</div>								
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-4 control-label">Billing Type :</label>
							<div class="col-sm-8 jrequired">
								<label class="radio-inline">
									<input type="radio" name="billing_type" id="billing_type_p2p" value="1"<?php if($operator->billing_type=='1'){?> checked="checked"<?php }?>> P2P (Principle to Principle)
								</label>
								<label class="radio-inline">
									<input type="radio" name="billing_type" id="billing_type_p2a" value="2"<?php if($operator->billing_type=='2'){?> checked="checked"<?php }?>> P2A (Principle to Agent)
								</label>
								<label class="radio-inline">
									<input type="radio" name="billing_type" id="billing_type_p2s" value="3"<?php if($operator->billing_type=='3'){?> checked="checked"<?php }?>> P2A (Surcharge)
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">HSN/SAC Code :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="hsn_sac_code" id="hsn_sac_code" value="<?php echo $operator->hsn_sac_code;?>" class="form-control" placeholder="HSN/SAC Code">
							</div>
						</div>
						<div class="form-group"><div class="col-sm-12"><hr></div></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">EGPAY :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-xs-7">
										<input type="text" name="code_egpay" id="code_egpay" value="<?php echo $operator->code_egpay;?>" class="form-control" placeholder="EGPAY OPERATOR CODE">
									</div>
									<div class="col-xs-5">
										<input type="text" name="service_type_egpay" value="<?php echo $operator->service_type_egpay;?>" class="form-control" placeholder="EGPAY SERVICE TYPE">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">ARROH :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_arroh" id="code_arroh" value="<?php echo $operator->code_arroh;?>" class="form-control" placeholder="ARROH OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">ACHARIYA :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_achariya" id="code_achariya" value="<?php echo $operator->code_achariya;?>" class="form-control" placeholder="ACHARIYA OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">MODEM :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_modem" id="code_modem" value="<?php echo $operator->code_modem;?>" class="form-control" placeholder="MODEM CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">MODEM ROUNDPAY :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_modem_rp" id="code_modem_rp" value="<?php echo $operator->code_modem_rp;?>" class="form-control" placeholder="MODEM ROUNDPAY OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">ROUNDPAY API :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_roundpay" id="code_roundpay" value="<?php echo $operator->code_roundpay;?>" class="form-control" placeholder="ROUNDPAY OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">RECHARGE A2Z :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_rechargeatz" id="code_rechargeatz" value="<?php echo $operator->code_rechargeatz;?>" class="form-control" placeholder="RECHARGE A2Z OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">EXIOMS :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-xs-7">
										<input type="text" name="code_exioms" id="code_exioms" value="<?php echo $operator->code_exioms;?>" class="form-control" placeholder="EXIOMS OPERATOR CODE">
									</div>
									<div class="col-xs-5">
										<input type="text" name="service_type_exioms" id="service_type_exioms" value="<?php echo $operator->service_type_exioms;?>" class="form-control" placeholder="EXIOMS SERVICE TYPE">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">AARAV MULTIRECHARGE :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-xs-7">
										<input type="text" name="code_aarav" id="code_aarav" value="<?php echo $operator->code_aarav;?>" class="form-control" placeholder="AARAV OPERATOR CODE">
									</div>
									<div class="col-xs-5">
										<input type="text" name="service_type_aarav" id="service_type_aarav" value="<?php echo $operator->service_type_aarav;?>" class="form-control" placeholder="AARAV SERVICE TYPE">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">AMBIKA RECHARGE :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_ambika" id="code_ambika" value="<?php echo $operator->code_ambika;?>" class="form-control" placeholder="AMBIKA OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">CYBERPLATE :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_cyberplat" id="code_cyberplat" value="<?php echo $operator->code_cyberplat;?>" class="form-control" placeholder="CYBERPLAT OPERATOR CODE">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">OFFLINE RECHARGE :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_offline" id="code_offline" value="<?php echo $operator->code_offline;?>" class="form-control" placeholder="OFFLINE OPERATOR CODE">
							</div>
						</div>
						
						<div class="form-group"><div class="col-sm-12"><hr></div></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">API :</label>
							<div class="col-sm-8 jrequired">
								<select name="api" id="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM api_list WHERE status = '1'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->api_id;?>"<?php if($operator->api_id==$result->api_id) {?> selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1"<?php if($operator->status=='1') {?> selected="selected"<?php } ?>>Active</option>
									<option value="0"<?php if($operator->status=='0') {?> selected="selected"<?php } ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="row">
					<div class="col-md-12">
						<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
							<i class="fa fa-save"></i> Save
						</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
