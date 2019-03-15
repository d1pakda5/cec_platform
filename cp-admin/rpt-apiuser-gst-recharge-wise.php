<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$tbl = new ListTable();
include('gst-function.php');

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '';

$sWhere = "WHERE rch.request_date BETWEEN '".$aFrom."' AND '".$aTo."' AND rch.status IN (0,1) AND usr.user_type='1' ";

if(isset($_GET['s']) && $_GET['s']!='') {
	$aStr = mysql_real_escape_string($_GET['s']);
	$sWhere .= " AND (rch.recharge_id='".$aStr."' OR rch.account_no='".$aStr."' OR rch.operator_ref_no='".$aStr."' OR rch.api_txn_no='".$aStr."') ";
}
if(isset($_GET['opr']) && $_GET['opr']!='') {
	$sWhere .= " AND rch.operator_id='".mysql_real_escape_string($_GET['opr'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND rch.uid='".mysql_real_escape_string($_GET['uid'])."' ";
}
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= " AND opr.billing_type='".mysql_real_escape_string($_GET['type'])."' ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id LEFT JOIN apps_user usr ON rch.uid=usr.uid LEFT JOIN usercommissions ucm ON usr.uid=ucm.uid AND rch.operator_id=ucm.operator_id $sWhere ORDER BY rch.request_date ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-apiuser-gst-recharge-wise.php');

$array['recharge_status'] = getRechargeStatusList();

$meta['title'] = "Report GST Recharge Wise";
include('header.php');
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
function doExcels(){
	var from = $('#from').val();
	var to = $('#to').val();
	var type = $('#type').val();
	var uid = $('#uid').val();
	window.location='excel/apiuser-gst-recharge-wise.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ GST Recharge Wise</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List GST Recharge Wise</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control input-sm">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control input-sm">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" class="form-control input-sm">
									<option value="">Select Type</option>
									<option value="1"<?php if($type=='1') { ?> selected="selected"<?php } ?>>P2P</option>
									<option value="2"<?php if($type=='2') { ?> selected="selected"<?php } ?>>P2A</option>
									<option value="3"<?php if($type=='3') { ?> selected="selected"<?php } ?>>SURCHARGE</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" size="8" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="UID" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="show" class="form-control input-sm">
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if($limit == '100') { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if($limit == '250') { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if($limit == '500') { ?> selected="selected"<?php } ?>>500</option>
									<option value="1000" <?php if($limit == '1000') { ?> selected="selected"<?php } ?>>1000</option>
									<option value="2000" <?php if($limit == '2000') { ?> selected="selected"<?php } ?>>2000</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-sm btn-warning">
								<button type="button" onclick="doExcels()" class="btn btn-sm btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th width="5%">Date</th>
							<th>Txn No</th>
							<th>User</th>
							<th>States</th>
							<th>Operator</th>
							<th>Type</th>
							<th>Mobile</th>
							<th>Amt</th>
							<th>Sc</th>
							<th>Comm</th>
							<th>Net</th>
							<th>Taxable</th>
							<th>GST</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT rch.*, opr.operator_name, opr.billing_type, opr.is_surcharge, usr.user_type, usr.company_name, usr.states, ucm.comm_dist, ucm.comm_api FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							if($result->user_type=='1') {
								$com_value = $result->comm_api;
							}
							else {
								$com_value = $result->comm_dist;
							}
							$net_amt = _getCommAmount($result->amount,$com_value,$result->is_surcharge,$result->surcharge);
							$gst = _gstTaxAmount($net_amt);
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y", strtotime($result->request_date));?></td>
							<td><?php echo $result->recharge_id;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<td><?php echo $result->states;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo _gstBillingType($result->billing_type);?></td>
							<td><?php echo $result->account_no;?></td>	
							<td align="right"><?php echo round($result->amount,2);?></td>
							<td align="right"><?php echo round($result->surcharge,2);?></td>
							<td><?php echo $com_value;?></td>
							<td><?php echo $net_amt;?></td>
							<td><?php echo $gst[0];?></td>
							<td><?php echo $gst[1];?></td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(rch.amount) AS totalRecharge, SUM(rch.surcharge) AS totalSurcharge FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="8"><b>Total</b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalRecharge,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalSurcharge,2);?></b></td>
							<td colspan="3"></td>
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
<?php include('footer.php'); ?>