<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere = " WHERE trans.transaction_ref_no = '".mysql_real_escape_string($_GET["s"])."' ";
} else {
	$sWhere = " WHERE trans.transaction_id = 'a' ";
}

$statement = "transactions trans LEFT JOIN apps_user user ON trans.to_account_id = user.uid $sWhere ORDER BY trans.transaction_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-user-transactions.php');

$meta['title'] = "User Transaction Reports";
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
			<div class="page-title">Reports <small>/ Transaction Status</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Transactions Status</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">	
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Enter Transaction No" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-basic">
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
							<th>Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT trans.*, user.company_name, user.uid FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->transaction_date));?></td>
							<td><?php if($row->company_name) { echo $row->company_name; } else { echo SITENAME;}?></td>
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
							<td align="right"><?php echo round($row->closing_balance,2);?></td>
						</tr>
						<?php } ?>
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