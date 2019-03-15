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
	if($_POST['user_type'] == '' || $_POST['uid'] == '' || $_POST['type'] == '' || $_POST['amount'] == '' || $_POST['remark'] == '' || $_POST['pin'] == '') {
		$error = 1;		
	} else {
		$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['admin']."' ");	
		if($admin_info && $admin_info->pin == hashPin($_POST['pin'])) {	
			$uid = htmlentities(addslashes($_POST['uid']),ENT_QUOTES);		
			$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);			
			$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);
			$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet ORDER BY admin_wallet_id ASC");
			if($_POST['type'] == 'cr') {				
				if($admin_wallet->balance >= $amount) {	
					$is_balance = true;
				} else {
					$is_balance = false;
				}
			} else {
				$is_balance = true;
			}
			
			if($is_balance) {		
						
				$db->query("START TRANSACTION");
				$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$uid."' ");
				if($_POST['type'] == 'cr') {
					$admin_closing_balance = $admin_wallet->balance - $amount;
					$closing_balance = $wallet->balance + $amount;
					$to_type = "dr";
					$from_type = "cr";
				} else if($_POST['type'] == 'dr') {
					if(trim($wallet->balance) >= trim($amount)) {
						$closing_balance = $wallet->balance - $amount;
						$admin_closing_balance = $admin_wallet->balance + $amount;
						$to_type = "cr";
						$from_type = "dr";
					} else {
						header("location:fund-add.php?error=3");
						exit();
					}
				}
                    
                    /* Add daily sale entry*/
				date_default_timezone_set('Asia/Kolkata');
				$date= date("Y-m-d");
				$time= date("H:i:s"); 
				$admin_id = htmlentities(addslashes($_POST['manager_id']),ENT_QUOTES);
				if($_POST['type'] == 'cr') {
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$uid."','".$amount."','cr')");
				} else if($_POST['type'] == 'dr') {
					$amount1=0-$amount;
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$uid."','".$amount1."','dr')");
				}

				/* End daily sale entry*/
				
				/*
				* Debit Transaction
				*/
				$db->query("UPDATE apps_admin_wallet SET balance = '".$admin_closing_balance."' WHERE admin_wallet_id = '".$admin_wallet->admin_wallet_id."' ");
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '".$uid."', '".$to_type."', '".$amount."', '".$admin_closing_balance."', 'FUND', '', '".$remark."', '0', '".$_SESSION['admin']."') ");
							
				/*
				* Credit Transaction
				*/
				$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
				$ts1 = mysql_affected_rows();
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '0', '".$from_type."', '".$amount."', '".$closing_balance."', 'FUND', '', '".$remark."', '0', '".$_SESSION['admin']."') ");					
				$ts2 = mysql_affected_rows();
				$updated_ref_id = $db->lastInsertedId();
				
				if($wallet && $ts1 && $ts2) {
					$commit = $db->query("COMMIT");
					if($commit) {
						$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
						if($_POST['type'] == 'cr') {
							$message = smsFundTransfer($amount, SITENAME, $user_info->company_name);
						} else {
							$message = smsFundDeduct($amount, $user_info->company_name, SITENAME);
						}
						smsSendSingle($user_info->mobile, $message, 'fund_transfer');
						if($_POST['type'] == 'cr') {
							mailFundTransfer($user_info->email, $user_info->company_name, $amount, $closing_balance, date("d-m-Y"));
						}
						$error = 4;
						header("location:fund-add.php?error=4");
					} else {
						$error = 5;
					}
				} else {
					$error = 5;
				}
				
			} else {
				$error = 3;
			}
			
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
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">


 
	
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
	$(document).ready(function() { $("#uid").select2(); });
	jQuery("select#uid").change(function(){
  	jQuery.post("ajax/user-balance.php",{uid: jQuery(this).val(), ajax: 'true'}, function(j){
			jQuery('#balance').val(j);
    })
    jQuery.post("ajax/list_manager.php",{uid: jQuery(this).val(), ajax: 'true'}, function(data){
			jQuery("select#manager_id").html(data);
			 });
  });
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add fund?")) {
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
										<select name="uid" id="uid" class="" style="width: 100%">
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
									<label class="col-sm-4 control-label">Balance :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="balance" id="balance" class="form-control">
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
									<label class="col-sm-4 control-label"> Remark :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="remark" id="remark" class="form-control"></textarea>
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