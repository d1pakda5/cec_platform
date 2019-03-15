<?php
session_start();
if(!isset($_SESSION['apiuser'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
include('../system/class.pagination.php');
$tbl = new ListTable();
$nFrom = date("Y-m-d", strtotime('-168 HOUR'));
$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
if($from >= $nFrom) {
	$aFrom = $from;
} else {
	$aFrom = $nFrom;
}
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE rch.uid = '".$_SESSION['apiuser_uid']."' AND rch.request_date LIKE '%".$aFrom."%' and tran.transaction_term='RECHARGE' and tran.type='dr' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( rch.recharge_id = '".mysql_real_escape_string($_GET['s'])."' OR rch.account_no = '".mysql_real_escape_string($_GET['s'])."' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR opr.operator_name = '".mysql_real_escape_string($_GET['s'])."'  ) ";
}
$statement = "apps_recharge rch LEFT JOIN operators opr ON rch.operator_id = opr.operator_id LEFT JOIN apps_user user ON rch.uid = user.uid LEFT JOIN transactions tran ON rch.recharge_id = tran.transaction_ref_no  $sWhere ORDER BY rch.request_date DESC";


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
function doExcel(uid){
  
	var from = jQuery('#from').val();
// 	var to = jQuery('#to').val();
	window.location='rpt-recharge-excel.php?from='+from;
  
   
}
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
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder=" Date" class="form-control">
									</div>
								</div>
							<!--	<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php //if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
									</div>
								</div>-->
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<input type="text" size="8" name="s" id="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
								<button type="button" onclick="doExcel(<?php echo $_SESSION['apiuser_uid'];?>)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-basic">
					<thead>
						<tr>
							<th width="5%">S.No.</th>
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
						$query = $db->query("SELECT rch.*,tran.closing_balance, opr.operator_name, user.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
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
							<td align="right"><?php echo round($result->amount,2);?></td>
							
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