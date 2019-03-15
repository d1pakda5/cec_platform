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
	if($_POST['old_mobile'] == '' || $_POST['mobile'] == '') {
		$error = 1;		
	} else {		
		$old_mobile = htmlentities(addslashes($_POST['old_mobile']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);		
		$find = $db->queryUniqueObject("SELECT * FROM apps_user WHERE mobile = '".$old_mobile."' ");
		if($find) {
			$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE mobile = '".$mobile."' AND user_id != '".$find->user_id."' ");
			if($exists) {
				$error = 2;
			} else {
				$db->execute("UPDATE `apps_user` SET `mobile` = '".$mobile."', `username` = '".$mobile."' WHERE `user_id` = '".$find->user_id."' ");
				$db->execute("INSERT INTO `mobile_change_request`(`request_id`, `request_user`, `request_to`, `mobile_old`, `mobile_new`, `request_date`, `request_status`, `update_date`, `status`) VALUES ('', '".$find->uid."', '".$find->uid."', '".$old_mobile."', '".$mobile."', NOW(), '1', NOW(), '1') ");
				$message = "Dear user, your mobile number has been changed to : ".$mobile;
				smsSendSingle($mobile, $message, 'registration');
				$error = 4;
			}			
		} else {
			$error = 3;
		}
	}
}

$meta['title'] = "Mobile Change";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#user_type").change(function(){
		var type = jQuery("#user_type").val();
		jQuery.ajax({ 
			url: "ajax/list-users.php",
			type: "POST",
			data: "type="+type,
			async: false,
			success: function(data) {
				jQuery("#uid").html(data);
			}
		});
	});
	jQuery("#uid").change(function(){
		var uid = jQuery("#uid").val();
		jQuery.ajax({ 
			url: "ajax/user-mobile.php",
			type: "POST",
			data: "uid="+uid,
			async: false,
			success: function(data) {
				jQuery("#old_mobile").val(data);
			}
		});
	});
	jQuery('#mobileForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update mobile?")) {
        form.submit();
      }
		},
	  rules: {
			user_type: {
				required: true
			},
			uid: {
				required: true
			},
	  	old_mobile: {
				required: true
			},
			mobile: {
				required:true
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
			<div class="page-title">Mobile Change <small>/ Request</small></div>
			<div class="pull-right">
				<a href="mobile-change-request.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
			</div>
		</div>
		<?php if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Old mobile number not matched.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Already mobile number is registered by users!
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Change user mobile number</h3>
					</div>
					<form action="" method="post" id="mobileForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">Users :</label>
									<div class="col-sm-8 jrequired">
										<div class="row">
											<div class="col-sm-4">
												<select name="user_type" id="user_type" class="form-control">
													<option value=""></option>
													<option value="1">API User</option>
													<option value="3">Master Distributor</option>
													<option value="4">Distributor</option>
													<option value="5">Retailer</option>
												</select>
											</div>
											<div class="col-sm-8">
												<select name="uid" id="uid" class="form-control">
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Old Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="old_mobile" id="old_mobile" readonly="" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">New Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Change Mobile
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
