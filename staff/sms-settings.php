<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['sms']['setting'])) { 
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['module'] == '' || empty($_POST['module'])) {
		$error = 1;		
	} else {
		foreach($_POST['module'] as $key=>$val) {		
			$db->execute("UPDATE `sms_module` SET `sms_api_id`='".$val."' WHERE sms_module_id = '".$key."' ");
		}
		$error = 3;
	}
}

if(isset($_POST['update'])) {
	if($_POST['api_id'] == '') {
		$error = 1;		
	} else {
		$db->execute("UPDATE `sms_module` SET `sms_api_id`='".$_POST['api_id']."' ");
		$error = 3;
	}
}

$smsapi = array();
$query = $db->query("SELECT * FROM sms_api WHERE status = '1' ORDER BY sms_api_id ASC");
while($result = $db->fetchNextObject($query)) {
	$smsapi[] = array('id'=>$result->sms_api_id, 'name'=>$result->api_name); 
}

function getSmsModuleNameSettings($name) {
	$arr = explode("_", $name);
	$arr_2 = isset($arr[1]) ? $arr[1] : "";
	return ucwords($arr[0]." ".$arr_2);
}
$meta['title'] = "SMS API's | Edit";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">SMS API's <small>/ Edit</small></div>
			<div class="pull-right">
				<a href="sms-api.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
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
		<div class="row">
			<div class="col-sm-8">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Edit</h3>
					</div>
					<form action="" method="post" id="operatorForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<?php 
								$query = $db->query("SELECT * FROM sms_module");
								while($result = $db->fetchNextObject($query)) {
								?>
								<div class="form-group">
									<label class="col-sm-4 control-label"><?php echo getSmsModuleNameSettings($result->sms_module_name);?> :</label>
									<div class="col-sm-8 jrequired">
										<select name="module[<?php echo $result->sms_module_id;?>]" id="module_<?php echo $result->sms_module_id;?>" class="form-control">
											<option value=""></option>
											<?php foreach($smsapi as $data) { ?>
											<option value="<?php echo $data['id'];?>" <?php if($data['id'] == $result->sms_api_id) {?>selected="selected"<?php } ?>><?php echo $data['name'];?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Save
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Bulk Update</h3>
					</div>
					<form action="" method="post" id="smsapiForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Select API :</label>
									<div class="jrequired">
										<select name="api_id" id="api_id" class="form-control">
											<option value=""></option>
											<?php foreach($smsapi as $data) { ?>
											<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="update" id="update" class="btn btn-primary pull-right">
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
</div>
<?php include('footer.php'); ?>
