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
	if($_POST['amount'] == '' || $_POST['remark'] == '' || $_POST['pin'] == '') {
		$error = 1;		
	} else {		
		$amount =  htmlentities(addslashes($_POST['amount']),ENT_QUOTES);		
		$remark =  htmlentities(addslashes($_POST['remark']),ENT_QUOTES);		
		$pin =  htmlentities(addslashes($_POST['pin']),ENT_QUOTES);		
		$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['admin']."' ");
		if($admin_info->pin == hashPin($pin)) {
			$admin_wallet = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet");
			if($admin_wallet) {	
				$closing_balance = $admin_wallet->balance + $amount;
				$db->execute("UPDATE `apps_admin_wallet` SET `balance` = '".$closing_balance."' ");
			} else {
				$closing_balance = $amount;
				$db->execute("INSERT INTO `apps_admin_wallet`(`balance`) VALUES ('".$closing_balance."')");
			}		
			$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_status`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '0', '0', 'cr', '".$amount."', '".$closing_balance."', 'FUND', 'NA', '".$remark."', '1', '0', '".$_SESSION['admin']."')");
			mailFundTransfer($admin_info->email, $admin_info->fullname, $amount, $closing_balance, date("d-m-Y H:i:s"));
			header("location:update-fund.php?error=3");
		}	else {
			$error = 2;	
		}
	}
}
$meta['title'] = "Update Fund";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update fund?")) {
        form.submit();
      }
		},
	  rules: {
	  	amount: {
				required: true
			},
			remark: {
				required:true
			},
			pin: {
				required:true
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
			<div class="page-title">My Account <small>/ Update Fund</small></div>
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
			<i class="fa fa-warning"></i> Invalid PIN!
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
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Update Fund</h3>
					</div>
					<form action="" method="post" id="fundForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Amount :</label>
									<div class="jrequired">
										<input type="text" name="amount" id="amount" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="control-label">Remark :</label>
									<div class="jrequired">
										<textarea name="remark" id="remark" class="form-control"></textarea>
									</div>
								</div>								
								<div class="form-group">
									<label class="control-label">Pin :</label>
									<div class="jrequired">
										<input type="password" name="pin" id="pin" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Fund
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-inr"></i> Current Balance</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="col-md-12 text-center bg-success">
						<h1><b>
							<?php
							$admin = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet");
							if($admin) {
								echo round($admin->balance,2);
							} else {
								echo "0";
							}?></b> Rs
						</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>