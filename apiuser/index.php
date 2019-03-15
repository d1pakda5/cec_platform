<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:../login.php");
	exit();
}
if($_SESSION['api_kyc'] =='0') {
	header("location:kyc-login.php?token=".$_SESSION['token']);
	exit();
}
include("../config.php");
include("common.php");
$error = 0 ;
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$_SESSION['apiuser_uid']."'");
if($wallet) {
	$current_balance = $wallet->balance;
	$cutoff_balance = $wallet->cuttoff;
} else {
	$current_balance = "0";
	$cutoff_balance = "0";
}
$popup = $db->queryUniqueObject("SELECT * FROM notifications WHERE ntype='p' AND status='1' AND (user_type='1' OR user_type='0')  AND notification_date_to >= CURDATE() ORDER BY notification_date DESC");
$meta['title'] = "Dashboard";
include("header.php");
?>
<script>
$(document).ready(function () {
	$("#modalPopUp").modal('show');
});
</script>
<?php if($aAPIUser->is_kyc=='0') { ?>
<script>
$(document).ready(function () {
	$("#modalKYC").modal('hide');
});
</script>
<?php } ?>
<style>
    
</style>
<div class="content">
	<div class="container">
		<?php
		$qry = $db->query("SELECT * FROM notifications WHERE ntype='s' AND status='1' AND (user_type='1' OR user_type='0') AND notification_date_to >= CURDATE() ORDER BY notification_date DESC");
		if($db->numRows($qry) > 0) { ?>
		<div class="">	
			<div class="">
				<div class="col-xs-11 pull-right">		
					<marquee style="margin-top: -10px;margin-bottom: 0px;" scrollamount="3" direction="scroll" onmouseover="this.setAttribute('scrollamount', 0, 0);" onmouseout="this.setAttribute('scrollamount', 3, 0);">
						<?php
						while($result = $db->fetchNextObject($qry)) { ?>
							<span class="text-alert" style="margin-right:40px;"><?php echo $result->notification_content;?></span>
						<?php } ?>
					</marquee>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="alert alert-default">
					<i class="fa fa-x fa-flag"></i> Welcome to API Panel
				</div>
			</div>
		</div>
		<div class="row min-height-480">
			<div class="col-sm-8">
				<div class="row">	
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="fund-request.php?token=<?php echo $token;?>" class="sb-ico-block"><i class="fa fa-x fa-money"></i></a>
							</div>
							<div class="sb-footer ft-blue">Fund Request</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="complaints.php" class="sb-ico-block"><i class="fa fa-x fa-send-o"></i></a>
							</div>
							<div class="sb-footer ft-blue">Complaints</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="tickets.php" class="sb-ico-block"><i class="fa fa-x fa-envelope-o"></i></a>
							</div>
							<div class="sb-footer ft-blue">Support Ticket</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="operator.php" class="sb-ico-block"><i class="fa fa-x fa-wifi"></i></a>
							</div>
							<div class="sb-footer ft-blue">Operator</div>
						</div>
					</div>			
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="rpt-recharge.php" class="sb-ico-block"><i class="fa fa-x fa-file-text-o"></i></a>
							</div>
							<div class="sb-footer ft-blue">Recharge Histroy</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="rpt-transactions.php" class="sb-ico-block"><i class="fa fa-x fa-file-text-o"></i></a>
							</div>
							<div class="sb-footer ft-blue">Transaction Histroy</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="rpt-commission.php" class="sb-ico-block"><i class="fa fa-x fa-file-text-o"></i></a>
							</div>
							<div class="sb-footer ft-blue">Commission Histroy</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="small-box">
					<div class="sb-body">
						<span class="text-ruppee"><?php echo round($current_balance, 2);?></span>
					</div>
					<div class="sb-footer ft-green">Current Balance (<i class="fa fa-inr"></i>)</div>
				</div>
				<div class="small-box">
					<div class="sb-body">
						<span class="text-ruppee"><?php echo round($cutoff_balance, 2);?></span>
					</div>
					<div class="sb-footer ft-green">Cutoff Amount (<i class="fa fa-inr"></i>)</div>
				</div>
				<div class="small-box">
					<div class="sb-body">
						<p class="text-email"><i class="fa fa-envelope-o"></i> <a href="mailto:<?php echo $website['email'];?>"><?php echo $website['email'];?></a></p>
						<p class="text-support"><i class="fa fa-mobile"></i> <?php echo $website['phone'];?></p>
					</div>
					<div class="sb-footer ft-green">For Support Contact</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if($popup && $popup->ntype=='p') { ?>
<!-- Modal Popup Notification -->
<div class="modal fade" id="modalPopUp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" style="margin:0px auto;width: 720px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="myModalLabel"><i class="fa fa-reorder"></i></h5>
			</div>
			<div class="modal-body">
				<?php if($popup->ctype=='i') { ?>
					<img src="../uploads/<?php echo $popup->notification_files;?>" class="img-responsive" />
				<?php } else { ?>
					<p><?php echo $popup->notification_content;?></p>
				<?php }?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<!-- Modal Popup Notification -->
<div class="modal fade" id="modalKYC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="myModalLabel"><i class="fa fa-reorder"></i></h5>
			</div>
			<div class="modal-body" style="font-size:16px;">
				<p>Please update you <font color="#FF0000">KYC</font> (Know your customer form) and kindly update scan copy of <font color="#FF0000">PAN Number, Aadhar Number, Address proof</font>. To update details please go to KYC page.</p>
				<p>Also update your <font color="#FF0000">GSTIN</font> (Goods and services Tax Identification Number) if your turnover is in GST Slab.</p>
				<p>My Account - > <b><a href="kyc.php?token=<?php echo $token;?>">KYC</a></b></p>
				<p>Please update all the details in 7 Days after your service has been deactivated.</p>
				<p>Thanks for you co-operation.</p>
				<p>Team, Support</p>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php");?>