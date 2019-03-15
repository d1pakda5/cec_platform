<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="api-user.php">API User</a></li>
						<li><a href="master-distributor.php">Master Distributor</a></li>
						<li><a href="distributor.php">Distributor</a></li>
						<li><a href="retailer.php">Retailer</a></li>
						<li><a href="direct_retailer.php">Direct Retailer</a></li>

						<li><a href="all-users.php">All User</a></li>
						<li class="divider"></li>
						<li><a href="rpt-kyc.php">KYC Verification</a></li>
						<li><a href="kyc-list.php">KYC's</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Funds <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="fund-add.php">Add/Deduct Fund</a></li>
						<li><a href="fund-request.php">Fund Request</a></li>
						<li><a href="fund-deduct-x.php">Fund Deduct</a></li>
					</ul>
				</li>
				<li><a href="complaints.php">Complaints</a></li>
				<li><a href="tickets.php">Support</a></li>				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="live-recharge.php">Live Recharge</a></li>
						<li><a href="rpt-recharge.php">Recharge Report</a></li>
						<li><a href="rpt-offline.php">Offline Report</a></li>
						<li><a href="rpt-transactions.php">Transaction Report</a></li>
						<li><a href="rpt-user-transactions.php">User Transaction Report</a></li>
						<li><a href="rpt-user-summary.php">User Summary Report</a></li>
						<li><a href="rpt-status.php">Recharge Status</a></li>
						<li><a href="rpt-status-transaction.php">Transaction Status</a></li>
						<li><a href="rpt-gst-deduct.php">GST Deduct Report</a></li>
						<li><a href="rpt-long-code.php">Long Code Report</a></li>
						<li><a href="rpt-sent-sms.php">Sent SMS Report</a></li>
						<li><a href="rpt-login-activity.php">Login Activity Report</a></li>
						<li class="divider"></li>
						<li><a href="rpt-api-response.php">API Response Report</a></li>
						<li><a href="rpt-api-callback.php">API Callback Report</a></li>
						<li><a href="rpt-user-callback.php">User Callback Report</a></li>
					</ul>
				</li>					
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Settings <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="operator.php">Operators</a></li>
						<li><a href="service.php">Services</a></li>
						<li><a href="api.php">API's</a></li>
						<li><a href="denomination.php">Denomination's</a></li>
						<li><a href="api-balance.php">API Balance</a></li>
						<li class="divider"></li>
						<li><a href="products.php">Products</a></li>
						<li class="divider"></li>
						<li class="navbar-plain-text"><b>Money Transfer</b></li>
						<li class="divider"></li>
						<li><a href="dmt-setting.php">DMT Settings</a></li>
						<li class="divider"></li>
					</ul>
				</li>				
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Utilities <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="notification.php">Notification</a></li>
						<li><a href="mobile-change-request.php">Mobile Change</a></li>
						<li class="divider"></li>
						<li class="navbar-plain-text"><b>SMS Settings</b></li>
						<li><a href="send-sms.php">Send SMS</a></li>
						<li><a href="sms-api.php">SMS API</a></li>
						<li><a href="sms-settings.php">SMS Settings</a></li>
						<li><a href="sms-balance.php">SMS Balance</a></li>
						<li class="divider"></li>
						<li><a href="dmt-activation-request.php">DMT Activations</a></li>
						<li class="divider"></li>
						<li><a href="admin-user.php">Admin Users</a></li>
						<li><a href="assign-manager.php">Assign Manager</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">GST Txns <span class="caret"></span></a>
					<ul class="dropdown-menu">						
						<li><a href="rpt-retailer-gst-recharge-wise.php">Retailer (GST Recharge Wise)</a></li>
						<li><a href="rpt-distributor-gst-recharge-wise.php">Distributor (GST Recharge Wise)</a></li>
						<li><a href="rpt-apiuser-gst-recharge-wise.php">API User (GST Recharge Wise)</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown pull-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Profile <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="profile.php">Profile</a></li>
						<li><a href="change-password.php">Change Password</a></li>
						<li><a href="change-pin.php">Reset Pin</a></li>
						<li><a href="update-fund.php">Update Balance</a></li>
						<li class="divider"></li>
						<li><a href="logout.php">Logout <i class="fa fa-sign-out"></i></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>
