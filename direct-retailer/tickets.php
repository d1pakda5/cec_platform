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

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime('-7 DAYS', strtotime($from)));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = " WHERE submited_by = '".$_SESSION['retailer_uid']."' AND submit_date BETWEEN '".$aFrom."' AND '".$aTo."' ";

if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( title LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['u']) && $_GET['u'] != '') {
	$sWhere .= " AND urgency = '".mysql_real_escape_string($_GET['u'])."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND status = '".mysql_real_escape_string($_GET['status'])."' ";
}
$statement = "tickets $sWhere ORDER BY last_reply_date, submit_date DESC";
//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('tickets.php');

$array['status'] = getTicketStatusList();
$array['urgency'] = getTicketUrgencyList();

$meta['title'] = "Support Tickets";
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
			<div class="page-title">Support <small>/ Tickets</small></div>
			<div class="pull-right">
				<a href="tickets-new.php" class="btn btn-info"><i class="fa fa-plus"></i> New Ticket</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Tickets</h3>
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
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="u" id="u" class="form-control">
									<option value=""></option>
									<?php foreach($array['urgency'] as $key=>$data) { ?>
									<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['u']) && $_GET['u'] == $data['id']) {?>selected="selected"<?php } ?>><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php foreach($array['status'] as $data) { ?>
									<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status']) && $_GET['status'] == $data['id']) {?>selected="selected"<?php } ?>><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th width="6%">S. No.</th>
							<th width="16%">Date</th>
							<th>Subject</th>
							<th width="12%">Urgency</th>
							<th width="12%">Status</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->submit_date;?></td>
							<td>
								<a href="tickets-detail.php?id=<?php echo $result->ticket_id;?>">
								<?php if($result->is_read == '0') {?>
									<b><?php echo $result->title;?></b>
								<?php } else { ?>
									<?php echo $result->title;?>
								<?php } ?>
								</a>
							</td>
							<td><?php echo getTicketUrgency($array['urgency'], $result->urgency);?></td>
							<td><span class="label label-primary"><?php echo getTicketStatus($array['status'], $result->status);?></span></td>
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