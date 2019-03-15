<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.gst.php');
$gst = new GST();
include('../system/class.pagination.php');
$tbl = new ListTable();

$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : '2017';
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$utype = isset($_GET["utype"]) && $_GET["utype"]!='' ? mysql_real_escape_string($_GET["utype"]) : '';

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime('-168 HOURS', strtotime($from)));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE id!='' ";
if(isset($_GET['utype']) && $_GET['utype']!='') {
	$sWhere .= " AND user.user_type='".mysql_real_escape_string($_GET["utype"])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND gst.uid='".mysql_real_escape_string($_GET["uid"])."' ";
}
if(isset($_GET["type"]) && $_GET["type"]!='') {
	$sWhere .= " AND gst.bill_type='".mysql_real_escape_string($_GET["type"])."' ";
}
//to make pagination
$statement = "gstinvoices gst LEFT JOIN apps_user user ON gst.uid=user.uid $sWhere ORDER BY gst.invoice_month,gst.id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('gst-invoices.php');

$months = $gst->getMonthList();

$meta['title'] = "GST Invoices";
include('header.php');
function getGstInvoiceLink($type) {
	if($type=='1') {
		$data = "gst-p2p";
	} elseif($type=='2') {
		$data = "gst-p2a";
	} elseif($type=='3') {
		$data = "gst-p2s";
	} else {
		$data = "Invoice";
	}
	return $data;
}
function getGstTypeAmt($type,$amount) {
	$sgst = 0;
	$igst = 0;
	if($type=='2') {
		//SGST
		$sgst = $amount/2;
	} else {
		//IGST
		$igst = $amount;
	}
	return array('igst'=>$igst,'sgst'=>$sgst,'cgst'=>$sgst);
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
	window.location='excel/gst-txn-monthwise.php?month='+month+'&utype='+utype+'&uid='+uid+'&type='+type+'&op='+op;
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
								<button type="button" onclick="doExcel('rptGstTanMonthWise')" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th>S.No</th>
							<th>User Name</th>
							<th>User Type</th>
							<th>Month</th>
							<th>Invoice No.</th>
							<th>Invoice Type</th>
							<th>Net</th>
							<th>SGST Tax</th>
							<th>CGST Tax</th>
							<th>IGST Tax</th>
							<th>Total</th>
							<th>View</th>
							<th>Download</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT gst.*, user.company_name,user.user_type,user.gst_type FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
							$gst = getGstTypeAmt($row->gst_type,$row->tax_amount);
						?>
						<tr>
							<td><?php echo $scnt++;?> </td>
							<td><?php echo $row->company_name;?> (<?php echo $row->uid;?>)</td>
							<td><?php echo getUserType($row->user_type);?></td>
							<td><?php echo date("F, Y", strtotime($row->invoice_month));?></td>
							<td><?php echo $row->invoice_num;?></td>
							<td><b><?php echo strtoupper(getBillingType($row->bill_type));?></b></td>
							<td align="right"><?php echo $row->sub_total;?></td>
							<td align="right"><b><?php echo $gst['sgst'];?></b></td>
							<td align="right"><b><?php echo $gst['cgst'];?></b></td>
							<td align="right"><b><?php echo $gst['igst'];?></b></td>
							<td align="right"><?php echo $row->total_amount;?></td>
							<td align="center">
								<a href="../gst/<?php echo getGstInvoiceLink($row->bill_type);?>.php?id=<?php echo $row->id;?>" target="_blank" class="btn btn-xs btn-success"><i class="fa fa-file-text-o"></i> View</a>
							</td>
							<td align="center">
								<a href="../gst/getpdf.php?id=<?php echo $row->id;?>&type=<?php echo $row->bill_type;?>" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-download"></i> Download</a>
							</td>
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