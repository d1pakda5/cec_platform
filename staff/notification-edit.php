<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if($sP['is_notification'] != 'y') { 
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['notification'] == '') {
		$error = 1;		
	} else {
		
		$notification = htmlentities(addslashes($_POST['notification']),ENT_QUOTES);		
		$db->execute("UPDATE `notifications` SET `user_type` = '".$_POST['user_type']."', `notification_content` = '".$notification."', `notification_date` = NOW(), `status` = '".$_POST['status']."' WHERE notification_id = '".$request_id."'");
			$error = 3;
	}
}

$notification_info = $db->queryUniqueObject("SELECT * FROM notifications WHERE notification_id = '".$request_id."' ");
if(!$notification_info) header("location:notification.php");

$meta['title'] = "Notifications | Edit";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#notificationForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update notification?")) {
        		form.submit();
      		}
		},
	    rules: {
	    	notification: {
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
			<div class="page-title">Notifications <small>/ Edit</small></div>
			<div class="pull-right">
				<a href="notification.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
			<form action="" method="post" id="notificationForm" class="form-horizontal">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Details :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="notification" id="notification" class="form-control" placeholder="Notifications" rows="8"><?php echo $notification_info->notification_content;?></textarea>
							</div>
						</div>
						<div class="form-group"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">User Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="user_type" id="user_type" class="form-control">
									<option value=""></option>
									<option value="0" <?php if($notification_info->user_type=='0') {?>selected="selected"<?php } ?>>All</option>
									<option value="1" <?php if($notification_info->user_type=='1') {?>selected="selected"<?php } ?>>API User</option>
									<option value="3" <?php if($notification_info->user_type=='3') {?>selected="selected"<?php } ?>>Master Distributor</option>
									<option value="4" <?php if($notification_info->user_type=='4') {?>selected="selected"<?php } ?>>Distributor</option>
									<option value="5" <?php if($notification_info->user_type=='5') {?>selected="selected"<?php } ?>>Retailer</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1" <?php if($notification_info->status=='1') {?>selected="selected"<?php } ?>>Active</option>
									<option value="0" <?php if($notification_info->status=='0') {?>selected="selected"<?php } ?>>Inactive</option>
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
