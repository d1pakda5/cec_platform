<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("common.php");
if(!isset($_GET['token']) || $_GET['token'] != $token) { exit("Token not match"); }
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['uid'] == '' || $_POST['amount'] == '' || $_POST['remark'] == '' || $_POST['pin'] == '') {
		$error = 1;		
	} else {		
		if($aDistributor->pin == hashPin($_POST['pin'])) {	
			$uid = htmlentities(addslashes($_POST['uid']),ENT_QUOTES);		
			$amount = htmlentities(addslashes($_POST['amount']),ENT_QUOTES);			
			$remark = htmlentities(addslashes($_POST['remark']),ENT_QUOTES);	
			/*
			* Start Transaction
			*/		
			$db->query("START TRANSACTION");			
			$ds_wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$_SESSION['distributor_uid']."' ");
			if($ds_wallet && (($ds_wallet->balance - $ds_wallet->cuttoff) >= $amount) && ($amount > 0)) {	
			    
			    if($_POST['manager_id']!==""||$_POST['manager_id']!==null)
			    {
			    /* Add daily sale entry*/
				date_default_timezone_set('Asia/Kolkata');
				$date= date("Y-m-d");
				$time= date("H:i:s"); 
				$admin_id = htmlentities(addslashes($_POST['manager_id']),ENT_QUOTES);
				$db->execute("INSERT INTO daily_sale_entries(sale_date, sale_time, account_manager_id, emp_uid, sale_amount,type) VALUES ('".$date."','".$time."','".$admin_id."','".$uid."','".$amount."','cr')");
			    }
			    else
			    {
			        
			    }
				
				    
			

				/* End daily sale entry*/
			    
			    
				/*
				* Debit Transaction
				*/
				$ds_closing_balance = $ds_wallet->balance - $amount;
				$db->query("UPDATE apps_wallet SET balance = '".$ds_closing_balance."' WHERE wallet_id = '".$ds_wallet->wallet_id."' ");
				$ts1 = mysql_affected_rows();
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$_SESSION['distributor_uid']."', '".$uid."', 'dr', '".$amount."', '".$ds_closing_balance."', 'FUND', '', '".$remark."', '4', '".$_SESSION['distributor_uid']."') ");
				$ts2 = mysql_affected_rows();	
				
		
				
				
				
				
				/*
				* Credit Transaction
				*/
				$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$uid."' ");				
				$closing_balance = $wallet->balance + $amount;				
				$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
				$ts3 = mysql_affected_rows();
				$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '".$_SESSION['distributor_uid']."', 'cr', '".$amount."', '".$closing_balance."', 'FUND', '', '".$remark."', '4', '".$_SESSION['distributor_uid']."') ");
				$ts4 = mysql_affected_rows();
				$updated_ref_id = $db->lastInsertedId();
				
				if($ts1 && $ts2 && $ts1 && $ts2) {
					$commit = $db->query("COMMIT");
					if($commit) {
						$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$uid."' ");
						$message = smsFundTransfer($amount, $aDistributor->company_name, $user_info->company_name);
						smsSendSingle($aDistributor->mobile, $message, 'fund_transfer');
						smsSendSingle($user_info->mobile, $message, 'fund_transfer');
						$error = 4;
						header("location:fund-add.php?token=".$token."&error=4");
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
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$_SESSION['distributor_uid']."'");
if($wallet) {
	$current_balance = $wallet->balance;
	$cutoff_balance = $wallet->cuttoff;
} else {
	$current_balance = "0";
	$cutoff_balance = "0";
}
$meta['title'] = "Fund Add/Deduct";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	jQuery("select#uid").change(function(){
  	jQuery.post("../ajax/user-balance.php",{uid: jQuery(this).val(), ajax: 'true'}, function(j){
			jQuery('#balance').val(j);
    });
    	jQuery.post("../ajax/list_manager.php",{uid: jQuery(this).val(), ajax: 'true'}, function(data){
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
				<a href="user-fund-request.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
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
			<i class="fa fa-check"></i> Insufficient balance in account to add fund.
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
									<label class="col-sm-4 control-label">Users :</label>
									<div class="col-sm-8 jrequired">
										<select name="uid" id="uid" class="form-control">
										<option value="">Select Retailer</option>
										<?php
										$qry = $db->query("SELECT * FROM apps_user WHERE user_type='5' AND dist_id='".$_SESSION['distributor_uid']."' AND status='1' ORDER BY company_name ASC ");
										while($rst = $db->fetchNextObject($qry)) {?>
										<option value="<?php echo $rst->uid;?>"><?php echo $rst->company_name;?> (<?php echo $rst->uid;?>)</option>
										<?php } ?>
										</select>
									</div>
								</div>
								<?php 
									$result = $db->queryUniqueValue("SELECT uid FROM apps_user WHERE uid='".$_SESSION['distributor_uid']."' AND status='1' AND is_fos='1'");
									
									if($result==""||$result==null)
									{

									}
									else
									{
								 ?>
								 
								<div class="form-group">
									<label class="col-sm-4 control-label">Account Manager:</label>
									<div class="col-sm-8 jrequired">
										<select name="manager_id" id="manager_id" class="form-control"></select>
									</div>
								</div>
							<?php }?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Balance :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" name="balance" id="balance" class="form-control">
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
							    <?php if($_SESSION['distributor_uid']=='20032368')
								{?>
									<button type="submit" disabled name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>

								<?php } else {
								?>
									<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
									
								<?php }?>
							
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-inr"></i> Current Balance</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">	
								<span class="text-ruppee"><?php echo round($current_balance, 2);?></span>
								<p class="text-inr"> Cutoff Amount:- <strong><?php echo round($cutoff_balance, 2);?></strong> Rs</p>
							</div>
						</div>
					</div>
				</div>
				
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Notepad</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<textarea name="note" id="note" rows="12" cols="15" class="form-control"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>