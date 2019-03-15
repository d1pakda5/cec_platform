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
	if($_POST['website_name'] == '' || $_POST['support_number'] == '' || $_POST['support_email'] == '') {
		$error = 1;		
	} else {		
		$website_name = htmlentities(addslashes($_POST['website_name']),ENT_QUOTES);
		$website_url =  htmlentities(addslashes($_POST['website_url']),ENT_QUOTES);
		$support_number = htmlentities(addslashes($_POST['support_number']),ENT_QUOTES);
		$support_email =  htmlentities(addslashes($_POST['support_email']),ENT_QUOTES);
		
		$f1 = "";
		if($_FILES['logo']['name'] !='' ) {			
			$allowed_filetypes = array('.jpg','.gif','.bmp','.png','.jpeg','.JPG','.GIF','.BMP','.PNG');
			$max_filesize = 524288;
			$str= "../uploads/";
			$filename = $_FILES['logo']['name'];
			$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);
			if(!in_array($ext, $allowed_filetypes)) {
				$error=2;
			} else if($ext!="") {	
				$f1 = time().$_FILES['logo']['name'];
				$s = move_uploaded_file($_FILES['logo']['tmp_name'],$str.$f1);
				$img_len = strlen($str.$f1);
				$im_format = substr($str.$f1, ($img_len-3), 3);
			}
		}	
		
		$exists = $db->queryUniqueObject("SELECT * FROM website_profile WHERE website_id = '".$_POST['website_id']."' ");
		if($exists) {
			$db->execute("UPDATE `website_profile` SET `website_name`='".$website_name."', `website_url`='".$website_url."', `support_number`='".$support_number."', `support_email`='".$support_email."' WHERE website_id = '".$exists->website_id."' ");
			if($f1 != '') {
				$db->execute("UPDATE `website_profile` SET `website_logo`='".$f1."' WHERE website_id = '".$exists->website_id."' ");	
			}
			$error = 3;
		} else {
			$db->execute("INSERT INTO `website_profile`(`website_id`, `website_uid`, `website_name`, `website_url`, `support_number`, `support_email`, `website_logo`) VALUES ('', '".$_POST['uid']."', '".$website_name."', '".$website_url."', '".$support_number."', '".$support_email."', '".$f1."')");	
		}
	}
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
if(!$user) header("location:index.php");
$web = $db->queryUniqueObject("SELECT * FROM website_profile WHERE website_uid = '".$user->uid."' ");
$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#webForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to continue?")) {
        form.submit();
      }
		},
	  rules: {
	  	website_name: {
				required: true
			},
			support_number: {
				required: true
			},
			support_email: {
				required:true,
				email: true
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
			<div class="page-title"><?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<a href="master-distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Found duplicate values!
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Update Profile</h3>
					</div>
					<form action="" method="post" enctype="multipart/form-data" id="webForm" class="form-horizontal">
					<input type="hidden" name="uid" id="uid" value="<?php echo $user->uid;?>">
					<input type="hidden" name="website_id" id="website_id" value="<?php echo $web->website_id;?>">
					<div class="box-body min-height-300">
						<div class="row padding-50">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Website Name :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="website_name" id="website_name" value="<?php echo $web->website_name;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Website URL :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="website_url" id="website_url" value="<?php echo $web->website_url;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Logo :</label>
									<div class="col-sm-8 jrequired" style="overflow:hidden;">
										<input type="file" name="logo" id="logo"><br>
										<?php if($web && $web->website_logo != '') { ?>
											<p><img src="../uploads/<?php echo $web->website_logo;?>" /></p>
											<p><a href="#">Remove Logo</a></p>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="support_number" id="support_number" value="<?php echo $web->support_number;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="support_email" id="support_email" value="<?php echo $web->support_email;?>" class="form-control">
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
									<i class="fa fa-save"></i> Update
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