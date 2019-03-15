<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['move']['distributor'])) {
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['uid']=='' || $_POST['mdist_id']=='' || $_POST['pin']=='') {
		$error = 1;		
	} else {
		$uid = htmlentities(addslashes($_POST['uid']),ENT_QUOTES);
		$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['staff']."'");	
		if($admin_info && $admin_info->pin==hashPin($_POST['pin'])) {	
			$query = $db->query("SELECT * FROM apps_user WHERE dist_id='".$uid."' AND user_type='5'");
			while($result = $db->fetchNextObject($query)) {	
				$db->execute("UPDATE apps_user SET mdist_id='".$_POST['mdist_id']."' WHERE user_id='".$result->user_id."'");
			}
			$db->execute("UPDATE apps_user SET mdist_id='".$_POST['mdist_id']."' WHERE user_id='".$request_id."' AND user_type='4'");
			$error = 3;
		} else {
			$error = 2;
		}	
	}
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' AND user_type='4'");
if(!$user) header("location:index.php");

if($user->user_type == '1') {	
	if(empty($sP['api_user']['update'])) {
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '3') {
	if(empty($sP['md_user']['update'])) { 
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '4') {
	if(empty($sP['ds_user']['update'])) { 
		include('permission.php');
		exit(); 
	}
} else if($user->user_type == '5') {
	if(empty($sP['rt_user']['update'])) { 
		include('permission.php');
		exit(); 
	}
} else {
	include('permission.php');
	exit();
}

$meta['title'] = getUserType($user->user_type)." - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#userForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to move distributor?")) {
        form.submit();
      }
		},
	  rules: {
	  	mdist_id: {
				required: true
			},
			pin: {
				required:true,
				digits: true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<style>
.list-buttons .btn {
	margin-bottom:10px;
	text-align:left;
}
</style>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Move <i class="fa fa-random"></i> <?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<?php if($user->user_type == '4') {?>
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type == '5') {?>
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } ?>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> User has been move successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> PIN not matched!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Some parameters are empty!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-random"></i> Move User</h3>
					</div>
					<form action="" method="post" id="userForm">
					<input type="hidden" name="uid" id="uid" value="<?php echo $user->uid;?>" class="form-control" placeholder="UID">
					<div class="box-body min-height-300">					
						<div class="row padding-50">
							<div class="col-sm-6 col-sm-offset-3">
								<div class="form-group">
									<label class="control-label">Master Distributor :</label>
									<div class="jrequired">
										<select name="mdist_id" id="mdist_id" class="form-control">
											<option value="">-----select master distributor-----</option>
											<?php
											$qry = $db->query("SELECT * FROM apps_user WHERE user_type = '3' AND status = '1' ORDER BY company_name ASC ");
											while($rst = $db->fetchNextObject($qry)) {?>
											<option value="<?php echo $rst->uid;?>"><?php echo $rst->company_name;?> (<?php echo $rst->uid;?>)</option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<div class="text-red">
										If you are confirm to move, the distributor and all its retailer will move to the selected master distributor. so before you will proceed please cross check all the values. 
									</div>
								</div>								
								<div class="form-group">
									<div class="row">
										<label class="col-sm-8 control-label">Total Associate Retailers :</label>
										<div class="col-sm-4">
											<b><?php echo $db->countOf("apps_user", "dist_id='".$user->uid."' AND user_type = '5'"); ?></b>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">Pin :</label>
									<div class="jrequired">
										<input type="text" name="pin" id="pin" class="form-control" placeholder="Enter 4 Digit PIN">
									</div>
								</div>
								<div class="form-group">&nbsp;</div>
								<div class="form-group">
									<div class="jrequired">
										<input type="submit" name="submit" id="submit" value="Confirm &amp; Submit" class="btn btn-block btn-primary">
									</div>
								</div>
							</div>
						</div>
						<!--end of row-->
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-gears"></i> Services</h3>
					</div>
					<div class="box-body no-padding">
						<table class="table table-striped">
							<tr>
								<td>Recharge / Bills </td>
								<td width="25%">
									<?php if($user->is_recharge=='a') { ?>
									<a href="#" class="label label-success"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } else { ?>
									<a href="#" class="label label-danger"><?php echo getServiceStatus($user->is_recharge);?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td>Money Transfer </td>
								<td>
									<?php if($user->is_money=='a') { ?>
									<a href="#" class="label label-success"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } else { ?>
									<a href="#" class="label label-danger"><?php echo getServiceStatus($user->is_money);?></a>
									<?php } ?>
								</td>
							</tr>							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function actionRow(vlu, vlu1, vlu2) {
	if(vlu!="" && vlu1!="" && vlu2!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="user-service-action.php?id="+vlu+"&action="+vlu1+"&service="+vlu2;
		}
	}
}
</script> 
<?php include('footer.php'); ?>