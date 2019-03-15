<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0 ;
$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['admin']."' ");

$admin = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet");
if($admin) {
	$current_balance = $admin->balance;
} else {
	$current_balance = "0";
}
$countOffline = $db->countOf("apps_recharge", "api_id = '11' AND ( status = '1' OR status = '7' OR status = '8')");
$countFund = $db->countOf("fund_requests", "user_type = '3' AND status = '0' ");
$meta['title'] = "Dashboard";
include("header.php");
?>
<div class="content">
	<div class="container-fluid">		
		<?php if($countFund > 0) { ?>
		<div class="alert alert-red">
			You have <span class="badge"><?php echo $countFund;?></span> fund request is pending, to view click to button <a href="fund-request.php" class="btn btn-sm btn-info">List Fund Request</a>
		</div>
		<?php } ?>
		<?php if($countOffline > 0) { ?>
		<div class="alert alert-red">
			You have <span class="badge"><?php echo $countOffline;?></span> offline transactions are pending, to view click to button <a href="offline-payment.php" class="btn btn-sm btn-info">List Offline</a>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-8">
				<div class="row">
					<div class="col-sm-3">
						<div class="small-box">
							<a href="live-recharge.php" class="small-block">
								<i class="fa fa-refresh"></i>								
								<p>Live Recharge</p>
							</a>
						</div>
					</div>			
					<div class="col-sm-3">
						<div class="small-box">
							<a href="offline-payment.php" class="small-block">
								<i class="fa fa-shield"></i>
								
								<p>Offline (<b><?php echo $countOffline;?></b>)</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="fund-request.php" class="small-block">
								<i class="fa fa-inbox"></i>
								<p>Fund Reqs (<b><?php echo $countFund;?></b>)</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="complaints.php" class="small-block">
								<i class="fa fa-mobile"></i>
								<p>Complaints</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="tickets.php" class="small-block">
								<i class="fa fa-support"></i>
								<p>Support</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="all-users.php" class="small-block">
								<i class="fa fa-user"></i>
								<p>Users</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="operator.php" class="small-block">
								<i class="fa fa-wifi"></i>
								<p>Operator</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="notification.php" class="small-block">
								<i class="fa fa-bell"></i>
								<p>Notifications</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="admin-zone.php" class="small-block">
								<i class="fa fa-lock"></i>
								<p>Admin Zone</p>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="small-box">
					<a class="small-block">
						<span style="font-size:42px; line-height:72px; color:#819bd5;"><?php echo round($current_balance, 2);?> Rs</span>
						<p>Current Balance</p>
					</a>
				</div>
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Online Users</h3>				
					</div>
					<div class="box-body max-height-300 overflow-auto">
						<?php
						$query = $db->query("SELECT * FROM activity_login WHERE user_type != '0' AND (login_time > DATE_SUB(NOW(), INTERVAL 2 HOUR)) GROUP BY username ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No user online</td></tr>";
						while($result = $db->fetchNextObject($query)) {					
							$user = $db->queryUniqueObject("SELECT uid,username,company_name FROM apps_user WHERE username = '".$result->username."' ");
						?>
						<p> <i class="fa fa-circle text-green"></i> &nbsp; <?php echo $user->company_name;?> (<?php echo getUserType($result->user_type);?>)</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="panel padding-left-10 padding-top-10 padding-bottom-10">
		Last Login IP: <?php echo $admin_info->last_login_ip;?> at  <?php echo date('r', strtotime($admin_info->last_login_time));?>
	</div>
</div>
<?php include("footer.php");?>