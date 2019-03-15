<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$aFrom = date("Y-m-d 00:00:00");
$aTo = date("Y-m-d 23:59:59");

$sumCredit = $db->queryUniqueObject("SELECT COALESCE(SUM(amount),0) AS totalCredit FROM trans_deduct WHERE type = 'cr' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
$sumDebit = $db->queryUniqueObject("SELECT COALESCE(SUM(amount),0) AS totalDebit FROM trans_deduct WHERE type = 'dr' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");

$sumRecharge = $db->queryUniqueObject("SELECT log.*, rch.amount, COALESCE(SUM(rch.amount),0) AS totalRecharge FROM recharge_change_log log LEFT JOIN apps_recharge rch ON log.recharge_id = rch.recharge_id WHERE log.type = '0' AND log.updated_date BETWEEN '".$aFrom."' AND '".$aTo."' ");

$sumRevert = $db->queryUniqueObject("SELECT log.*, rch.amount, COALESCE(SUM(rch.amount),0) AS totalRevert FROM recharge_change_log log LEFT JOIN apps_recharge rch ON log.recharge_id = rch.recharge_id WHERE log.type = '1' AND log.updated_date BETWEEN '".$aFrom."' AND '".$aTo."' ");

$meta['title'] = "Admin Zone";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Admin Zone</div>
		</div>
		<div class="row min-height-480">
			<div class="col-sm-4">
				<ul class="list-group">
					<li class="list-group-item">Today's Recharge</li>
					<li class="list-group-item">Success Recharge: <b><?php echo $sumRecharge->totalRecharge;?></b></li>
					<li class="list-group-item">Revert Recharge: <b><?php echo $sumRevert->totalRevert;?></b></li>
				</ul>
			</div>
			<div class="col-sm-4">
				<ul class="list-group">
					<li class="list-group-item">Today's Transactions</li>
					<li class="list-group-item">Credit Amount: <b><?php echo $sumCredit->totalCredit;?></b></li>
					<li class="list-group-item">Debit Amount: <b><?php echo $sumDebit->totalDebit;?></b></li>
				</ul>
			</div>
			<div class="col-sm-4">
				<ul class="list-group">
					<li class="list-group-item">Report's</li>
					<li class="list-group-item"><a href="rpt-zone-setsuccess-recharge.php">Recharge Report</a></li>
					<li class="list-group-item"><a href="rpt-zone-fund-deduct.php">Fund Deduct Report</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
