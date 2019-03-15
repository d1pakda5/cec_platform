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
	if($_POST['user_type'] == '' || $_POST['uid'] == '' || $_POST['type'] == '' || $_POST['amount'] == '' ||  $_POST['pin'] == '') {
		$error = 1;		
	} else {
		$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['admin']."' ");	
		if($admin_info && $admin_info->pin == hashPin($_POST['pin'])) {	
			$uid = htmlentities(addslashes($_POST['uid']),ENT_QUOTES);		
			$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);			
			$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
			$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
			
			
				/* Add daily sale entry*/
				date_default_timezone_set('Asia/Kolkata');
				$date= htmlentities(addslashes($_POST['date']),ENT_QUOTES);	
				$time= date("H:i:s"); 
				$admin_id = htmlentities(addslashes($_POST['manager_id']),ENT_QUOTES);
				if($_POST['type'] == 'cr') {
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$uid."','".$amount."','cr')");
				
				} else if($_POST['type'] == 'dr') {
					$amount1=0-$amount;
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$uid."','".$amount1."','dr')");

				}
$error == 4;
				/* End daily sale entry*/
				
				
				/*
				* Credit Transaction
				*/
				
			
				
			
			
		} else {
			$error = 2;
		}	
	}
}

$meta['title'] = "Fund Add/Deduct";
include('header.php');
?>
 <link rel="stylesheet" href="http://select2.github.io/select2//select2-3.5.3/select2.css?ts=2015-08-29T20%3A09%3A48%2B00%3A00">
  <script src="http://select2.github.io/select2/select2-3.5.3/select2.js?ts=2015-08-29T20%3A09%3A48%2B00%3A00"></script>
  <link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">


jQuery(document).ready(function() {
	jQuery('#date').datepicker({
		format: 'yyyy-mm-dd'
	});
	
});
	
jQuery(document).ready(function(){	

	jQuery("#user_type").change(function(){
		jQuery.ajax({ 
			url: "ajax/list-users.php",
			type: "POST",
			data: "type="+jQuery(this).val(),
			async: false,
			success: function(data) {
				jQuery("select#uid").html(data);
			}
		});
	});
	$(document).ready(function() { $("#uid").select2();$("#employee_id").select2(); });
	jQuery("select#uid").change(function(){
  	jQuery.post("ajax/user-balance.php",{uid: jQuery(this).val(), ajax: 'true'}, function(j){
			jQuery('#balance').val(j);
			 });
  	jQuery.post("ajax/list_manager.php",{uid: jQuery(this).val(), ajax: 'true'}, function(data){
			jQuery("select#manager_id").html(data);
			 });
	
  });
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add sale entry?")) {
        form.submit();
      }
		},
	  rules: {
	  	user_type: {
				required: true
			},
			uid: {
				required:true
			},
			manager_id: {
				required:true
			},
			balance: {
				required: true,
				number: true
			},
			type: {
				required:true
			},
			amount: {
				required:true,
				number: true
			},
			remark: {
				required: true
			},
			pin: {
				required: true,
				minlength: 4,
				maxlength: 4
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
			<div class="page-title">Fund <small>/ Add/Deduct</small></div>
			<div class="pull-right">
				<a href="fund-request.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 5) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Transaction has been rollback due to internal error.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> Transaction successful, Wallet updated.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Insufficient balance in account to deduct.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Invalid PIN!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some parameters are missing!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-8">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Fund Add/Deduct</h3>
					</div>
					<form action="" method="post" id="fundForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-sm-4 control-label">Users Type :</label>
									<div class="col-sm-8 jrequired">
										<select name="user_type" id="user_type" class="form-control">
											<option value=""></option>
											<option value="1">API User</option>
											<option value="4">Distributor</option>
											<option value="5">Retailer</option>
											<option value="6">Direct Retailer</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Users :</label>
									<div class="col-sm-8 jrequired">
										<select name="uid" id="uid" class="" style="width: 100%" onchange="manager_bind()">
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Account Manager:</label>
									<div class="col-sm-8 jrequired">
										<select name="manager_id" id="manager_id" class="form-control"></select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Date :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" size="8" name="date" id="date" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder=" Date" class="form-control">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-4 control-label">Transaction Type :</label>
									<div class="col-sm-8 jrequired">
										<select name="type" id="type" class="form-control">
											<option value=""></option>
											<option value="cr">Add Fund (Credit)</option>
											<option value="dr">Deduct Fund (Debit)</option>	
										</select>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-sm-4 control-label"> Amount :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="amount" id="amount" class="form-control">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-4 control-label"> Pin :</label>
									<div class="col-sm-8 jrequired">
										<input type="password" name="pin" id="pin" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Notepad</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<textarea name="note" id="note" rows="21" cols="15" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('footer.php'); ?>