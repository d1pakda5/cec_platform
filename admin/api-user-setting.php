<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
if(!$user) header("location:index.php");
if(isset($_POST['ip_submit'])) {
	if($_POST['ip1'] == '') {	
		$error = 1;			
	} else {
		$ip1 =  htmlentities(addslashes($_POST['ip1']),ENT_QUOTES);
		$ip2 =  htmlentities(addslashes($_POST['ip2']),ENT_QUOTES);	
		$ip3 =  htmlentities(addslashes($_POST['ip3']),ENT_QUOTES);	
		$ip4 =  htmlentities(addslashes($_POST['ip4']),ENT_QUOTES);	
		$info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id = '".$user->user_id."' AND uid = '".$user->uid."' ");
		if($info) {	
			$db->execute("UPDATE `apps_user_api_settings` SET `ip1` = '".$ip1."', `ip2` = '".$ip2."', `ip3` = '".$ip3."', `ip4` = '".$ip4."' WHERE api_setting_id = '".$info->api_setting_id."' ");	
		} else {			
			$db->execute("INSERT INTO `apps_user_api_settings`(`api_setting_id`, `user_id`, `uid`, `ip1`, `ip2`, `ip3`, `ip4`) VALUES ('', '".$user->user_id."', '".$user->uid."', '".$ip1."', '".$ip2."', '".$ip3."', '".$ip4."')");
		}
		$error = 3;
		header("location:api-user-setting.php?id=".$request_id."&error=3");
	}
}

$api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id = '".$user->user_id."' AND uid = '".$user->uid."' ");
if($api_info) {
	$ip1 = $api_info->ip1;
	$ip2 = $api_info->ip2;
	$ip3 = $api_info->ip3;
	$ip4 = $api_info->ip4;
	$uid = $api_info->uid;
	$key = $api_info->user_key;
} else {
	$ip1 = "";
	$ip2 = "";
	$ip3 = "";
	$ip4 = "";
	$uid = $_SESSION['apiuser_uid'];
	$key = "";
}
$meta['title'] = "API Setting";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#ipForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add/update IP Address?")) {
				form.submit();
			}
		},
	  rules: {
			ip1: {
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
			<div class="page-title">API <small>/ Setting</small></div>
			<div class="pull-right">				
				<a href="api-reverse-url.php?id=<?php echo $request_id;?>" class="btn btn-primary"><i class="fa fa-link"></i> URL</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> IP Address updated successfully
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
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil"></i> Add IP Address</h3>
					</div>
					<form action="" method="post" id="ipForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">IP Addresses (1) :</label>
									<div class="jrequired">
										<input type="text" name="ip1" id="ip1" value="<?php echo $ip1;?>" class="form-control" placeholder="IP ADDRESS 1">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">IP Addresses (2) :</label>
									<div class="jrequired">
										<input type="text" name="ip2" id="ip2" value="<?php echo $ip2;?>" class="form-control" placeholder="IP ADDRESS 2">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">IP Addresses (3) :</label>
									<div class="jrequired">
										<input type="text" name="ip3" id="ip3" value="<?php echo $ip3;?>" class="form-control" placeholder="IP ADDRESS 3">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">IP Addresses (4) :</label>
									<div class="jrequired">
										<input type="text" name="ip4" id="ip4" value="<?php echo $ip4;?>" class="form-control" placeholder="IP ADDRESS 4">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="ip_submit" id="ip_submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Changes
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-key"></i> Genderate Key</h3>
					</div>
					<form action="" method="post" id="keyForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">UID :</label>
									<div class="jrequired">
										<input type="text" name="uid" id="uid" value="<?php echo $uid;?>" readonly="" class="form-control" placeholder="UID">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">User Key :</label>
									<div class="jrequired">
										<input type="text" name="user_key" id="user_key" value="<?php echo $key;?>" class="form-control" placeholder="GENERATE KEY">
									</div>
								</div>
								<div class="form-group"><br><br><br><br><br><br>
								</div>
								<div class="form-group">
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="generate_key" id="generate_key" disabled="disabled" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Generate Key
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