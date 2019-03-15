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
		$min_amount = htmlentities(addslashes($_POST['min_amount']),ENT_QUOTES);
		$max_amount = htmlentities(addslashes($_POST['max_amount']),ENT_QUOTES);	
		$sur_amount = htmlentities(addslashes($_POST['sur_amount']),ENT_QUOTES);
		$code_egpay = htmlentities(addslashes($_POST['code_egpay']),ENT_QUOTES);
		$service_type_egpay = htmlentities(addslashes($_POST['service_type_egpay']),ENT_QUOTES);
		$code_royal_capital = htmlentities(addslashes($_POST['code_royal_capital']),ENT_QUOTES);
		$code_achariya = htmlentities(addslashes($_POST['code_achariya']),ENT_QUOTES);
		$code_modem = htmlentities(addslashes($_POST['code_modem']),ENT_QUOTES);
		$code_modem_rp = htmlentities(addslashes($_POST['code_modem_rp']),ENT_QUOTES);
		$code_roundpay = htmlentities(addslashes($_POST['code_roundpay']),ENT_QUOTES);
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
			$db->execute("UPDATE `operators` SET `operator_name`='".$operator_name."', `operator_code`='".$operator_code."', `operator_longcode`='".$operator_longcode."', `service_type`='".$_POST['service_type']."', `minimum_amount`='".$min_amount."', `maximum_amount`='".$max_amount."', `code_egpay`='".$code_egpay."', `service_type_egpay`='".$service_type_egpay."', `code_royal_capital`='".$code_royal_capital."', `code_achariya`='".$code_achariya."', `code_modem`='".$code_modem."', `code_modem_rp`='".$code_modem_rp."', `code_roundpay`='".$code_roundpay."', `code_exioms`='".$code_exioms."', `service_type_exioms`='".$service_type_exioms."', `code_aarav`='".$code_aarav."', `service_type_aarav`='".$service_type_aarav."', `code_ambika`='".$code_ambika."', `code_cyberplat`='".$code_cyberplat."', `code_offline`='".$code_offline."', `api_id`='".$_POST['api']."', `surcharge`='".$_POST['surcharge']."', `surcharge_amount`='".$sur_amount."', `is_auto_switch`='".$is_auto_switch."', `status`='".$_POST['status']."' WHERE operator_id = '".$request_id."' ");
			$error = 3;
		}		
	}
}

$operator = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id = '".$request_id."' ");
if(!$operator) header("location:operator.php");

$meta['title'] = "Operator";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#surcharge_no").click(function(){
		jQuery("#sur_amount").prop('readonly', true);
  	});
	jQuery("#surcharge_yes").click(function(){
		jQuery("#sur_amount").prop('readonly', false);
  	});
	jQuery('#operatorForm').validate({
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
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Operators <small>/ Add</small></div>
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
				<h3 class="box-title"><i class="fa fa-plus-square"></i> Add</h3>
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
							<label class="col-sm-4 control-label">Surcharge :</label>
							<div class="col-sm-8 jrequired">
								<div class="row">
									<div class="col-sm-3">
										<label class="radio-inline">
											<input type="radio" name="surcharge" id="surcharge_no" value="n" <?php if($operator->surcharge == 'n') {?>checked="checked"<?php } ?>> No
										</label>
										<label class="radio-inline">
											<input type="radio" name="surcharge" id="surcharge_yes" value="y" <?php if($operator->surcharge == 'y') {?>checked="checked"<?php } ?>> Yes
										</label>
									</div>
									<div class="col-sm-9">
										<input type="text" name="sur_amount" id="sur_amount" value="<?php echo $operator->surcharge_amount;?>" <?php if($operator->surcharge == 'n') {?>readonly=""<?php } ?> class="form-control">
									</div>
								</div>
							</div>
						</div>						
						<div class="form-group">
							<label class="col-sm-4 control-label">Is Safe Mode :</label>
							<div class="col-sm-8 jrequired">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="is_auto_switch" <?php if($operator->is_auto_switch=='y'){?>checked="checked"<?php }?> /> Yes
									</label>
								</div>
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
							<label class="col-sm-4 control-label">ROYAL CAPITAL :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="code_royal_capital" id="code_royal_capital" value="<?php echo $operator->code_royal_capital;?>" class="form-control" placeholder="ROYAL CAPITAL OPERATOR CODE">
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
									<option value="<?php echo $result->api_id;?>" <?php if($operator->api_id==$result->api_id) {?>selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
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
									<option value="1" <?php if($operator->status=='1') {?>selected="selected"<?php } ?>>Active</option>
									<option value="0" <?php if($operator->status=='0') {?>selected="selected"<?php } ?>>Inactive</option>
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
