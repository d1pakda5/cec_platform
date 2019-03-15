<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
 

if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere = "WHERE (gst.recharge_id='".mysql_real_escape_string($_GET['s'])."' OR gst.transaction_id='".mysql_real_escape_string($_GET['s'])."') ";
} else {
	$sWhere = "WHERE gst.trans_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND gst.uid='".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET['op']) && $_GET['op']!='') {
	$sWhere .= " AND gst.operator_id='".mysql_real_escape_string($_GET["op"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"]!='') {
	$sWhere .= " AND gst.bill_type='".mysql_real_escape_string($_GET["type"])."' ";
}

$statement = "gst_transactions gst LEFT JOIN apps_user user ON gst.uid=user.uid $sWhere ORDER BY gst.trans_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-gst-deduct.php');

$meta['title'] = "GST Deduct Reports";
include('header.php');
$oprs = [];
$query = $db->query("SELECT operator_id,operator_name FROM operators ORDER BY service_type,operator_name ASC ");
while($row = $db->fetchNextObject($query)) {
	$oprs[$row->operator_id] = $row->operator_name;
}
function gstOpName($opr, $id) {
	$result = "";
	foreach($opr as $key=>$data) {
		if($key == $id) {
			$result = $data;
		}
	}
	return $result;
}
?>
<script>
$(document).ready(function() {
	$('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	$('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ GST Deduct</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List GST &amp; TDS Detail</h3>
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
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="User UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control">
									<option value="">--Select--</option>
									<option value="1" <?php if(isset($_GET['type']) && $_GET['type']=="1") { ?> selected="selected"<?php } ?>>P2P</option>
									<option value="2" <?php if(isset($_GET['type']) && $_GET['type']=="2") { ?> selected="selected"<?php } ?>>P2A</option>
									<option value="3" <?php if(isset($_GET['type']) && $_GET['type']=="3") { ?> selected="selected"<?php } ?>>SURCHARGE</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="1%">S.No.</th>
							<th>Date</th>
							<th>User</th>
							<th>Type</th>
							<th>Rch ID</th>
							<th>Operator</th>
							<th>Amount</th>
							<th>Comm/Sur</th>
							<th>GST Deduct</th>
							<th>Tds On</th>
							<th>Tds Rate</th>
							<th>Tds Deduct</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT gst.*, user.company_name, user.uid FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->trans_date));?></td>
							<td><?php if($row->company_name) { echo $row->company_name; } else { echo SITENAME;}?></td>
							<td><?php echo getBillingType($row->bill_type);?></td>
							<td><a href="rpt-status.php?s=<?php echo $row->recharge_id;?>" target="_blank"><?php echo $row->recharge_id;?></a></td>
							<td><?php echo gstOpName($oprs,$row->operator_id);?></td>
							<td><?php echo $row->rch_amount;?></td>
							<td><?php echo $row->rch_comm_value;?></td>
							<td><?php echo $row->gst_amount_deduct;?></td>
							<td><?php echo $row->tds_value;?></td>
							<td><?php echo $row->tds_rate;?></td>
							<td><?php echo $row->tds_amount;?></td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(gst.rch_amount) AS sum_amount, SUM(rch_comm_value) AS sum_comm_value, SUM(gst_amount_deduct) AS sum_gst_amount_deduct, SUM(tds_value) AS sum_tds_value, SUM(tds_amount) AS sum_tds_amount FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td colspan="6"><b>Total</b></td>
							<td><b><?php echo $row->sum_amount;?></b></td>
							<td><b><?php echo $row->sum_comm_value;?></b></td>
							<td><b><?php echo $row->sum_gst_amount_deduct;?></b></td>
							<td><b><?php echo $row->sum_tds_value;?></b></td>
							<td></td>
							<td><b><?php echo $row->sum_tds_amount;?></b></td>
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