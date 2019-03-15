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
<style>
.navbar,
.nav,
.navbar-collapse {
	background:#0066FF;
	border:0px;
}
.nav.navbar-nav > li > a {
	color:#fff;
}
.nav.navbar-nav > li > a:hover {
	color:#555;
}
.navbar-nav > .open > a,
.navbar-nav > .open > a:hover,
.navbar-nav > .open > a:focus {
  color: #555;
  background-color: #e7e7e7;
}
</style>
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
				<li><a href="distributor.php"> Distributor</a></li>
				<li><a href="retailer.php"> Retailer</a></li>
				<li><a href="direct-retailer.php"> Direct Retailer</a></li>
				<li><a href="fund-request.php">Fund Request</a></li>
				<li><a href="operator.php">Operators</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="rpt-recharge.php">Recharge Report</a></li>
						<li><a href="rpt-transactions.php">Transaction Report</a></li>
						<li><a href="rpt-fund-transfer.php">Fund Transfer</a></li>
						<li><a href="rpt-status.php">Recharge Status</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav pull-right">
				<li><a href="logout.php">Logout <i class="fa fa-sign-out"></i></a></li>
			</ul>
		</div>
	</div>
</div>
