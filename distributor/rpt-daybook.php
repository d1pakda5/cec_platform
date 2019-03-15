<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$uid = $_SESSION['distributor_uid'];
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."'");
if($user) {
	$__name = $user->company_name;
	$__uid = $user->uid;
} else {
	$__name = "";
	$__uid = "0";
}
$meta['title'] = "User Summary Reports";

include('header.php');
?>
<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Day Summary Report</div>
			
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i>Day Book</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="row">
					
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
						</div>
					</form>
				</div>
				<?php
				//SUM(CASE WHEN type='cr' AND transaction_term='FUND' THEN amount ELSE 0 END) as balanceAdded 
				$qry = $db->query("
					SELECT 
						SUM(IF(type='dr', amount, 0)) AS debitAmount,
						SUM(IF(type='dr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeDebit, 
						SUM(IF(type='dr' AND transaction_term='FAILURE', amount, 0)) AS failureDebit, 
						SUM(IF(type='dr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceDeduct,
						SUM(IF(type='dr' AND transaction_term='REVERT', amount, 0)) AS revertDebit, 
						SUM(IF(type='dr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundDebit,
						SUM(IF(type='cr', amount, 0)) AS creditAmount,
						SUM(IF(type='cr' AND transaction_term='RECHARGE', amount, 0)) AS rechargeCredit,
						SUM(IF(type='cr' AND transaction_term='FAILURE', amount, 0)) AS failureCredit, 
						SUM(IF(type='cr' AND (transaction_term='FUND' OR transaction_term='DEDUCT FUND' OR transaction_term='ADD FUND'), amount, 0)) AS balanceCredit,
						SUM(IF(type='cr' AND transaction_term='REVERT', amount, 0)) AS revertCredit, 
						SUM(IF(type='cr' AND (transaction_term='REFUND' OR transaction_term='REFUNDED'), amount, 0)) AS refundCredit					
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row = $db->fetchNextObject($qry);
				$qry1 = $db->query("
					SELECT 
						SUM(amount) AS commissionTotal	
					FROM commission_details WHERE uid='".$uid."' AND added_date BETWEEN '".$aFrom."' AND '".$aTo."' ");
				$row1 = $db->fetchNextObject($qry1);
			
				
				$open_bal = "0";
				$opb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date < '".$aFrom."' ORDER BY transaction_id DESC");
				if($opb) {
					$open_bal = $opb->closing_balance;
				} else {
					$opb1 = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ORDER BY transaction_id ASC");
					if($opb1) {
						$open_bal = $opb1->closing_balance;
					}
				}
				
				$clb = $db->queryUniqueObject("
					SELECT 
						closing_balance	
					FROM transactions WHERE account_id='".$uid."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ORDER BY transaction_id DESC");
				
				?>
				<table class="table table-bordered table-basic">
					<thead>
						<tr>
							<th >UID</th>
							<th>Opening Balance</th>
							<th>Purchase</th>
							<th>Sale</th>
							<th>Commission</th>
							<th>Closing Balance</th>
						</tr>
					</thead>
					<tbody>
						
						<tr>
							<td><?php echo $uid;?></td>
							<td><?php echo round($open_bal,2);?></td>
							<td><?php echo round($row->balanceCredit,2)?></td>
							<td><?php echo round($row->balanceDeduct,2);?></td>
							<td><?php echo round($row1->commissionTotal,2);?></td>
							<td><?php echo round($clb->closing_balance,2);?></td>
							
						</tr>
						
					</tbody>
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php');?>