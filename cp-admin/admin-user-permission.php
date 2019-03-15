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
	$api_user = isset($_POST['a']) ? serialize($_POST['a']) : NULL;
	$md_user = isset($_POST['m']) ? serialize($_POST['m']) : NULL;
	$ds_user = isset($_POST['d']) ? serialize($_POST['d']) : NULL;
	$rt_user = isset($_POST['r']) ? serialize($_POST['r']) : NULL;
	$userkyc = isset($_POST['kyc']) ? serialize($_POST['kyc']) : NULL;
	$moveuser = isset($_POST['move']) ? serialize($_POST['move']) : NULL;
	$fund = isset($_POST['fund']) ? serialize($_POST['fund']) : NULL;
	$complaint = isset($_POST['complaint']) ? serialize($_POST['complaint']) : NULL;
	$support = isset($_POST['support']) ? $_POST['support'] : 'n';
	$notification = isset($_POST['notification']) ? $_POST['notification'] : 'n';
	$operator = isset($_POST['operator']) ? serialize($_POST['operator']) : NULL;
	$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : 'n';
	$sms = isset($_POST['sms']) ? serialize($_POST['sms']) : NULL;
	$report = isset($_POST['report']) ? serialize($_POST['report']) : NULL;
    $operator_active = isset($_POST['operator_active']) ? $_POST['operator_active'] : '0';
    $is_dir_ret_move = isset($_POST['is_dir_ret_move']) ? $_POST['is_dir_ret_move'] : '0';
     $dir_ret_commission = isset($_POST['dir_ret_commission']) ? $_POST['dir_ret_commission'] : '0';
     $assign_manager = isset($_POST['assign_manager']) ? $_POST['assign_manager'] : '0';
     $is_close_api_complaint = isset($_POST['is_close_api_complaint']) ? $_POST['is_close_api_complaint'] : '0';
     $is_express = isset($_POST['is_express']) ? $_POST['is_express'] : '0';
     $offline_recharge = isset($_POST['offline_recharge']) ? $_POST['offline_recharge'] : '0';
	$exists = $db->queryUniqueObject("SELECT * FROM apps_admin_permission WHERE admin_id = '".$request_id."'");
	if($exists) {
		$db->execute("UPDATE `apps_admin_permission` SET `api_user`='".$api_user."', `md_user`='".$md_user."', `ds_user`='".$ds_user."', `rt_user`='".$rt_user."', `userkyc`='".$userkyc."', `moveuser`='".$moveuser."', `fund`='".$fund."', `complaint`='".$complaint."', `is_support`='".$support."', `is_notification`='".$notification."', `operators`='".$operator."', `is_mobile`='".$mobile."', `sms`='".$sms."', `reports`='".$report."', `is_operator_active`='".$operator_active."',`offline_recharge`='".$offline_recharge."',`is_dir_ret_move`='".$is_dir_ret_move."' ,`dir_ret_commission`='".$dir_ret_commission."',`assign_manager`='".$assign_manager."' ,`is_close_api_complaint`='".$is_close_api_complaint."',`is_express`='".$is_express."' WHERE admin_id = '".$request_id."' ");
		$error = 2;
	} else {			
		$db->execute("INSERT INTO `apps_admin_permission`(`permission_id`, `admin_id`, `api_user`, `md_user`, `ds_user`, `rt_user`, `userkyc`, `moveuser`, `fund`, `complaint`, `is_support`, `is_notification`, `operators`, `is_mobile`, `sms`, `reports`,'is_operator_active','offline_recharge',`is_dir_ret_move`,`dir_ret_commission`,`assign_manager`,`is_close_api_complaint`,`is_express`) VALUES ('', '".$request_id."', '".$api_user."', '".$md_user."', '".$ds_user."', '".$rt_user."', '".$userkyc."', '".$moveuser."', '".$fund."', '".$complaint."', '".$support."', '".$notification."', '".$operator."', '".$mobile."', '".$sms."', '".$report."', '".$operator_active."','".$offline_recharge."', '".$is_dir_ret_move."','".$dir_ret_commission."','".$assign_manager."','".$is_close_api_complaint."','".$is_express."')");	
		$error = 3;
	}
}

$admin = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$request_id."' ");
if(!$admin) header("location:admin-user.php");

$per = $db->queryUniqueObject("SELECT * FROM apps_admin_permission WHERE admin_id = '".$request_id."' ");
if($per) {
	$per_apiuser = unserialize($per->api_user);	
	$per_mduser = unserialize($per->md_user);
	$per_dsuser = unserialize($per->ds_user);
	$per_rtuser = unserialize($per->rt_user);
	$per_kyc = unserialize($per->userkyc);
	$per_move = unserialize($per->moveuser);
	$per_fund = unserialize($per->fund);
	$per_complaint = unserialize($per->complaint);
	$per_support = $per->is_support;
	$per_notification = $per->is_notification;
	$per_operator = unserialize($per->operators);
	$per_mobile = $per->is_mobile;
	$per_sms = unserialize($per->sms);
	$per_report = unserialize($per->reports);
	$per_operator_active = $per->is_operator_active;
	$per_is_dir_ret_move = $per->is_dir_ret_move;
	$per_dir_ret_commission = $per->dir_ret_commission;
	$per_assign_manager = $per->assign_manager;
	$per_is_close_api_complaint = $per->is_close_api_complaint;
	$per_is_express = $per->is_express;
    $per_offline_recharge = $per->offline_recharge;

}

