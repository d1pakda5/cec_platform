<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:../login.php");
	exit();
}

if($_SESSION['retailer_kyc'] =='0') {
	header("location:kyc-login.php?token=".$_SESSION['token']);
	exit();
}

include("../config.php");
include("common.php");
$error = 0 ;

$aParent = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$aRetailer->dist_id."' ");
if($aParent) {
	$parent_user_mobile = $aParent->mobile;
	if($aParent->email!='') { $parent_user_email=$aParent->email; } else { $parent_user_email='';}
}

$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid='".$_SESSION['retailer_uid']."'");
if($wallet) {
	$current_balance = $wallet->balance;
} else {
	$current_balance = "0";
}
$popup = $db->queryUniqueObject("SELECT * FROM notifications WHERE ntype='p' AND status='1' AND (user_type='5' OR user_type='0') AND notification_date_to >= CURDATE()  ORDER BY notification_date DESC");

$meta['title'] = "Dashboard";
 include("header.php");
?>
<script>
$(document).ready(function () {
	$("#modalPopUp").modal('show');
});
</script>
<?php if($aRetailer->is_kyc=='0') { ?>
<script>
$(document).ready(function () {
	$("#modalKYC").modal('hide');
});
</script>
<?php } ?>
<div class="content">
	<div class="container">
		<?php
		$qry = $db->query("SELECT * FROM notifications WHERE ntype='s' AND status='1' AND (user_type='5' OR user_type='0') AND notification_date_to >= CURDATE() ORDER BY notification_date DESC");
		if($db->numRows($qry) > 0) { ?>
		<div class="alert">
			<div class="row">
				<div class="col-xs-12 pull-right">			
					<marquee scrollamount="3" direction="scroll" onmouseover="this.setAttribute('scrollamount', 0, 0);" onmouseout="this.setAttribute('scrollamount', 3, 0);">
						<?php
						while($result = $db->fetchNextObject($qry)) { ?>
							<span class="text-alert" style="margin-right:40px;"><i class=""></i> <?php echo str_replace(array("<br>","<br/>"), "", $result->notification_content);?></span>
						<?php } ?>
					</marquee>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="row min-height-480">
			<div class="col-sm-8">
				<div class="row">	
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="recharge.php" class="sb-ico-block"><i class="fa fa-x fa-mobile"></i></a>
							</div>
							<div class="sb-footer ft-blue">Recharge</div>
						</div>
					</div>		
					<div class="col-sm-3">
						<div class="small-box">
							<div class="sb-body">
								<a href="money-transfer.php" class="sb-ico-block"><i class="fa fa-x fa-inr"></i></a>
							</div>
							<div class="sb-footer ft-blue">Money Transfer</div>
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
								<a href="fund-request.php?token=<?php echo $token;?>" class="sb-ico-block"><i class="fa fa-x fa-money"></i></a>
							</div>
							<div class="sb-footer ft-blue">Fund Request</div>
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
						<p class="text-email"><i class="fa fa-envelope-o"></i> <a href="mailto:<?php echo $website['email'];?>"><?php echo $website['email'];?></a></p>
						<p class="text-support"><i class="fa fa-mobile"></i> <?php echo $website['phone'];?></p>
					</div>
					<div class="sb-footer ft-green">For Support Contact</div>
				</div>
				<div class="small-box">
					<div class="sb-body">
						<span class="text-default"><?php echo $aParent->company_name;?>
						<br /><?php echo $aParent->address;?>, <?php echo $aParent->city;?>, <?php echo $aParent->states;?></span>						
					</div>
					<div class="sb-footer ft-green">Your Distributor</div>
				</div>				
				<div class="small-box">
					<div class="sb-body">
						<span class="text-default">						
						<br /><i class="fa fa-phone"></i> <?php echo $aParent->mobile;?>
						<br /><i class="fa fa-envelope-o"></i> <?php echo $aParent->email;?></span>						
					</div>
					<div class="sb-footer ft-green">Distributor Contact Details</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if($popup && $popup->ntype=='p') { ?>
<!-- Modal Popup Notification -->
<div class="modal fade" id="modalPopUp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
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
				<p>Please update you <font color="#FF0000">KYC</font> (Know your customer from) and kindly update scap copy of <font color="#FF0000">PAN Number, Aadhar Number, Address proof</font>. To update details please go to KYC page.</p>
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