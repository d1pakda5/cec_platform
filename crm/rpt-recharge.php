<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$tbl = new ListTable();

$from = date("Y-m-d");
$to = date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));


$sWhere = "WHERE user.assign_id='".$_SESSION['accmgr']."' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND (recharge.recharge_id='".mysql_real_escape_string($_GET['s'])."' OR recharge.account_no='".mysql_real_escape_string($_GET['s'])."') ";
} else {
	$sWhere .= " AND recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['opr']) && $_GET['opr']!='') {
	$sWhere .= " AND recharge.operator_id='".mysql_real_escape_string($_GET['opr'])."' ";
}
if(isset($_GET['user']) && $_GET['user']!='') {
	$sWhere .= " AND recharge.uid='".mysql_real_escape_string($_GET['user'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND recharge.status='".mysql_real_escape_string($_GET['status'])."' ";
}
if(isset($_GET['mode']) && $_GET['mode']!='') {
	$sWhere .= " AND recharge.recharge_mode='".mysql_real_escape_string($_GET['mode'])."' ";
}

$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid $sWhere ORDER BY recharge.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-recharge.php');

$array['recharge_status'] = getRechargeStatusList();

$meta['title'] = "Recharge";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
// 	jQuery('#from').datepicker({
// 		format: 'yyyy-mm-dd', 
	
	
// 	});
// 	jQuery('#to').datepicker({
// 		format: 'yyyy-mm-dd'
// 	});
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
			<div class="page-title">Reports <small>/ Recharge</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Recharge</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>From</label>
										<input type="text" size="8" name="f" id="from" value="<?php echo date('Y-m-d')?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>To</label>
										<input type="text" size="8" name="t" id="to" value="<?php echo date('Y-m-d')?>" placeholder="To Date" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Operator</label>
								<select name="opr" id="opr" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT operator_id,operator_name FROM operators ORDER BY service_type,operator_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->operator_id;?>" <?php if(isset($_GET['opr']) && $_GET['opr']==$result->operator_id) {?> selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status</label>
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge_status'] as $data) { ?>
										<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status']) && $_GET['status'] == $data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Mode</label>
								<select name="mode" id="mode" class="form-control">
									<option value=""></option>
									<option value="API" <?php if(isset($_GET['mode']) && $_GET['mode'] == "API"){?>selected="selected"<?php } ?>>API</option>
									<option value="WEB" <?php if(isset($_GET['mode']) && $_GET['mode'] == "WEB"){?>selected="selected"<?php } ?>>WEB</option>
									<option value="SMS" <?php if(isset($_GET['mode']) && $_GET['mode'] == "SMS"){?>selected="selected"<?php } ?>>SMS</option>
									<option value="GPRS" <?php if(isset($_GET['mode']) && $_GET['mode'] == "GPRS"){?>selected="selected"<?php } ?>>GPRS</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>User UID</label>
								<input type="text" size="8" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label>Search</label>
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Show</label>
								<select name="show" class="form-control">
									<option value="10" <?php if($limit == '10') { ?> selected="selected"<?php } ?>>10</option>
									<option value="25" <?php if($limit == '25') { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
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
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT recharge.recharge_id, recharge.uid, recharge.recharge_mode, recharge.api_id, recharge.service_type, recharge.operator_id, recharge.account_no, recharge.amount, recharge.surcharge, recharge.status, recharge.request_date, recharge.operator_ref_no, opr.operator_name, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
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
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(amount) AS totalRecharge FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="7"><b>Total</b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalRecharge,2);?></b></td>
							<td colspan="2"></td>
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