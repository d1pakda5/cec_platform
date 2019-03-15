<?php
session_start();
if(!isset($_SESSION['admin'])) {
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

$sWhere = "WHERE ts.txnadmid!='0' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND (ts.remark LIKE '%".mysql_real_escape_string($_GET['s'])."%')  ";
} else {
	$sWhere .= " AND ts.txndate BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET["uid"]) && $_GET["uid"] != '') {
	$sWhere .= " AND ts.txntouid='".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"] != '') {
	$sWhere .= " AND ts.txnterm='".mysql_real_escape_string($_GET["type"])."' ";
}

$statement = "transactions_adm ts LEFT JOIN apps_user us ON ts.txntouid=us.uid $sWhere ORDER BY ts.txndate DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-zone-fund-deduct.php');

$meta['title'] = "Transaction Reports";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery(".fancyAction").fancybox({
		closeClick	: false,
		helpers   : { 
			overlay : {closeClick: false}
		},
		'afterClose':function () {
    	window.location.reload();
    }
	});
});
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/zone-fund-deduct.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Transaction</small></div>
			<div class="pull-right">
				<a href="admin-dashboard.php" class="btn btn-primary"><i class="fa fa-reply"></i> Dashboard</a>
			</div>
		</div>
		<div class="box min-height-480">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Transactions</h3>
			</div>			
			<div class="box-body">
				<div class="box-filter">
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
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control">
									<option value=""></option>
									<option value="FUND" <?php if(isset($_GET['type']) && $_GET['type'] == "FUND") { ?> selected="selected"<?php } ?>>FUND</option>
									<option value="RECHARGE" <?php if(isset($_GET['type']) && $_GET['type'] == "RECHARGE") { ?> selected="selected"<?php } ?>>RECHARGE</option>
									<option value="FAILURE" <?php if(isset($_GET['type']) && $_GET['type'] == "FAILURE") { ?> selected="selected"<?php } ?>>FAILURE</option>
									<option value="REFUND" <?php if(isset($_GET['type']) && $_GET['type'] == "REFUND") { ?> selected="selected"<?php } ?>>REFUND</option>
									<option value="REVERT" <?php if(isset($_GET['type']) && $_GET['type'] == "REVERT") { ?> selected="selected"<?php } ?>>REVERT</option>
								</select>
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
							<th>Balance</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT ts.*, us.* FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->txndate));?></td>
							<td><?php if($row->company_name) { echo $row->company_name."(".$row->uid.")"; } else { echo "Admin";}?></td>
							<td><?php echo $row->txnterm;?></td>
							<td><?php echo $row->txnrefno;?></td>
							<td><?php echo $row->remark;?></td>
							<?php if($row->txntype=='dr') { ?>
							<td align="right"><?php echo round($row->txnamount,2);?></td>
							<td align="right"></td>
							<?php } else { ?>							
							<td align="right"></td>
							<td align="right"><?php echo round($row->txnamount,2);?></td>
							<?php } ?>
							<td align="right"><?php echo round($row->closingbalance,2);?></td>
							<td align="center">
								<?php if($row->txnstatus=='0' && $row->txntype=='dr') {?>
									<a class="fancyAction fancybox.ajax" href="zone-fund-refund.php?id=<?php echo $row->txnadmid;?>" title="Transaction Details">
										<img src="../images/revert_new_1.png" />
									</a>
								<?php } else {?>
									<img src="../images/revert_new_0.png" />
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT ts.*, us.uid, SUM(IF(ts.txntype='dr', ts.txnamount, 0)) AS debitAmount, SUM(IF(ts.txntype='cr', ts.txnamount, 0)) AS creditAmount FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="6"><b>Total</b></td>
							<td align="right"><b class="text-primary"><?php echo round($row->debitAmount,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->creditAmount,2);?></b></td>
							<td></td>
							<td></td>
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