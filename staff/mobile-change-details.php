<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['is_mobile']) || $sP['is_mobile'] != 'y') { 
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submitxxx'])) {
	if($_POST['old_mobile'] == '' || $_POST['mobile'] == '' || $_POST['status'] == '') {
		$error = 1;		
	} else {		
		$old_mobile = htmlentities(addslashes($_POST['old_mobile']),ENT_QUOTES);
		$mobile = htmlentities(addslashes($_POST['mobile']),ENT_QUOTES);
		if($_POST['status'] == '1') {
			$find = $db->queryUniqueObject("SELECT * FROM apps_user WHERE mobile = '".$old_mobile."' ");
			if($find) {
				$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE mobile = '".$mobile."' ");
				if($exists) {
					$error = 2;
				} else {
					$db->execute("UPDATE `apps_user` SET `mobile` = '".$mobile."', `username` = '".$mobile."' WHERE `user_id` = '".$find->user_id."' ");
					$db->execute("UPDATE `mobile_change_request` SET `request_status` = '1', `update_date` = NOW(), `status` = '".$_POST['status']."' WHERE `request_id` = '".$request_id."' ");
					$message = "Dear user, your mobile number has been changed to : ".$mobile;
					smsSendSingle($mobile, $message, 'registration');
					$error = 4;
				}			
			} else {
				$error = 3;
			}	
		} else if($_POST['status'] == '2') {
			$db->execute("UPDATE `mobile_change_request` SET `request_status` = '1', `update_date` = NOW(), `status` = '".$_POST['status']."' WHERE `request_id` = '".$request_id."' ");
			$error = 4;
		}
	}
}

$request = $db->queryUniqueObject("SELECT mob.*, user.fullname FROM mobile_change_request mob LEFT JOIN apps_user user ON mob.request_user = user.uid WHERE mob.request_id = '".$request_id."' ");
if(!$request) header("location:mobile-change-request.php");

$meta['title'] = "Mobile Change";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#mobileForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update mobile?")) {
        form.submit();
      }
		},
	  rules: {
	  	old_mobile: {
				required: true
			},
			mobile: {
				required:true
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Change user mobile number</h3>
					</div>
					<form action="" method="post" id="mobileForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">Users :</label>
									<div class="col-sm-8 jrequired">
										<p class="form-control-static"><?php echo $request->fullname;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Old Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="old_mobile" id="old_mobile" readonly="" value="<?php echo $request->mobile_old;?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">New Mobile :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="mobile" id="mobile" readonly="" value="<?php echo $request->mobile_new;?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status :</label>
									<div class="col-sm-8 jrequired">
										<select name="status" id="status" class="form-control">
											<option value=""></option>
											<option value="1">Accept Change</option>
											<option value="2">Reject Request</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<?php if($request->request_status == '0') {?>
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Mobile
								</button>
								<?php } else { ?>
								<button type="submit" disabled="disabled" id="disabled" class="btn btn-primary pull-right">
									<i class="fa fa-refresh"></i> Updated......
								</button>
								<?php } ?>
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
