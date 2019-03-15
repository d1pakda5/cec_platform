<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['ntype']=='' || $_POST['notification']=='' || $_POST['status']=='') {
		$error = 1;		
	} else {
		$can_update = true;		
		$notification = mysql_real_escape_string($_POST['notification']);
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
			$db->execute("INSERT INTO `notifications`(`notification_id`, `user_type`, `ntype`, `ctype`, `notification_content`, `notification_files`, `notification_date`,`notification_date_from`,`notification_date_to`, `status`) VALUES ('', '".$_POST['user_type']."', '".$_POST['ntype']."', '".$_POST['ctype']."', '".$notification."', '".$file."', NOW(), '".$_POST['notification_date_from']."','".$_POST['notification_date_to']."', '".$_POST['status']."')");
			$error = 3;
			header("location:notification-add.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
	}
}
$meta['title'] = "Notifications | Add";
include('header.php');
?>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<script src="../js/nicEdit/nicEdit.js"></script>
<script>
bkLib.onDomLoaded(function(){
  var myInstance = new nicEditor().panelInstance('notification');
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery('#notification_date_from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#notification_date_to').datepicker({
		format: 'yyyy-mm-dd'
	});
	
});

jQuery(document).ready(function(){
	jQuery('#notificationForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add notification?")) {
      	form.submit();
			}
		},
		rules: {
			notification: {
				required: true
			},
			notification_date_from: {
				required: true
			},
			notification_date_to: {
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
			var cTypeVal = jQuery("#ctype").val();
			if(cTypeVal=='i') {
				jQuery("#imgPane").css('display','');
				jQuery("#textPane").css('display','none');
			} else {
				jQuery("#textPane").css('display','');
				jQuery("#imgPane").css('display','none');
			}
		} else if(this.value=='s') {
			jQuery("#cType").css('display','none');
			jQuery("#imgPane").css('display','none');
			jQuery("#textPane").css('display','');
		} else {
			jQuery("#imgPane").css('display','none');
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
			<div class="page-title">Notifications <small>/ Add</small></div>
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
		<?php } else if($error==3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Added successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error==1) { ?>
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
									<option value="s">Scroll</option>
									<option value="p">PopUp</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="cType" style="display:none;">
							<label class="col-sm-4 control-label">Content Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="ctype" id="ctype" class="form-control">
									<option value="">select content type</option>
									<option value="t">Text</option>
									<option value="i">Image</option>
								</select>
							</div>
						</div>
						<div id="textPane" class="form-group">
							<label class="col-sm-4 control-label">Details :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="notification" id="notification" class="form-control" placeholder="Enter text content here" rows="8"></textarea>
							</div>
						</div>
						<div id="imgPane" class="form-group" style="display:none">
							<label class="col-sm-4 control-label">Image Upload :</label>
							<div class="col-sm-8 jrequired">
								<input type="file" name="notify_image" id="notify_image"/>
							</div>
						</div>
						<div class="form-group"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">User Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="user_type" id="user_type" class="form-control">
									<option value=""></option>
									<option value="0">All</option>
									<option value="1">API User</option>
									<option value="3">Master Distributor</option>
									<option value="4">Distributor</option>
									<option value="5">Retailer</option>
								</select>
							</div>
						</div>
						<div id="date_from"  class="form-group">
									<label class="col-sm-4 control-label">Notification From Date :</label>
									<div class="col-sm-5 jrequired">
									    <input type="text" size="8" name="notification_date_from" id="notification_date_from" value="" placeholder="Notification From Date " class="form-control">
									</div>
								</div>
								<div id="date_to" class="form-group">
									<label class="col-sm-4 control-label">Notification To Date :</label>
									<div class="col-sm-5 jrequired">
									    <input type="text" size="8" name="notification_date_to" id="notification_date_to" value="" placeholder="Notification To Date " class="form-control">
									</div>
								</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
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
