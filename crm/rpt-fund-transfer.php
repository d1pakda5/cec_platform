<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE trans.transaction_user_type='0' AND trans.account_id='0' AND user.assign_id='".$_SESSION['accmgr']."' AND trans.transaction_term='FUND' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND (trans.remark LIKE '%".mysql_real_escape_string($_GET['s'])."%')  ";
} else {
	$sWhere .= " AND trans.transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
	$sWhere .= " AND trans.to_account_id='".mysql_real_escape_string($_GET["uid"])."' ";
}
$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id=user.uid $sWhere ORDER BY trans.transaction_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-transactions.php');

$meta['title'] = "Transaction Reports";
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
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Transaction</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Transactions</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-3">
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
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="User UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
							<th>Date</th>
							<th>User</th>
							<th>Type</th>
							<th>Ref.</th>
							<th>Remark</th>
							<th>Debit</th>
							<th>Credit</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT trans.*, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->transaction_date));?></td>
							<td><?php if($row->company_name) { echo $row->company_name; } else { echo "Admin";}?></td>
							<td><?php echo $row->transaction_term;?></td>
							<td><?php echo $row->transaction_ref_no;?></td>
							<td><?php echo $row->remark;?></td>
							<?php if($row->type == 'dr') { ?>
							<td align="right"><?php echo round($row->amount,2);?></td>
							<td align="right"></td>
							<?php } else { ?>							
							<td align="right"></td>
							<td align="right"><?php echo round($row->amount,2);?></td>
							<?php } ?>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT trans.*, SUM(IF(trans.type = 'dr', trans.amount, 0)) AS debitAmount, SUM(IF(trans.type = 'cr', trans.amount, 0)) AS creditAmount FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="6"><b>Total</b></td>
							<td align="right"><b class="text-primary"><?php echo round($row->debitAmount,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->creditAmount,2);?></b></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php');?>