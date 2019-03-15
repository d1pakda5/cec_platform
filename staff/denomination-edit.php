<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['operator']['denom'])) { 
	include('permission.php');
	exit(); 
}
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['operator_id'] == '' || $_POST['amount'] == '' || $_POST['api_id'] == '') {
		$error = 1;		
	} else {		
		$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);		
		$exists = $db->queryUniqueObject("SELECT * FROM operators_denominations WHERE operator_id = '".$_POST['operator_id']."' AND amount_values LIKE '%".$amount."%' AND denomination_id != '".$request_id."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("UPDATE `operators_denominations` SET `operator_id` = '".$_POST['operator_id']."', `api_id` = '".$_POST['api_id']."', `amount_values` = '".$amount."', `status` = '".$_POST['status']."' WHERE denomination_id = '".$request_id."' ");
			$error = 3;
		}		
	}
}

$denomination = $db->queryUniqueObject("SELECT * FROM operators_denominations WHERE denomination_id = '".$request_id."' ");
if(!$denomination) header("location:denomination.php");

$meta['title'] = "Operator Denomination";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#operatorForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update operator denomination?")) {
        form.submit();
      }
		},
	  rules: {
			operator_id: {
				required:true
			},
			amount: {
				required: true
			},
			api_id: {
				required: true
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
			<div class="page-title">Operator Denominations <small>/ Edit</small></div>
			<div class="pull-right">
				<a href="denomination.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
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
			<form action="" method="post" id="operatorForm" class="form-horizontal">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">						
						<div class="form-group">
							<label class="col-sm-4 control-label">Operator Name :</label>
							<div class="col-sm-8 jrequired">
								<select name="operator_id" id="operator_id" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM operators WHERE status = '1' ORDER BY service_type,operator_name ASC ");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->operator_id;?>" <?php if($denomination->operator_id==$result->operator_id) {?>selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Denomination(s) :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="amount" id="amount" class="form-control" placeholder="Denominations"><?php echo $denomination->amount_values;?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">API :</label>
							<div class="col-sm-8 jrequired">
								<select name="api_id" id="api_id" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM api_list WHERE status = '1'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->api_id;?>" <?php if($denomination->api_id==$result->api_id) {?>selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1" <?php if($denomination->status=='1') {?>selected="selected"<?php } ?>>Active</option>
									<option value="0" <?php if($denomination->status=='0') {?>selected="selected"<?php } ?>>Inactive</option>
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