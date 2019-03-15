<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0 ;
$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['accmgr']."' ");

$countFund = $db->countOf("fund_requests", "user_type = '3' AND status = '0' ");
$countKyc = $db->countOf("userskyc", "status='0' ");
$meta['title'] = "Dashboard";
include("header.php");
?>
<div class="content" style="min-height:500px;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="small-box">
					<a href="distributor.php" class="small-block">
						<i class="fa fa-user"></i>								
						<p>Distributor</p>
					</a>
				</div>
			</div>			
			<div class="col-sm-2">
				<div class="small-box">
					<a href="retailer.php" class="small-block">
						<i class="fa fa-user"></i>
						<p>Retailer</p>
					</a>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="small-box">
					<a href="direct-retailer.php" class="small-block">
						<i class="fa fa-user"></i>
						<p>Direct Retailer</p>
					</a>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="small-box">
					<a href="fund-request.php" class="small-block">
						<i class="fa fa-inbox"></i>
						<p>Fund Request</p>
					</a>
				</div>
			</div>
			<div class="col-sm-2">
				<div class="small-box">
					<a href="operator.php" class="small-block">
						<i class="fa fa-signal"></i>
						<p>Operator</p>
					</a>
				</div>
			</div>
				<div class="col-sm-2">
				<div class="small-box">
					<a href="set_target.php" class="small-block">
						<i class="fa fa-bullseye"></i>
						<p>Monthly Sale Target</p>
					</a>
				</div>
			</div>
			<div class="col-sm-2">
						<div class="small-box">
							<a href="sale_rpt_form.php" class="small-block">
								<i class="fa fa-book"></i>
								<p>Monthly Sale Report</p>
							</a>
						</div>
					</div>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="panel padding-left-10 padding-top-10 padding-bottom-10">
		Welcome <?php echo $admin_info->username;?>, Last Login IP: <?php echo $admin_info->last_login_ip;?> at  <?php echo date('r', strtotime($admin_info->last_login_time));?>
	</div>
</div>
<?php include("footer.php");?>