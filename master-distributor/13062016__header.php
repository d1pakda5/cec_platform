<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $meta['title'];?></title>
<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/font-awesome.min.css">
<link rel="stylesheet" href="../css/theme.css" type="text/css" />
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</head>
<body class="bg-body">
<div class="header">
	<div class="container">
		<div class="logo pull-left">
			<img src="../uploads/<?php echo $website['logo'];?>" />
		</div>		
		<ul class="nav navbar-nav pull-right">
			<p class="navbar-text"><?php echo $aMaster->company_name;?> [ <?php echo $_SESSION['mdistributor_uid'];?> ]</p>
			<li class="dropdown pull-right">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Account <span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="profile.php?token=<?php echo $token;?>">Profile</a></li>
					<li><a href="change-password.php?token=<?php echo $token;?>">Change Password</a></li>
					<li><a href="change-pin.php?token=<?php echo $token;?>">Reset Pin</a></li>
					<li class="divider"></li>
					<li><a href="my-commission.php?token=<?php echo $token;?>">My Commission</a></li>
					<li class="divider"></li>
					<li><a href="fund-request.php?token=<?php echo $token;?>">My Fund Request</a></li>
					<li><a href="mobile-change-request.php?token=<?php echo $token;?>">Mobile Change</a></li>
					<li class="divider"></li>
					<li><a href="logout.php">Logout <i class="fa fa-sign-out"></i></a></li>
				</ul>
			</li>
		</ul>
	</div>
</div>
<div class="menu">
	<div class="container">
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
							<li><a href="distributor.php">Distributor</a></li>
							<li><a href="retailer.php">Retailer</a></li>
						</ul>						
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Fund <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="fund-add.php?token=<?php echo $token;?>">Fund Add</a></li>
							<li><a href="user-fund-request.php">User Fund Request</a></li>
						</ul>						
					</li>
					<li><a href="complaints.php">Complaints</a></li>
					<li><a href="tickets.php"><i></i> Support</a></li>							
					<li><a href="operator.php">Operators</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="rpt-recharge.php">Recharge Report</a></li>
							<li><a href="rpt-transactions.php">Transaction Report</a></li>
							<li><a href="rpt-commission.php">Commission Report</a></li>
							<li><a href="rpt-recharge-status.php">Recharge Status</a></li>
							<li><a href="rpt-login-activity.php">Login Activity Report</a></li>
						</ul>
					</li>
				</ul>				
			</div>
		</div>
	</div>
</div>