<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['is_mobile']) || $sP['is_mobile'] != 'y') { 
	include('permission.php');
	exit(); 
}
include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE mob.request_id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( mob.mobile_old = '".mysql_real_escape_string($_GET['s'])."' OR mob.mobile_new = '".mysql_real_escape_string($_GET['s'])."' OR user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
} else {
	$sWhere .= " AND mob.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND mob.request_status = '".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND mob.request_status = '0' ";
}
$statement = "mobile_change_request mob LEFT JOIN apps_user user ON mob.request_user = user.uid $sWhere ORDER BY mob.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('mobile-change-request.php');

$meta['title'] = "Mobile No. Change Request";
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
			<div class="page-title">Mobile No. Change Request</div>
			<div class="pull-right">				
				<a href="mobile-change-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Request</h3>
			</div>			
			<div class="box-body no-padding">
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
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0"){?>selected="selected"<?php } ?>>Pending</option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1"){?>selected="selected"<?php } ?>>Closed</option>
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
							<th width="16%">Request Date</th>
							<th>User</th>
							<th width="12%">Old Number</th>
							<th width="12%">New Number</th>
							<th width="8%"></th>
							<th width="8%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT mob.*, user.uid, user.company_name, user.mobile FROM {$statement} LIMIT {$startpoint}, {$limit}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->request_date;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<td><?php echo $result->mobile_old;?></td>	
							<td><?php echo $result->mobile_new;?></td>
							<td>
								<?php if($result->status == '1') {
										echo "Success";
									} else if ($result->status == '2') {
										echo "Cancelled";
									} else {
										echo "-";
									}?>
							</td>
							<td>
								<?php if($result->request_status == '0') { ?>
								<a href="#" onClick="editRow('<?php echo $result->request_id;?>');" title="Update" class="label label-warning">Pending</a>
								<?php } else { ?>
								<span class="label label-success">Closed</span>
								<?php } ?>
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
<script type="text/javascript">
function editRow(param1, param2, param3) {
	if(param1!="") {
		location.href="mobile-change-details.php?id="+param1;
	}
}
</script> 
<?php include('footer.php'); ?>
