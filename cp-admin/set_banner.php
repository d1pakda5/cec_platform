<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;

if(isset($_POST['submit'])) {
	
	if($_FILES=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
			
			if(!empty($_FILES["notify_image"]["name"])) {	

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
		
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id='".$id."'");
			$error = 3;
			header("location:mobile_notification.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
   }
}
if(isset($_POST['sb_banner_2'])) {
	if($_POST['banner_2']=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
			if($_FILES['banner_2']['name']!='' ) {			
				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['banner_2']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['banner_2']['name'];
					$s = move_uploaded_file($_FILES['banner_2']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		}
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id=2");
			$error = 3;
			header("location:mobile_notification.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
}
if(isset($_POST['sb_banner_3'])) {
	if($_POST['banner_3']=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
			if($_FILES['banner_3']['name']!='' ) {			
				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['banner_3']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['banner_3']['name'];
					$s = move_uploaded_file($_FILES['banner_3']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		}
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id=3");
			$error = 3;
			header("location:mobile_notification.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
}
if(isset($_POST['sb_banner_4'])) {
	if($_POST['banner_4']=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
			if($_FILES['banner_4']['name']!='' ) {			
				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['banner_4']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['banner_4']['name'];
					$s = move_uploaded_file($_FILES['banner_4']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		}
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id=4");
			$error = 3;
			header("location:mobile_notification.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
}
if(isset($_POST['sb_banner_5'])) {
	if($_POST['banner_5']=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
			if($_FILES['banner_5']['name']!='' ) {			
				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['banner_5']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['banner_5']['name'];
					$s = move_uploaded_file($_FILES['banner_5']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		}
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id=5");
			$error = 3;
			header("location:mobile_notification.php?error=".$error);
			exit();
		} else {
			$error = 4;
		}
}

$meta['title'] = "Notifications | Add";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Mobile Notifications <small>/ Add</small></div>
			<div class="pull-right">
				<a href="mobile_notification.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
			<div class="box-body no-padding min-height-480">
			<form action="" method="post" id="notificationForm1" class="form-horizontal" enctype="multipart/form-data">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div id="imgPane" class="form-group">
							<label class="col-sm-4 control-label">Image Upload :</label>
							<div class="col-sm-8 jrequired">
								<input type="file" name="notify_image" id="notify_image"/>
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
</div>
<?php include('footer.php'); ?>
