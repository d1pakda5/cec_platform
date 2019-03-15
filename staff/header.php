<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $meta['title'];?></title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/font-awesome.min.css">
<link rel="stylesheet" href="../css/stylesheet.css" type="text/css" />
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</head>
<body class="bg-body">
<div class="menu">
	<div class="navbar" role="navigation">
		<div class="navbar-header">
			<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#dr-menu">
				<span class="sr-only">Toggle navigation</span>
				<span class="fa fa-bars"></span>
			</button>						
		</div>
		
		<div id="dr-menu" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="index.php">&nbsp;<i class="fa fa-lg fa-home"></i>&nbsp;</a></li>
				<?php if(!empty($sP['api_user']) || !empty($sP['md_user']) || !empty($sP['ds_user']) || !empty($sP['rt_user'])) { ?>				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php if(!empty($sP['api_user'])) { ?>
						<li><a href="api-user.php">API User</a></li>
						<?php } ?>
						<?php if(!empty($sP['md_user'])) { ?>
						<li><a href="master-distributor.php">Master Distributor</a></li>
						<?php } ?>
						<?php if(!empty($sP['ds_user'])) { ?>
						<li><a href="distributor.php">Distributor</a></li>
						<?php } ?>
						<?php if(!empty($sP['rt_user'])) { ?>
						<li><a href="retailer.php">Retailer</a></li>
						<?php } ?>
						<?php if(!empty($sP['rt_user'])) { ?>
						<li><a href="direct-retailer.php">Direct Retailer</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<?php if(!empty($sP['fund'])) { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Funds <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="fund-add.php">Add/Deduct Fund</a></li>
						<li><a href="fund-request.php">Fund Request</a></li>
						<li><a href="set_target.php">Monthly Sale Target</a></li>
						<li><a href="sale_entry_test.php">Sale Entry</a></li>

					</ul>
				</li>
				<?php } ?>
				<li><a href="complaints.php">Complaints</a></li>
				<li><a href="tickets.php"><i></i> Support</a></li>	
				<?php if(!empty($sP['operator'])) { ?>							
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Operators <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php if(!empty($sP['operator']['opr'])) { ?>
						<li><a href="operator.php">Operators</a></li>
						<?php } ?>
						<?php if(!empty($sP['operator']['service'])) { ?>
						<li><a href="service.php">Services</a></li>
						<?php } ?>
						<?php if(!empty($sP['operator']['api'])) { ?>
						<li><a href="api.php">API's</a></li>
						<?php } ?>
						<?php if(!empty($sP['operator']['denom'])) { ?>
						<li><a href="denomination.php">Denomination's</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<?php if((!empty($sP['is_notification']) && $sP['is_notification']=='y') || (!empty($sP['is_mobile']) && $sP['is_mobile']=='y') || !empty($sP['sms']) || !empty($sP['kyc'])) { ?>	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Utilities <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php if(!empty($sP['is_notification']) && $sP['is_notification'] == 'y') {?>
						<li><a href="notification.php">Notification</a></li>
						<?php } ?>
						<?php if(!empty($sP['is_mobile']) && $sP['is_mobile'] == 'y') {?>
						<li><a href="mobile-change-request.php">Mobile Change</a></li>
						<?php } ?>
						<?php if(!empty($sP['sms'])) {?>
							<?php if(!empty($sP['sms']['send']) && $sP['sms']['send']=='y') {?>
							<li><a href="send-sms.php">Send SMS</a></li>
							<?php } ?>
						<?php } ?>
						<?php if(!empty($sP['sms'])) { ?>
							<?php if(!empty($sP['sms']['setting']) && $sP['sms']['setting']=='y') {?>
							<li><a href="sms-api.php">SMS API</a></li>
							<li><a href="sms-settings.php">SMS Settings</a></li>
							<?php } ?>
						<?php } ?>
						<li><a href="admin-user.php">Account Manager</a></li>
						<li class="divider"></li>
						<?php if(!empty($sP['kyc'])) { ?>
							<li><a href="rpt-kyc.php">KYC Verification</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<?php if(!empty($sP['reports'])) { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php if(!empty($sP['reports']['recharge']) && $sP['reports']['recharge']=='y') {?>
						<li><a href="rpt-recharge.php">Recharge Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['offline']) && $sP['reports']['offline']=='y') {?>
						<li><a href="rpt-offline.php">Offline Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['transaction']) && $sP['reports']['transaction']=='y') {?>
						<li><a href="rpt-transactions.php">Transaction Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['usertransaction']) && $sP['reports']['usertransaction']=='y') {?>
						<li><a href="rpt-user-transactions.php">User Transaction Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['rechargestatus']) && $sP['reports']['rechargestatus']=='y') {?>
						<li><a href="rpt-status.php">Recharge Status</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['transactionstatus']) && $sP['reports']['transactionstatus']=='y') {?>
						<li><a href="rpt-status-transaction.php">Transaction Status</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['longcode']) && $sP['reports']['longcode']=='y') {?>
						<li><a href="rpt-long-code.php">Long Code Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['sentsms']) && $sP['reports']['sentsms']=='y') {?>
						<li><a href="rpt-sent-sms.php">Sent SMS Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['login']) && $sP['reports']['login']=='y') {?>
						<li><a href="rpt-login-activity.php">Login Activity Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['apiresponse']) && $sP['reports']['apiresponse']=='y') {?>
						<li><a href="rpt-api-response.php">API Response Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['apicallback']) && $sP['reports']['apicallback']=='y') {?>
						<li><a href="rpt-api-callback.php">API Callback Report</a></li>
						<?php } ?>
						<?php if(!empty($sP['reports']['usercallback']) && $sP['reports']['usercallback']=='y') {?>
						<li><a href="rpt-user-callback.php">User Callback Report</a></li>
						<?php } ?>
						<li><a href="rpt-kyc.php">KYC Request</a></li>
					</ul>
				</li>
				<?php } ?>
				<?php if(!empty($sP['assign_manager'])) { ?>
					<li><a href="assign-manager.php">Assign Manager</a></li>
					<?php }?>
			</ul>
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown pull-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Profile <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="profile.php">Profile</a></li>
						<li><a href="change-password.php">Change Password</a></li>
						<li><a href="change-pin.php">Reset Pin</a></li>
						<li class="divider"></li>
						<li><a href="logout.php">Logout <i class="fa fa-sign-out"></i></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>



