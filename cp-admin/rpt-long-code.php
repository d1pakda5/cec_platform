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

$sWhere = "WHERE id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( message LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
} else {
	$sWhere .= " AND request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['code']) && $_GET['code'] != '') {
	$sWhere .= " AND ( code LIKE '%".mysql_real_escape_string($_GET['code'])."%' )  ";
} 
$statement = "longcode_message $sWhere ORDER BY request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-long-code.php');

$meta['title'] = "Longcode SMS Reports";
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
			<div class="page-title">Reports <small>/ Longcode</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Longcode</h3>
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
						<div class="col-sm-2">
							<div class="form-group">
									<select name="code" id="code" class="form-control">
									<option value=""></option>
								
										<option value="RR" <?php if(isset($_GET['code']) && $_GET['code'] =="RR"){?>selected="selected"<?php } ?>>RR</option>
								
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
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
							<th>GateWay No.</th>
							<th>Code</th>
							<th>User</th>
							<th>Msisdn</th>
							<th width="25%">Message(s)</th>
							<th width="13%">IP</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							if(strlen($result->msisdn) > 10) {
								$username = substr($result->msisdn,2);
							} else {
								$username = $result->msisdn;
							}
							$row = $db->queryUniqueObject("SELECT * FROM apps_user WHERE username = '".$username."'");
						?>
						<tr>
							<td align="center" title="<?php echo $result->id;?>"><?php echo $scnt++;?></td>
							<td><?php echo $result->request_date;?></td>
							<td><?php echo $result->provider;?></td>
							<td><?php echo $result->code;?></td>
							<td><?php echo $row->company_name;?></td>
							<td><?php echo $result->msisdn;?></td>
							<td><?php echo $result->message;?></td>
							<td><?php echo $result->ip;?></td>
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