$meta['title'] = "Admin User - Permission";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	jQuery('#perForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update user?")) {
        form.submit();
      }
		},
	  rules: {
	  	fullname: {
				required: true
			},
			mobile: {
				required: true,
				digits: true,
				minlength: 10,
				maxlength: 10
			},
			email: {
				required:true,
				email: true
			},
			username: {
				required: true
			},
			password: {
				required: false,
				minlength: 6
			},
			pin: {
				required: false,
				minlength: 4,
				maxlength: 4,
				digits: true
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
			<div class="page-title">Admin User <small>/ Permission</small></div>
			<div class="pull-right">
				<a href="admin-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully!
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
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-plus-square"></i> User permission</h3>
					</div>
					<form action="" method="post" id="permissionForm" class="form-horizontal">
					<div class="box-body min-height-300">
						<div class="row padding-50">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-sm-4 control-label">API User :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="a[view]" value="y" <?php if(isset($per_apiuser['view']) && $per_apiuser['view'] == 'y') {?> checked="checked"<?php } ?> /> View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="a[add]" value="y" <?php if(isset($per_apiuser['add']) && $per_apiuser['add'] == 'y') {?> checked="checked"<?php } ?> /> Add &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="a[update]" value="y" <?php if(isset($per_apiuser['update']) && $per_apiuser['update'] == 'y') {?> checked="checked"<?php } ?> /> Update </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Master Distributor :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="m[view]" value="y" <?php if(isset($per_mduser['view']) && $per_mduser['view'] == 'y') {?> checked="checked"<?php } ?> /> View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="m[add]" value="y" <?php if(isset($per_mduser['add']) && $per_mduser['add'] == 'y') {?> checked="checked"<?php } ?> /> Add &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="m[update]" value="y" <?php if(isset($per_mduser['update']) && $per_mduser['update'] == 'y') {?> checked="checked"<?php } ?> /> Update </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Distributor :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="d[view]" value="y" <?php if(isset($per_dsuser['view']) && $per_dsuser['view'] == 'y') {?> checked="checked"<?php } ?> /> View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="d[add]" value="y" <?php if(isset($per_dsuser['add']) && $per_dsuser['add'] == 'y') {?> checked="checked"<?php } ?> /> Add &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="d[update]" value="y" <?php if(isset($per_dsuser['update']) && $per_dsuser['update'] == 'y') {?> checked="checked"<?php } ?> /> Update </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Retailer :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="r[view]" value="y" <?php if(isset($per_rtuser['view']) && $per_rtuser['view'] == 'y') {?> checked="checked"<?php } ?> /> View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="r[add]" value="y" <?php if(isset($per_rtuser['add']) && $per_rtuser['add'] == 'y') {?> checked="checked"<?php } ?> /> Add &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="r[update]" value="y" <?php if(isset($per_rtuser['update']) && $per_rtuser['update'] == 'y') {?> checked="checked"<?php } ?> /> Update </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Users KYC :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="kyc[view]" value="y" <?php if(isset($per_kyc['view']) && $per_kyc['view'] == 'y') {?> checked="checked"<?php } ?> /> View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="kyc[add]" value="y" <?php if(isset($per_kyc['add']) && $per_kyc['add'] == 'y') {?> checked="checked"<?php } ?> /> Add &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="kyc[update]" value="y" <?php if(isset($per_kyc['update']) && $per_kyc['update'] == 'y') {?> checked="checked"<?php } ?> /> Edit  &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="kyc[verify]" value="y" <?php if(isset($per_kyc['verify']) && $per_kyc['verify'] == 'y') {?> checked="checked"<?php } ?> /> Verify </label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Move User :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="move[retailer]" value="y" <?php if(isset($per_move['retailer']) && $per_move['retailer'] == 'y') {?> checked="checked"<?php } ?> /> Retailer &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="move[distributor]" value="y" <?php if(isset($per_move['distributor']) && $per_move['distributor'] == 'y') {?> checked="checked"<?php } ?> /> Distributor &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Fund Add :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="fund[add]" value="y" <?php if(isset($per_fund['add']) && $per_fund['add'] == 'y') {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Fund Add Limit :</label>
									<div class="col-sm-6 jrequired">
										<div class="row">
											<div class="col-xs-3">
												<input type="text" name="fund[minlimit]" value="<?php if(isset($per_fund['minlimit'])) { echo $per_fund['minlimit']; }?>" class="form-control" placeholder="Minimum Amount" />
											</div>
											<div class="col-xs-1 text-center"><p class="form-control-static">To</p></div>
											<div class="col-xs-5">
												<input type="text" name="fund[maxlimit]" value="<?php if(isset($per_fund['maxlimit'])) { echo $per_fund['maxlimit']; }?>" class="form-control" placeholder="Maximum Amount" />												
											</div>
											<div class="col-xs-1 text-center"><p class="form-control-static">Rs</p></div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Complaint Refund :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="complaint[refund]" value="y" <?php if(isset($per_complaint['refund']) && $per_complaint['refund'] == 'y') {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Refund Limit :</label>
									<div class="col-sm-4 jrequired">
										<input type="text" name="complaint[limit]" value="<?php if(isset($per_complaint['limit'])) { echo $per_complaint['limit']; }?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Support Reply :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="support" value="y" <?php if(isset($per_support) && $per_support == 'y') {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Notification :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="notification" value="y" <?php if(isset($per_notification) && $per_notification == 'y') {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Operator</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="operator[opr]" value="y" <?php if(isset($per_operator['opr']) && $per_operator['opr'] == 'y') {?> checked="checked"<?php } ?> /> Add/Edit/View &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="operator[service]" value="y" <?php if(isset($per_operator['service']) && $per_operator['service'] == 'y') {?> checked="checked"<?php } ?> /> Services &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="operator[api]" value="y" <?php if(isset($per_operator['api']) && $per_operator['api'] == 'y') {?> checked="checked"<?php } ?> /> API &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="operator[denom]" value="y" <?php if(isset($per_operator['denom']) && $per_operator['denom'] == 'y') {?> checked="checked"<?php } ?> /> Denomination &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Mobile Update :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="mobile" value="y" <?php if(isset($per_mobile) && $per_mobile == 'y') {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Operator Active/Inactive</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="operator_active" value="1" <?php if(isset($per_operator_active) && $per_operator_active == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Offline Recharge</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="offline_recharge" value="1" <?php if(isset($per_offline_recharge) && $per_offline_recharge == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Move Direct Retailer</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_dir_ret_move" value="1" <?php if(isset($per_is_dir_ret_move) && $per_is_dir_ret_move == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Users Commission</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="dir_ret_commission" value="1" <?php if(isset($per_dir_ret_commission) && $per_dir_ret_commission == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Assign Manager</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="assign_manager" value="1" <?php if(isset($per_assign_manager) && $per_assign_manager == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Close API Complaint</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_close_api_complaint" value="1" <?php if(isset($per_is_close_api_complaint) && $per_is_close_api_complaint == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Operator Change to Express:</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="is_express" value="1" <?php if(isset($per_is_express) && $per_is_express == "1") {?> checked="checked"<?php } ?> /> Yes &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">SMS Settings :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="sms[setting]" value="y" <?php if(isset($per_sms['setting']) && $per_sms['setting'] == 'y') {?> checked="checked"<?php } ?> /> SMS Settings &nbsp;&nbsp;</label>
											<label><input type="checkbox" name="sms[send]" value="y" <?php if(isset($per_sms['send']) && $per_sms['send'] == 'y') {?> checked="checked"<?php } ?> /> Send SMS &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Reports :</label>
									<div class="col-sm-8 jrequired">
										<div class="checkbox">
											<label><input type="checkbox" name="report[recharge]" value="y" <?php if(isset($per_report['recharge']) && $per_report['recharge'] == 'y') {?> checked="checked"<?php } ?> /> Recharge &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[rechargestatus]" value="y" <?php if(isset($per_report['rechargestatus']) && $per_report['rechargestatus'] == 'y') {?> checked="checked"<?php } ?> /> Recharge Status &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[transaction]" value="y" <?php if(isset($per_report['transaction']) && $per_report['transaction'] == 'y') {?> checked="checked"<?php } ?> /> Transaction &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[usertransaction]" value="y" <?php if(isset($per_report['usertransaction']) && $per_report['usertransaction'] == 'y') {?> checked="checked"<?php } ?> /> User Transaction &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[transactionstatus]" value="y" <?php if(isset($per_report['transactionstatus']) && $per_report['transactionstatus'] == 'y') {?> checked="checked"<?php } ?> /> Transaction Status &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[longcode]" value="y" <?php if(isset($per_report['longcode']) && $per_report['longcode'] == 'y') {?> checked="checked"<?php } ?> /> Long Code &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[sentsms]" value="y" <?php if(isset($per_report['sentsms']) && $per_report['sentsms'] == 'y') {?> checked="checked"<?php } ?> /> Sent SMS &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[apiresponse]" value="y" <?php if(isset($per_report['apiresponse']) && $per_report['apiresponse'] == 'y') {?> checked="checked"<?php } ?> /> API Response &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[apicallback]" value="y" <?php if(isset($per_report['apicallback']) && $per_report['apicallback'] == 'y') {?> checked="checked"<?php } ?> /> API Callback &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[usercallback]" value="y" <?php if(isset($per_report['usercallback']) && $per_report['usercallback'] == 'y') {?> checked="checked"<?php } ?> /> User Callback &nbsp;&nbsp;</label>
										</div>
										<div class="checkbox">
											<label><input type="checkbox" name="report[login]" value="y" <?php if(isset($per_report['login']) && $per_report['login'] == 'y') {?> checked="checked"<?php } ?> /> Login Activity &nbsp;&nbsp;</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
									<i class="fa fa-save"></i> Save Permission
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>