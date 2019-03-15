<?php
session_start();
include('config.php');
//
include('../system/class.gst.php');
$gst = new GST();
//
include('../system/class.pagination.php');
$tbl = new ListTable();
$date = isset($_GET['date']) && $_GET['date']!='' ? mysql_real_escape_string($_GET['date']) : date("m");
$dts = explode("-",$date);
$month = isset($dts[0]) && $dts[0]!='' ? $dts[0] : date("m");
$year = isset($dts[1]) && $dts[1]!='' ? $dts[1] : date("Y");
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$utype = isset($_GET["utype"]) && $_GET["utype"]!='' ? mysql_real_escape_string($_GET["utype"]) : '';
$op = isset($_GET['op']) && $_GET['op']!='' ? mysql_real_escape_string($_GET['op']) : '';
if($date=='all') {
	$dtFrom = "2017-07-01 00:00:00";
	$dtTo = "2018-12-31 23:23:59";
}

$sWhere = "WHERE gst.rch_date BETWEEN '".$dtFrom."' AND '".$dtTo."' AND gst.has_gst='1' AND gst.user_type='4' ";
if(isset($_GET['utype']) && $_GET['utype']!='') {
	//$sWhere .= " AND gst.user_type='".mysql_real_escape_string($_GET["utype"])."' ";
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
//
$statement = "gst_monthly_txns gst LEFT JOIN apps_user usr ON gst.uid=usr.uid $sWhere GROUP BY gst.uid ORDER BY gst.uid ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 250 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-gst-invoice-new.php');
//
$months = $gst->getMonthList();
$meta['title'] = "GST Invoice Reports";
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
			<div class="page-title">GST Invoice MonthWise</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List GST Invoice Detail</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-2">
							<div class="form-group">
								<select name="date" id="date" class="form-control input-sm">
									<option value="">Select Month</option>
									<option value="07-2017"<?php if($date=='07-2017') { ?> selected="selected"<?php } ?>>July 2017</option>		
									<option value="08-2017"<?php if($date=='08-2017') { ?> selected="selected"<?php } ?>>August 2017</option>	
									<option value="09-2017"<?php if($date=='09-2017') { ?> selected="selected"<?php } ?>>September 2017</option>	
									<option value="10-2017"<?php if($date=='10-2017') { ?> selected="selected"<?php } ?>>October 2017</option>	
									<option value="11-2017"<?php if($date=='11-2017') { ?> selected="selected"<?php } ?>>November 2017</option>	
									<option value="12-2017"<?php if($date=='12-2017') { ?> selected="selected"<?php } ?>>December 2017</option>	
									<option value="01-2018"<?php if($date=='01-2018') { ?> selected="selected"<?php } ?>>January 2018</option>
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
								<input type="submit" value="Filter" class="btn btn-warning btn-sm">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="1%">S.No.</th>
							<th>User</th>
							<th>UID</th>
							<th>User Type</th>
							<th>Month</th>
							<th width="8%">P2P INVOICE</th>
							<th width="8%">SURCHARGE</th>
							<th width="8%">P2A INVOICE</th>
							<th width="8%">RECEIPT</th>
							<th width="8%">STATEMENT</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT gst.*, usr.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php if($row->company_name) { echo $row->company_name; } else { echo SITENAME;}?></td>
							<td><?php echo $row->uid;?></td>
							<td><?php echo getUserType($row->user_type);?></td>
							<td><?php echo date("F, Y", strtotime($row->rch_date));?></td>
							<td align="center"><a href="gst-p2p-update.php?id=<?php echo $row->uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-xs">P2P</a></td>
							<td align="center"><a href="gst-p2s-update.php?id=<?php echo $row->uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-xs">Surcharge</a></td>
							<td align="center"><a href="gst-p2a-update.php?id=<?php echo $row->uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-xs">P2A</a></td>
							<td align="center"><a href="gst-p2d-update.php?id=<?php echo $row->uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-warning btn-xs">Receipt</a></td>
							<td align="center"><a href="gst-excel-update.php?id=<?php echo $row->uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-info btn-xs">Statement</a></td>
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