<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['sb_banner_1'])) {
	if($_POST['notificationForm1']=='') {
		$error = 1;		
	} else {
		$can_update=true;
		$status = 0;		
		$file = "";
		print_r($_FILES);die;	
			if(!empty($_FILES["banner_1"]["name"])) {	

				$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg');
				$max_filesize = 5242880; //5MB
				$str = "../uploads/";
				$filename = $_FILES['banner_1']['name'];
				$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
				
				if(!in_array($ext, $allowed_filetypes)) {
					$error = 2;
					$can_update = false;
				} else if($ext!="") {	
					$file = time()."-".$_FILES['banner_1']['name'];
					
					$s = move_uploaded_file($_FILES['banner_1']['tmp_name'], $str.$file);
					$img_len = strlen($str.$file);
					$im_format = substr($str.$file, ($img_len-3), 3);					
				}
			}
		
		if($can_update) {
			$db->execute("UPDATE `app_notification` SET `notification_date`='".date('Y-m-d')."',`file_path`='".$file."',`file_name`='".$filename."',`status`='".$status."' WHERE id=1");
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

</script>
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
			<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th>S.No</th>
							<th>Date</th>
							<th>File</th>
							<th>Image</th>
							<th>Set</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt=0;						
						$query = $db->query("SELECT * FROM app_notification ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo ++$scnt;?></td>
							<td><b><?php echo $result->notification_date;?></b></td>
							<td><?php echo $result->file_name;?></td>
							<td><img  style="width: 200px;height: 90px;" src="http://99-604-99-605.com/uploads/<?php echo $result->file_path;?>" ></td>
							
							<td>
								<a type="button" href="set_banner.php?id=<?php echo $scnt;?>" class="btn btn-info btn-lg" data-toggle="modal">Set Banner</a>
							</td>
							
						</tr>
						<?php } ?>
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
