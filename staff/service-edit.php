<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['operator']['service'])) { 
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['service_name'] == '') {
		$error = 1;		
	} else {		
		$service_name = htmlentities(addslashes($_POST['service_name']),ENT_QUOTES);
		$exists = $db->queryUniqueObject("SELECT * FROM service_type WHERE service_name = '".$service_name."' AND service_type_id != '".$request_id."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("UPDATE `service_type` SET `service_name`='".$service_name."', `status`='".$_POST['status']."' WHERE service_type_id = '".$request_id."' ");
			$error = 3;
		}		
	}
}

$service = $db->queryUniqueObject("SELECT * FROM service_type WHERE service_type_id = '".$request_id."' ");
if(!$service) header("location:service.php");

$meta['title'] = "Service | Edit";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Service <small>/ Edit</small></div>
			<div class="pull-right">
				<a href="service.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
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
				<h3 class="box-title"><i class="fa fa-pencil-square"></i> Edit</h3>
			</div>
			<form action="" method="post" id="operatorForm" class="form-horizontal">
			<div class="box-body padding-50 min-height-300">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Service Name :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="service_name" id="service_name" value="<?php echo $service->service_name;?>" class="form-control" placeholder="SERVICE NAME">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1" <?php if($service->status=='1') {?>selected="selected"<?php } ?>>Active</option>
									<option value="0" <?php if($service->status=='0') {?>selected="selected"<?php } ?>>Inactive</option>
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
