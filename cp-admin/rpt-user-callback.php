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

$sWhere = "WHERE log.log_id!='' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( log.url_detail LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
} else {
	$sWhere .= " AND log.added_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['api']) && $_GET['api'] != '') {
	$sWhere .= " AND log.api_id='".mysql_real_escape_string($_GET['api'])."' ";
}

$statement = "reverse_url_log log LEFT JOIN api_list api ON log.api_id=api.api_id $sWhere ORDER BY log.added_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-user-callback.php');
$meta['title'] = "Recharge User Callback Reports";
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
			<div class="page-title">Reports <small>/ Recharge User Callback</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List User Callback</h3>
			</div>			
			<div class="box-body no-padding" style="overflow:auto">
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
						<div class="col-sm-3">
							<div class="form-group">
								<select name="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT api_id,api_name FROM api_list WHERE status='1' ORDER BY api_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->api_id;?>" <?php if(isset($_GET['api']) && $_GET['api']==$result->api_id) {?> selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
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
				<table class="table table-basic">
					<thead>
						<tr>
							<th width="6%">S. No.</th>
							<th width="18%">Date</th>
							<th>User</th>
							<th>API</th>
							<th>Output(s)</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT log.*, api.api_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center" title="<?php echo $result->log_id;?>"><?php echo $scnt++;?></td>
							<td><?php echo $result->added_date;?></td>
							<td><?php echo $result->client_id;?></td>
							<td><?php echo $result->api_name;?></td>
							<td><?php echo $result->url_detail;?></td>
							<td><?php echo $result->status_code;?></td>
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