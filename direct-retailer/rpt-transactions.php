<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

//$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$from = date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime('-48 HOURS', strtotime($from)));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE transaction_status = '1' AND account_id = '".$_SESSION['retailer_uid']."' AND transaction_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
$statement = "transactions $sWhere ORDER BY transaction_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 10 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-transactions.php');

$meta['title'] = "Transactions";
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
			<div class="page-title">Transactions</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Transactions</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
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
					</form>
				</div>
				<table class="table table-basic">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
							<th>Date</th>
							<th>Type</th>
							<th>Ref.</th>
							<th>Remark</th>
							<th>Type</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->transaction_date));?></td>
							<td><?php echo $row->transaction_term;?></td>
							<td><?php echo $row->transaction_ref_no;?></td>
							<td><?php echo $row->remark;?></td>
							<td align="center"><?php echo $row->type;?></td>
							<td align="right"><?php echo round($row->amount,2);?></td>
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