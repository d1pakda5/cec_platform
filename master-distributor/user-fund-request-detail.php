<?php
session_start();
if(!isset($_SESSION['mdistributor'])) header("location:login.php");
include('../config.php');
include('common.php');
if(!isset($_GET['token']) || $_GET['token'] != $token) { exit("Token not match"); }
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['rqstamt'] == '' || $_POST['addonamt'] == '' || $_POST['totalamt'] == '' || $_POST['remark'] == '') {
		$error = 1;		
	} else {		
		if($_POST['action'] == '1') {			
			$post_request_id = htmlentities(addslashes($_POST['request_id']),ENT_QUOTES);		
			$request_info = $db->queryUniqueObject("SELECT * FROM fund_requests WHERE request_id = '".$post_request_id."' AND request_to = '".$_SESSION['mdistributor_uid']."' ");
			if($request_info) {
				if($request_info->status != '0') {
					$error = 2;
				}  else {
					$totalamt = htmlentities(addslashes($_POST['totalamt']),ENT_QUOTES);
					$remark = "FUND | ID - $request_id | $request_info->amount | $totalamt";
					/*
					* Start Transaction
					*/		
					$db->query("START TRANSACTION");				
					$md_wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$_SESSION['mdistributor_uid']."' ");
					if($md_wallet && (($md_wallet->balance - $md_wallet->cuttoff) >= $totalamt) {
						/*
						* Debit Transaction
						*/
						$md_closing_balance = $md_wallet->balance - $totalamt;
						$db->query("UPDATE apps_wallet SET balance='".$md_closing_balance."' WHERE wallet_id = '".$md_wallet->wallet_id."' ");
						$ts1 = mysql_affected_rows();
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$_SESSION['mdistributor_uid']."', '".$request_info->request_user."', 'dr', '".$totalamt."', '".$md_closing_balance."', 'FUND', '', '".$remark."', '3', '".$_SESSION['mdistributor_uid']."') ");
						
						/*
						* Credit Transaction
						*/
						$wallet = $db->queryUniqueObject("SELECT wallet_id,uid,balance FROM apps_wallet WHERE uid = '".$uid."' ");				
						$closing_balance = $wallet->balance + $amount;				
						$db->query("UPDATE apps_wallet SET balance = '".$closing_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
						$ts2 = mysql_affected_rows();
						$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$wallet->uid."', '".$_SESSION['mdistributor_uid']."', 'cr', '".$totalamt."', '".$closing_balance."', 'FUND', '', '".$remark."', '3', '".$_SESSION['mdistributor_uid']."') ");
						$updated_ref_id = $db->lastInsertedId();
						
						if($ts1 && $ts2) {
							$commit = $db->query("COMMIT");
							if($commit) {
								$db->execute("UPDATE `fund_requests` SET `updated_date` = NOW(), `updated_ref_id` = '".$updated_ref_id."', `updated_by` = '".$_SESSION['mdistributor_uid']."', status = '".$_POST['action']."' WHERE request_id = '".$post_request_id."' ");
								$to_user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$request_info->request_user."' ");
								$message = smsFundTransfer($totalamt, $aMaster->company_name, $to_user->company_name);
								smsSendSingle($to_user->mobile, $message, 'fund_transfer');
								$error = 3;
								header("location:user-fund-request-detail.php?token=".$token."&id=".$post_request_id."&error=3");
							} else {
								$error = 4;
							}
						} else {
							$error = 4;
						}
					} else {
						$error = 7;
					}
				}
			} else {
				$error = 1;
			}
		} else if($_POST['action'] == '2') {
			$db->execute("UPDATE `fund_requests` SET `updated_date` = NOW(), `updated_by` = '".$_SESSION['mdistributor_uid']."', status = '".$_POST['action']."' WHERE request_id = '".$post_request_id."' ");
			$error = 5;
		} else {
			$error = 6;
		}	
	}
}

$request = $db->queryUniqueObject("SELECT * FROM fund_requests WHERE request_id = '".$request_id."' AND request_to = '".$_SESSION['mdistributor_uid']."' ");
if(!$request) header("location:user-fund-request.php");
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$request->request_user."' ");
$meta['title'] = "Fund Request";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	jQuery("#addonamt").keyup(function(){
		var a = parseFloat(jQuery(this).val());		
		var b = parseFloat(jQuery("#rqstamt").val());
		var s = a+b;
		jQuery("#totalamt").val(s);
	});
	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to add fund?")) {
        form.submit();
      }
		},
	  rules: {
	  	rqstamt: {
				required: true
			},
			addonamt: {
				required:true
			},
			totalamt: {
				required: true
			},
			remark: {
				required: true
			},
			action: {
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
			<div class="page-title">Fund Request <small>/ Add</small></div>
			<div class="pull-right">
				<a href="fund-request.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 7) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Insufficent Fund in account.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 6) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Please select a valid action to do transaction.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 5) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Fund request has been rejected successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 4) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Transaction has been rollback due to internal error.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Transaction has been already processed!
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
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Fund Request</h3>
					</div>
					<form action="" method="post" id="fundForm" class="form-horizontal">
					<input type="hidden" name="request_id" id="request_id" value="<?php echo $request->request_id;?>">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-6">								
								<div class="form-group">
									<label class="col-sm-5 control-label">Users :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $user->company_name;?> <?php echo $user->uid;?></p>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-5 control-label"> Transaction Ref No :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $request->transaction_ref_no;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Payment Mode :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo getPaymentMode(getPaymentModeList(), $request->pay_mode);?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Payment Date :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo date("d-m-Y", strtotime($request->payment_date));?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> In Bank :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $request->to_bank_account;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> From Bank Name :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $request->your_bank_name;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> From Bank Account :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $request->your_bank_account;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> From Bank Account :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static"><?php echo $request->your_bank_account;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Attachment :</label>
									<div class="col-sm-7 jrequired">
										<p class="form-control-static">
											<?php if($request->file_attachment != '') {?>
												<a href="../uploads/<?php echo $request->file_attachment;?>" target="_blank">View File</a>
											<?php } ?>
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-6">								
								<div class="form-group">
									<label class="col-sm-5 control-label"> Amount :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="rqstamt" id="rqstamt" readonly="" value="<?php echo $request->amount;?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Addon Amount :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="addonamt" id="addonamt" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Total Amount :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="totalamt" id="totalamt" readonly="" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label"> Remark :</label>
									<div class="col-sm-7 jrequired">
										<textarea name="remark" id="remark" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">Action :</label>
									<div class="col-sm-7 jrequired">
										<select name="action" id="action" class="form-control">
											<option value=""></option>
											<?php $array['pay_status'] = getPaymentStatusList();
											foreach($array['pay_status'] as $data) { ?>
											<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
											<?php }	?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>