<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	$reverse_url = htmlentities(addslashes($_POST['url']),ENT_QUOTES);
	$info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id = '".$_SESSION['apiuser']."' AND uid = '".$_SESSION['apiuser_uid']."' ");
	if($info) {	
		$db->execute("UPDATE `apps_user_api_settings` SET `reverse_url` = '".$reverse_url."' WHERE api_setting_id = '".$info->api_setting_id."' ");	
	} else {			
		$db->execute("INSERT INTO `apps_user_api_settings`(`api_setting_id`, `user_id`, `uid`, `reverse_url`) VALUES ('', '".$_SESSION['apiuser']."', '".$_SESSION['apiuser_uid']."', '".$reverse_url."')");
	}
	$error = 4;
	header("location:reverse-notification.php?token=".$token."&error=4");
}

$api_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id = '".$_SESSION['apiuser']."' AND uid = '".$_SESSION['apiuser_uid']."' ");
if($api_info) {
	$reverse_url = $api_info->reverse_url;
} else {
	$reverse_url = "";
}

$meta['title'] = "Setting API Reverse Notification";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#urlForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update URL?")) {
				form.submit();
			}
		},
	  rules: {
			url: {
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
	<div class="container">
		<div class="page-header">
			<div class="page-title">Setting <small>/ API Reverse Notification</small></div>
		</div>
		<?php if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> URL has been updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
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
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil"></i> Update Reverse Notification</h3>
					</div>
					<form action="" method="post" id="urlForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Notification URL :</label>
									<div class="jrequired">
										<input type="text" name="url" id="url" value="<?php echo $reverse_url;?>" class="form-control" placeholder="REVERSE NOTIFICATION URL">
									</div>
								</div>
								<div class="form-group">
									<div class="well">
										<p>http://yourdomain.com/your_file_name?txnid=#TXNID#&status=#STATUS#&opref=#OPERATORREF#&msg=#MESSAGE#&usertxn=#USERTXN#</p>
										<h4>Parameters</h4>
										<ol>
											<li>yourdomain.com = Your Server Domain or IP (Mandatory)</li>
											<li>your_file_name = Your Processing File (Mandatory)</li>
											<li>#TXNID# = The Unique Transaction ID (Mandatory)</li>
											<li>#USERTXN# = User Specific Transaction ID</li>
											<li>#STATUS# = SUCCESS, FAILURE, PENDING, REVERSED, ERROR</li>
											<li>#OPERATORREF# = Operator Txn Ref. Number</li>
											<li>#MESSAGE# = Details of Transactions</li>
										</ol>
										<h4>Security Tips &amp; Warnings</h4>
										<ol>
											<li>Accept only the request from our Server IP <b><?php echo $_SERVER['SERVER_ADDR'];?></b></li>
											<li>Don't share the URL with anyone.</li>
											<li>Better to pass some Additional Key along with URL</li>
										</ol>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update URL
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