<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

//$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$from = date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime('-48 HOUR', strtotime($from)));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$ret = '';
$qry = $db->query("SELECT uid FROM apps_user WHERE dist_id = '".$_SESSION['distributor_uid']."' ");
while($rlt = $db->fetchNextObject($qry)) {
	$ret .= $rlt->uid.", ";
}
$ret .= '0';
$sWhere = "WHERE rch.uid IN ($ret) AND rch.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( rch.recharge_id = '".mysql_real_escape_string($_GET['s'])."' OR rch.account_no = '".mysql_real_escape_string($_GET['s'])."' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR opr.operator_name = '".mysql_real_escape_string($_GET['s'])."'  ) ";
}
if(isset($_GET['u']) && $_GET['u'] != '') {
	$sWhere .= " AND rch.uid = '".mysql_real_escape_string($_GET['u'])."' ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id LEFT JOIN apps_user user ON rch.uid = user.uid $sWhere ORDER BY rch.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 20 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-recharge.php');

$meta['title'] = "Recharge";
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
	<div class="container">
		<div class="page-header">
			<div class="page-title">Recharge</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Recharges</h3>
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
								<select name="u" id="u" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM apps_user WHERE status = '1' AND dist_id = '".$_SESSION['distributor_uid']."' ");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->uid;?>" <?php if(isset($_GET['u']) && $_GET['u'] == $result->uid) {?>selected="selected"<?php } ?>><?php echo $result->company_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" size="8" name="s" id="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-basic">
					<thead>
						<tr>
							<th width="1%">S</th>
							<th width="6%">Txn ID</th>
							<th width="16%">Date</th>
							<th width="5%">Mode</th>
							<th>User</th>
							<th>Operator</th>
							<th>Mobile/Acc</th>
							<th width="5%">Amount</th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT rch.*, opr.operator_name, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><?php echo $scnt++;?></td>
							<td><?php echo $result->recharge_id;?></td>
							<td><?php echo $result->request_date;?></td>
							<td><?php echo $result->recharge_mode;?></td>
							<td><?php echo $result->company_name;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->account_no;?></td>
							<td><?php echo round($result->amount,2);?></td>
							<td><?php echo getRechargeStatusLabelUser($result->status);?></td>
							<td><a class="fancyStatus fancybox.ajax" href="../ajax/recharge-status.php?token=<?php echo $token;?>&id=<?php echo $result->recharge_id;?>">
								<i class="fa fa-lg fa-plus-square text-green"></i></a></td>
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