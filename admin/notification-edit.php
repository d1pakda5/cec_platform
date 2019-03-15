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
	if($_POST['notification'] == '') {
		$error = 1;		
	} else {	
		$can_update = true;		
		$notification = htmlentities(addslashes($_POST['notification']),ENT_QUOTES);
		$file = "";
		if($_POST['ctype']=='i') {
			if($_FILES['notify_image']['name']!='' ) {			
				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['notify_image']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['notify_image']['name'];
					$s = move_uploaded_file($_FILES['notify_image']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		}
		if($can_update) {	
			$db->execute("UPDATE `notifications` SET `user_type`='".$_POST['user_type']."', `ntype`='".$_POST['ntype']."', `ctype`='".$_POST['ctype']."', `notification_content`='".$notification."', `notification_date`=NOW(), `status`='".$_POST['status']."' WHERE notification_id='".$request_id."'");
			if($file!="") {
				$db->execute("UPDATE `notifications` SET `notification_files`='".$file."' WHERE notification_id='".$request_id."'");
			}
			$error = 3;
			header("location:notification-edit.php?id=".$request_id."&error=".$error);
			exit();
		} else {
			$error = 4;
		}
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
	jQuery('#ntype').change(function() {
		if(this.value=='p') {
			jQuery("#cType").css('display','');			
		} else {
			jQuery("#cType").css('display','none');
		}
	});
	jQuery('#ctype').change(function() {
		if(this.value=='i') {
			jQuery("#imgPane").css('display','');
			jQuery("#textPane").css('display','none');			
		} else {
			jQuery("#imgPane").css('display','none');
			jQuery("#textPane").css('display','');
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
		<?php if($error==4) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Cannot update, please check file uploaded is valid.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==1) { ?>
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
			<form action="" method="post" id="notificationForm" class="form-horizontal" enctype="multipart/form-data">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="ntype" id="ntype" class="form-control">
									<option value="">select type</option>
									<option value="s"<?php if($notification_info->ntype=='s'){?> selected="selected"<?php } ?>>Scroll</option>
									<option value="p"<?php if($notification_info->ntype=='p'){?> selected="selected"<?php } ?>>PopUp</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="cType" <?php if($notification_info->ntype=='s'){?>style="display:none;"<?php } ?>>
							<label class="col-sm-4 control-label">Content Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="ctype" id="ctype" class="form-control">
									<option value="">select content type</option>
									<option value="t"<?php if($notification_info->ctype=='t'){?> selected="selected"<?php } ?>>Text</option>
									<option value="i"<?php if($notification_info->ctype=='i'){?> selected="selected"<?php } ?>>Image</option>
								</select>
							</div>
						</div>
						<div id="textPane" class="form-group" <?php if($notification_info->ctype=='i'){?>style="display:none;"<?php } ?>>
							<label class="col-sm-4 control-label">Details :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="notification" id="notification" class="form-control" placeholder="Enter text content here" rows="8"><?php echo $notification_info->notification_content;?></textarea>
							</div>
						</div>
						<div id="imgPane" class="form-group" <?php if($notification_info->ctype=='t'){?>style="display:none;"<?php } ?>>
							<label class="col-sm-4 control-label">Image Upload :</label>
							<div class="col-sm-8 jrequired">
								<input type="file" name="notify_image" id="notify_image"/>
								<p class="margin-top-10"><img src="../uploads/<?php echo $notification_info->notification_files;?>" class="img-responsive" /></p>
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
