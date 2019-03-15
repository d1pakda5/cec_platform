<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
$error = 0 ;
$aParent = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$aRetailer->dist_id."' ");
if($aParent) {
	$parent_user_mobile = $aParent->mobile;
	if($aParent->email != '') { $parent_user_email = $aParent->email; } else { $parent_user_email = '';}
}
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE uid = '".$_SESSION['retailer_uid']."'");
if($wallet) {
	$current_balance = $wallet->balance;
} else {
	$current_balance = "0";
}
$meta['title'] = "Dashboard";
include("header.php");
?>
<div class="content">
	<div class="container">
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
<?php include("footer.php");?>