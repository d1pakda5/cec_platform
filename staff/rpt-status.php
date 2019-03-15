<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['reports']['rechargestatus'])) { 
	include('permission.php');
	exit(); 
}
include("../system/class.pagination.php");
$tbl = new ListTable();

if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere = " WHERE ( recharge.recharge_id = '".mysql_real_escape_string($_GET['s'])."' OR recharge.account_no = '".mysql_real_escape_string($_GET['s'])."' ) ";
} else {
	$sWhere = " WHERE recharge.recharge_id = '' ";
}
$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN apps_user user ON recharge.uid = user.uid $sWhere ORDER BY recharge.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-recharge.php');

$array['recharge_status'] = getRechargeStatusList();

$meta['title'] = "Search Recharge";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery(".fancyDetails").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	jQuery(".fancyAction").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	jQuery(".fancyStatus").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Search Recharge</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Result</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" class="">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Search</label>
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-striped table-condensed-sm">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th width="1%">Mode</th>
							<th width="8%">Date</th>
							<th>Txn No</th>
							<th>User</th>
							<th>Operator</th>
							<th>Mobile</th>
							<th>Amt</th>
							<th>Ref No</th>
							<th>Status</th>
							<th>A</th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->recharge_mode;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($result->request_date));?></td>
							<td><?php echo $result->recharge_id;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->account_no;?></td>	
							<td align="right"><?php echo round($result->amount,2);?></td>
							<td><?php echo $result->operator_ref_no;?></td>
							<td><?php echo getRechargeStatusLabel($array['recharge_status'],$result->status);?></td>
							<td><?php echo $result->api_id;?></td>	
							<td><a class="fancyDetails fancybox.ajax" href="recharge-details.php?id=<?php echo $result->recharge_id;?>">
								<i class="fa fa-lg fa-plus-circle text-green"></i></a></td>
							<td><a class="fancyStatus fancybox.ajax" href="api-recharge-status.php?id=<?php echo $result->recharge_id;?>">
								<i class="fa fa-lg fa-info-circle text-default"></i></a></td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(amount) AS totalRecharge FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="7"><b>Total</b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalRecharge,2);?></b></td>
							<td colspan="5"></td>
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