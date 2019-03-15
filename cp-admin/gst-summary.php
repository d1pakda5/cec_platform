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
//
$status = isset($_GET['status']) && $_GET['status']!='' ? mysql_real_escape_string($_GET['status']) : '';
$uid = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : '0';
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '1';
$month = isset($_GET['month']) && $_GET['month']!='' ? mysql_real_escape_string($_GET['month']) : date("m");
$year = isset($_GET['year']) && $_GET['year']!='' ? mysql_real_escape_string($_GET['year']) : date("Y");
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : '0';
//
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$uid."' ");
if(!$user) {
	header("location:index.php");
	exit();
}
$balance = "ERROR";
$wallet = $db->queryUniqueObject("SELECT * FROM apps_wallet WHERE user_id='".$user->user_id."' ");
if($wallet) {
	$balance = $wallet->balance;
}	
//
$dtFrom = date($year."-".$month."-01 00:00:00");
$dtTo = date($year."-".$month."-t 23:23:59", strtotime($year."-".$month."-01"));
$sWhere = "";
if($user->user_type=='4') {
	$ret = '';
	$qry = $db->query("SELECT uid FROM apps_user WHERE dist_id='".$user->uid."' ");
	while($rlt = $db->fetchNextObject($qry)) {
		$ret .= $rlt->uid.", ";
	}
	$ret .= '0';
	$sWhere = "WHERE rch.uid IN ($ret) AND rch.status='0' AND opr.billing_type='".$type."' AND rch.request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
} else {
	$sWhere = "WHERE rch.uid='".$uid."' AND rch.status='0' AND opr.billing_type='".$type."' AND rch.request_date BETWEEN '".$dtFrom."' AND '".$dtTo."' ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id=opr.operator_id $sWhere GROUP BY rch.operator_id ORDER BY opr.service_type,opr.operator_id ASC";
// Commissions
if($user->user_type=='1') {
	$comm_uid = $uid;
} else if($user->user_type=='4') {
	$comm_uid = $uid;
} else if($user->user_type=='5') {
	$comm_uid = $user->dist_id;
} else {
	$comm_uid = 0;
}
$comms = [];
$query = $db->query("SELECT * FROM usercommissions WHERE uid='".$comm_uid."' ");
while($result = $db->fetchNextObject($query)) {
	$comms[] = $result;
}
//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 120 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('gst-summary.php');
//
$months = $gst->getMonthList();
$inMonth = date($year."-".$month."-t");
$todayDate = date("Y-m-01");
$meta['title'] = "GST Summary - ".$user->company_name;
include('header.php');
?>
<script type="text/javascript" src="../js/tableExport.js"></script>
<script type="text/javascript" src="../js/jquery.base64.js"></script>
<style>
.list-buttons .btn {
	margin-bottom:10px;
	text-align:left;
}
</style>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"><?php echo getUserType($user->user_type);?> <small>/ <?php echo $user->company_name;?> (<?php echo $user->uid;?>)</small></div>
			<div class="pull-right">
				<?php if($user->user_type=='1') {?>
				<a href="api-user.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type=='3') {?>
				<a href="master-distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type=='4') {?>
				<a href="distributor.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } else if($user->user_type=='5') {?>
				<a href="retailer.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
				<?php } ?>
			</div>
		</div>
		<?php if($error == 2) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Password has been reset successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Pin has been reset successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> GST Summary (<b class="text-primary"><?php echo getInvoiceTypeName($type);?></b>)</h3>
			</div>			
			<div class="box-body min-height-480">
				<div class="box-filter padding-20">
					<div class="row">
						<div class="col-sm-8">
							<form method="get">
								<input type="hidden" name="uid" value="<?php echo $uid;?>">
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
										<select name="month" class="form-control input-sm">
											<option value="">Select Month</option>
											<?php foreach($months as $key=>$data) { ?>
											<option value="<?php echo $key;?>"<?php if($month==$key) { ?> selected="selected"<?php } ?>><?php echo $data;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<select name="year" class="form-control input-sm">
											<option value="">Select Year</option>
											<?php for($y=2017; $y<=2018; $y++) { ?>
											<option value="<?php echo $y;?>"<?php if($year==$y) {?> selected="selected"<?php } ?>><?php echo $y;?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<input type="submit" value="Filter" class="btn btn-warning btn-sm">
										<button type="button" onClick ="$('#gstSummary').tableExport({type:'excel',escape:'false'});" class="btn btn-success btn-sm">Export</button>			
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-4 text-right">
							<?php if($inMonth < $todayDate){?>	
							<a href="gst-invoice-p2p.php?id=<?php echo $uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-sm"><b>P2P INVOICE</b></a>
							<a href="gst-invoice-p2a.php?id=<?php echo $uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-sm"><b>P2A INVOICE</b></a>
							<a href="gst-invoice-p2s.php?id=<?php echo $uid;?>&month=<?php echo $month;?>&year=<?php echo $year;?>" target="_blank" class="btn btn-success btn-sm"><b>SURCHARGE INVOICE</b></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php if($type=='3') { ?>
				<table id="gstSummary" class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
							<th>Operator</th>
							<th>HSN/SAC</th>
							<th>Gross</th>
							<th>Surcharge (Rs)</th>
							<th>Sur Amount.</th>
							<th>Net Amount.</th>
							<th>Taxable Amount.</th>
							<th>CGST 9%</th>
							<th>SGST 9%</th>
							<th>IGST 18%</th>
							<th>Total Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$total_amount = 0;
						$total_sur = 0;
						$total_net = 0;
						$total_taxable = 0;
						$total_cgst = 0;
						$total_sgst = 0;
						$total_igst = 0;
						$query = $db->query("SELECT SUM(rch.amount) AS amt, SUM(rch.surcharge) AS sur_amt, rch.operator_id, opr.operator_name, opr.item_group, opr.billing_type, opr.hsn_sac_code FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							$row_amount = $result->amt;
							$total_amount += $row_amount;
							//
							$sur_amt = $result->sur_amt;
							$total_sur += $sur_amt;
							//
							$net_amount = $total_sur;
							$total_net += $net_amount;
							//
							$taxable_amount = $gst->getTaxableAmount($net_amount);
							$total_taxable += $taxable_amount;
							//
							$gst_sum = $gst->getTaxSummary($net_amount,$user->gst_type);
							$cgst = $gst_sum['cgst_amount'];
							$total_cgst += $cgst;
							//
							$sgst = $gst_sum['sgst_amount'];
							$total_sgst += $sgst;
							//
							$igst = $gst_sum['igst_amount'];
							$total_igst += $igst;
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->hsn_sac_code;?></td>
							<td align="right"><?php echo $result->amt;?></td>
							<td align="center">-</td>
							<td align="right"><?php echo $sur_amt;?></td>
							<td align="right"><?php echo $net_amount;?></td>
							<td align="right"><?php echo $taxable_amount;?></td>
							<td align="right"><?php echo $cgst;?></td>
							<td align="right"><?php echo $sgst;?></td>
							<td align="right"><?php echo $igst;?></td>
							<td align="right"><?php echo $net_amount;?></td>
						</tr>
						<?php } ?>
						<tr style="font-weight:bold;">
							<td align="center"></td>
							<td align="center">Total</td>
							<td align="center"></td>
							<td align="right"><?php echo $total_amount;?></td>
							<td align="right"></td>
							<td align="right"><?php echo $total_sur;?></td>
							<td align="right"><?php echo $total_net;?></td>
							<td align="right"><?php echo $total_taxable;?></td>
							<td align="right"><?php echo $total_cgst;?></td>
							<td align="right"><?php echo $total_sgst;?></td>
							<td align="right"><?php echo $total_igst;?></td>
							<td align="right"><?php echo $total_net;?></td>
						</tr>
					</tbody>
				</table>
				<?php } else if($type=='2') { ?>
				<table id="gstSummary" class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
							<th>Operator</th>
							<th>HSN/SAC</th>
							<th>Gross</th>
							<th>Comm. (%)</th>
							<th>Comm Amount.</th>
							<th>Net Amount.</th>
							<th>Taxable Amount.</th>
							<th>CGST 9%</th>
							<th>SGST 9%</th>
							<th>IGST 18%</th>
							<th>Total Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$total_amount = 0;
						$total_comm = 0;
						$total_net = 0;
						$total_taxable = 0;
						$total_cgst = 0;
						$total_sgst = 0;
						$total_igst = 0;
						$query = $db->query("SELECT SUM(rch.amount) AS amt, rch.operator_id, opr.operator_name, opr.item_group, opr.billing_type, opr.hsn_sac_code FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							$row_amount = $result->amt;
							$total_amount += $row_amount;
							//
							$comm_per = $gst->getOperatorCommission($result->operator_id,$comms,$user->user_type);
							//
							$comm_amt = $gst->getTotalCommission($row_amount,$comm_per);
							$total_comm += $comm_amt;
							//
							$net_amount = $comm_amt;
							$total_net += $net_amount;
							//
							$taxable_amount = $gst->getTaxableAmount($net_amount);
							$total_taxable += $taxable_amount;
							//
							$gst_sum = $gst->getTaxSummary($net_amount,$user->gst_type);
							$cgst = $gst_sum['cgst_amount'];
							$total_cgst += $cgst;
							//
							$sgst = $gst_sum['sgst_amount'];
							$total_sgst += $sgst;
							//
							$igst = $gst_sum['igst_amount'];
							$total_igst += $igst;
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->hsn_sac_code;?></td>
							<td align="right"><?php echo $result->amt;?></td>
							<td align="right"><?php echo $comm_per;?></td>
							<td align="right"><?php echo $comm_amt;?></td>
							<td align="right"><?php echo $net_amount;?></td>
							<td align="right"><?php echo $taxable_amount;?></td>
							<td align="right"><?php echo $cgst;?></td>
							<td align="right"><?php echo $sgst;?></td>
							<td align="right"><?php echo $igst;?></td>
							<td align="right"><?php echo $net_amount;?></td>
						</tr>
						<?php } ?>
						<tr style="font-weight:bold;">
							<td align="center"></td>
							<td align="center">Total</td>
							<td align="center"></td>
							<td align="right"><?php echo $total_amount;?></td>
							<td align="right"></td>
							<td align="right"><?php echo $total_comm;?></td>
							<td align="right"><?php echo $total_net;?></td>
							<td align="right"><?php echo $total_taxable;?></td>
							<td align="right"><?php echo $total_cgst;?></td>
							<td align="right"><?php echo $total_sgst;?></td>
							<td align="right"><?php echo $total_igst;?></td>
							<td align="right"><?php echo $total_net;?></td>
						</tr>
					</tbody>
				</table>
				<?php } else if($type=='1') { ?>
				<table id="gstSummary" class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
							<th>Operator</th>
							<th>HSN/SAC</th>
							<th>Gross</th>
							<th>Comm.(%)</th>
							<th>Comm Amount.</th>
							<th>Net Amount.</th>
							<th>Taxable Amount.</th>
							<th>CGST 9%</th>
							<th>SGST 9%</th>
							<th>IGST 18%</th>
							<th>Total Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$total_amount = 0;
						$total_comm = 0;
						$total_net = 0;
						$total_taxable = 0;
						$total_cgst = 0;
						$total_sgst = 0;
						$total_igst = 0;
						$query = $db->query("SELECT SUM(rch.amount) AS amt, rch.operator_id, opr.operator_name, opr.item_group, opr.billing_type, opr.hsn_sac_code FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							$row_amount = $result->amt;
							$total_amount += $row_amount;
							//
							$comm_per = $gst->getOperatorCommission($result->operator_id,$comms,$user->user_type);
							//
							$comm_amt = $gst->getTotalCommission($row_amount,$comm_per);
							$total_comm += $comm_amt;
							//
							$net_amount = $row_amount - $comm_amt;
							$total_net += $net_amount;
							//
							$taxable_amount = $gst->getTaxableAmount($net_amount);
							$total_taxable += $taxable_amount;
							//
							$gst_sum = $gst->getTaxSummary($net_amount,$user->gst_type);
							$cgst = $gst_sum['cgst_amount'];
							$total_cgst += $cgst;
							//
							$sgst = $gst_sum['sgst_amount'];
							$total_sgst += $sgst;
							//
							$igst = $gst_sum['igst_amount'];
							$total_igst += $igst;
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->hsn_sac_code;?></td>
							<td align="right"><?php echo $result->amt;?></td>
							<td align="right"><?php echo $comm_per;?></td>
							<td align="right"><?php echo $comm_amt;?></td>
							<td align="right"><?php echo $net_amount;?></td>
							<td align="right"><?php echo $taxable_amount;?></td>
							<td align="right"><?php echo $cgst;?></td>
							<td align="right"><?php echo $sgst;?></td>
							<td align="right"><?php echo $igst;?></td>
							<td align="right"><?php echo $net_amount;?></td>
						</tr>
						<?php } ?>
						<tr style="font-weight:bold;">
							<td align="center"></td>
							<td align="center">Total</td>
							<td align="center"></td>
							<td align="right"><?php echo $total_amount;?></td>
							<td align="right"></td>
							<td align="right"><?php echo $total_comm;?></td>
							<td align="right"><?php echo $total_net;?></td>
							<td align="right"><?php echo $total_taxable;?></td>
							<td align="right"><?php echo $total_cgst;?></td>
							<td align="right"><?php echo $total_sgst;?></td>
							<td align="right"><?php echo $total_igst;?></td>
							<td align="right"><?php echo $total_net;?></td>
						</tr>
					</tbody>
				</table>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>