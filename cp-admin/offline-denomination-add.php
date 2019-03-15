<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['service_type_id'] == '') {
		$error = 1;		
	} else {		
		$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);	
		$amount_from=$_POST['amount_from'];
		$amount_to=$_POST['amount_to'];
		$exists = $db->queryUniqueObject("SELECT * FROM offline_denominations WHERE service_type_id = '".$_POST['service_type_id']."' AND user_type='".$_POST['user_type']."' and (amount_values LIKE '%".$amount."%' Or amount_from='".$_POST['amount_from']."' OR amount_to='".$_POST['amount_to']."' )");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("INSERT INTO `offline_denominations`(`id`, `service_type_id`,`user_type`, `amount_from`, `amount_to`, `amount_values`, `status`) VALUES ('', '".$_POST['service_type_id']."','".$_POST['user_type']."','".$amount_from."','".$amount_to."','".$amount."', '".$_POST['status']."') ");
			$error = 3;
		}		
	}
}


$meta['title'] = "Offline Denomination";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#operatorForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update operator?")) {
        form.submit();
      }
		},
	  rules: {
			service_type_id: {
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
			<div class="page-title">Offline Denominations <small>/ Add</small></div>
			<div class="pull-right">
				<a href="offline-denomination.php" class="btn btn-primary"><i class="fa fa-th-list"></i> List</a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Added successfully
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
							<label class="col-sm-4 control-label">User Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="user_type" id="user_type" class="form-control">
									<option value=""></option>
									<option value="1">API User</option>
									<option value="5">Retailer</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Service Type :</label>
							<div class="col-sm-8 jrequired">
								<select name="service_type_id" id="service_type_id" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM service_type WHERE status = '1'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->service_type_id;?>"><?php echo $result->service_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Denomination(s) :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="amount" id="amount" class="form-control" placeholder="Denominations"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Amount From :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="amount_from" id="amount_from" class="form-control" placeholder="Amount From"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Amount To :</label>
							<div class="col-sm-8 jrequired">
								<textarea name="amount_to" id="amount_to" class="form-control" placeholder="Amount To"></textarea>
							</div>
						</div>
						<div class="form-group"></div>
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