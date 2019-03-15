<?php
session_start();
include('config.php');
include('../system/class.gst.php');
$gst = new GST();
include('../system/class.pagination.php');
$tbl = new ListTable();

$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : '2017';
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$utype = isset($_GET["utype"]) && $_GET["utype"]!='' ? mysql_real_escape_string($_GET["utype"]) : '';
$op = isset($_GET['op']) && $_GET['op']!='' ? mysql_real_escape_string($_GET['op']) : '';
if($month=='all') {
	$dtFrom = "2017-07-01 00:00:00";
	$dtTo = "2017-12-31 23:23:59";
}
$sWhere = "WHERE gst.rch_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
if(isset($_GET['utype']) && $_GET['utype']!='') {
	$sWhere .= " AND gst.user_type='".mysql_real_escape_string($_GET["utype"])."' ";
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
if(isset($_GET["grp"]) && $_GET["grp"]!='') {
	$sWhere .= " AND gst.item_group='".mysql_real_escape_string($_GET["grp"])."' ";
}
if(isset($_GET["inv"]) && $_GET["inv"]!='') {
	$sWhere .= " AND gst.has_gst='".mysql_real_escape_string($_GET["inv"])."' ";
}

$statement = "gst_monthly_txns gst LEFT JOIN apps_user user ON gst.uid=user.uid $sWhere ORDER BY gst.rch_date ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-gst-monthwise.php');
//
$months = $gst->getMonthList();
$meta['title'] = "GST Txn Reports";
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
function doExcel(){
	var month = $('#month').val();
	var utype = $('#utype').val();
	var uid = $('#uid').val();
	var type = $('#type').val();
	var op = $('#op').val();
	window.location='excel-txn-monthwise.php?month='+month+'&utype='+utype+'&uid='+uid+'&type='+type+'&op='+op;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ GST MonthWise (OLD)</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List GST Detail</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-2">
							<div class="form-group">
								<select name="month" id="month" class="form-control input-sm">
									<option value="">Select Month</option>
									<?php foreach($months as $key=>$data) { ?>
									<option value="<?php echo $key;?>"<?php if($month==$key) { ?> selected="selected"<?php } ?>><?php echo $data;?> 2017</option>
									<?php } ?>
									<option value="all"<?php if($month=='all') { ?> selected="selected"<?php } ?>>All</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="utype" id="utype" class="form-control input-sm">
									<option value="">User</option>
									<option value="1" <?php if(isset($_GET['utype']) && $_GET['utype']=="1") { ?>selected="selected"<?php } ?>>API User</option>
									<option value="4" <?php if(isset($_GET['utype']) && $_GET['utype']=="4") { ?>selected="selected"<?php } ?>>Distributor</option>
									<option value="5" <?php if(isset($_GET['utype']) && $_GET['utype']=="5") { ?>selected="selected"<?php } ?>>Retailer</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="inv" id="inv" class="form-control input-sm">
									<option value="">Invoice</option>
									<option value="0" <?php if(isset($_GET['inv']) && $_GET['inv']=="0") { ?>selected="selected"<?php } ?>>No</option>
									<option value="1" <?php if(isset($_GET['inv']) && $_GET['inv']=="1") { ?>selected="selected"<?php } ?>>Yes</option>
								</select>
							</div>
						</div>											
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="User UID" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control input-sm">
									<option value="">--Select--</option>
									<option value="1" <?php if(isset($_GET['type']) && $_GET['type']=="1") { ?> selected="selected"<?php } ?>>P2P</option>
									<option value="2" <?php if(isset($_GET['type']) && $_GET['type']=="2") { ?> selected="selected"<?php } ?>>P2A</option>
									<option value="3" <?php if(isset($_GET['type']) && $_GET['type']=="3") { ?> selected="selected"<?php } ?>>SURCHARGE</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="op" id="op" class="form-control input-sm">
									<option value="">--Operator--</option>
									<?php foreach($oprs as $key=>$data) { ?>
									<option value="<?php echo $key;?>"<?php if($op==$key) { ?> selected="selected"<?php } ?>><?php echo $data;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning btn-sm">
								<button type="button" onclick="doExcel('rptGstTanMonthWise')" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>
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
							<th>Operator</th>
							<th>Amount</th>
							<th>Comm/Sur</th>
							<th>Taxable</th>
							<th>GST</th>
							<th>Total</th>
						</tr>
					</thead>
					<thead>
						<?php $qry1 = $db->query("SELECT SUM(gst.rch_amount) AS sum_amount, SUM(gst.rch_comm_value) AS sum_comm_value, SUM(gst.gst_net) AS sum_gst_net, SUM(gst.gst_tax) AS sum_gst_tax, SUM(gst.taxable_value) AS sum_taxable_value FROM {$statement}");
						$row1 = $db->fetchNextObject($qry1); ?>
						<tr>
							<td colspan="5"><b>Total</b></td>
							<td><b><?php echo $row1->sum_amount;?></b></td>
							<td><b><?php echo $row1->sum_comm_value;?></b></td>
							<td><b><?php echo $row1->sum_gst_net;?></b></td>
							<td><b><?php echo $row1->sum_gst_tax;?></b></td>
							<td><b><?php echo $row1->sum_taxable_value;?></b></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT gst.*, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y", strtotime($row->rch_date));?></td>
							<td><?php if($row->company_name) { echo $row->company_name."(".$row->uid.")"; } else { echo SITENAME;}?></td>
							<td><?php echo getBillingType($row->bill_type);?></td>
							<td><?php echo gstOpName($oprs,$row->operator_id);?></td>
							<td align="right"><?php echo $row->rch_amount;?></td>
							<td align="right"><?php echo $row->rch_comm_value;?></td>
							<td align="right"><?php echo $row->gst_net;?></td>
							<td align="right"><?php echo $row->gst_tax;?></td>
							<td align="right"><?php echo $row->taxable_value;?></td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(gst.rch_amount) AS sum_amount, SUM(gst.rch_comm_value) AS sum_comm_value, SUM(gst.gst_net) AS sum_gst_net, SUM(gst.gst_tax) AS sum_gst_tax, SUM(gst.taxable_value) AS sum_taxable_value FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td colspan="5"><b>Total</b></td>
							<td><b><?php echo $row->sum_amount;?></b></td>
							<td><b><?php echo $row->sum_comm_value;?></b></td>
							<td><b><?php echo $row->sum_gst_net;?></b></td>
							<td><b><?php echo $row->sum_gst_tax;?></b></td>
							<td><b><?php echo $row->sum_taxable_value;?></b></td>
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