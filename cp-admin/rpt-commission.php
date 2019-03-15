<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:00", strtotime($to));

$sWhere = "WHERE recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' AND tran.transaction_term='RECHARGE' AND tran.type='dr' ";
//  $sWhere = "WHERE recharge.recharge_id!=''";
if(isset($_GET['s']) && $_GET['s']!='') {
	$aStr = mysql_real_escape_string($_GET['s']);
	$sWhere .= " AND (recharge.recharge_id='".$aStr."' OR recharge.account_no='".$aStr."' OR recharge.operator_ref_no='".$aStr."' OR recharge.api_txn_no='".$aStr."' OR user.company_name LIKE '%".$aStr."%') ";
}
else
{
    $sWhere .= " AND recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}

if(isset($_GET["f"]) && $_GET["f"]!='') {
    $sWhere .= " AND recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}

if(isset($_GET['opr']) && $_GET['opr']!='') {
	$sWhere .= " AND recharge.operator_id='".mysql_real_escape_string($_GET['opr'])."' ";
}
if(isset($_GET['api']) && $_GET['api']!='') {
	$sWhere .= " AND recharge.api_id='".mysql_real_escape_string($_GET['api'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND recharge.uid='".mysql_real_escape_string($_GET['uid'])."' ";
}



$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.org_operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid LEFT JOIN transactions tran ON recharge.recharge_id=tran.transaction_ref_no LEFT JOIN commission_details com ON recharge.recharge_id=com.recharge_id $sWhere ORDER BY recharge.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-commission.php');

$array['recharge_status'] = getRechargeStatusList();

$meta['title'] = "Commission";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />

<script>
jQuery(document).ready(function() {
    
    // alert($(".click_it").attr("val"));
});


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
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var opr = jQuery('#opr').val();
	var api = jQuery('#api').val();
	var status = jQuery('#status').val();
	var mode = jQuery('#mode').val();
	var uid = jQuery('#uid').val();
	var api_complaint = jQuery('#api_complaint').val();
	var op_ref = jQuery('#op_ref').val();
	window.location='excel/recharge.php?from='+from+'&to='+to+'&opr='+opr+'&api='+api+'&status='+status+'&mode='+mode+'&uid='+uid+'&api_complaint='+api_complaint+'&op_ref='+op_ref;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Commission</small></div>
		</div>
	
		<?php if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> API Complaint Closed Successfully.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Something Went Wrong!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Recharge Commission</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>From</label>
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>To</label>
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
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
								<label>API</label>
								<select name="api" id="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT api_id,api_name FROM api_list WHERE status = '1' ORDER BY api_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->api_id;?>" <?php if(isset($_GET['api']) && $_GET['api']==$result->api_id) {?> selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					
						<div class="col-sm-2">
							<div class="form-group">
								<label>User UID</label>
								<input type="text" size="8" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Search</label>
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile/Name" class="form-control">
							</div>
						</div>
						
					
						<div class="col-sm-2">
							<div class="form-group">
								<label>Show</label>
								<select name="show" class="form-control">
									<option value="10" <?php if($limit == '10') { ?> selected="selected"<?php } ?>>10</option>
									<option value="25" <?php if($limit == '25') { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if($limit == '100') { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if($limit == '250') { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if($limit == '500') { ?> selected="selected"<?php } ?>>500</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								<input type="submit" value="Filter" class="btn btn-warning">
								<button type="button" onclick="doExcel('rptRecharge')" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-striped table-responsive table-bordered table-condensed-sm">
					<thead>
						<tr>
							<th width="5%">S.</th>
							<th width="9%">Date</th>
							<th width="10%">Txn No</th>
							<th width="27%">User</th>
							<th width="10%">Operator</th>
							<th width="10%">Mobile</th>
							<th width="10%">Amt</th>
							<th width="10%">Commision</th>
							<th width="10%">Recharge Amt</th>
						</tr>
					</thead>
					<tbody>
						<?php
						
						$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name, tran.amount as tran_amount,com.amount as com_amount, com.added_date,com.closing_balance FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($result->request_date));?></td>
							<td><?php echo $result->recharge_id;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<?php
							$opr_info = $db->queryUniqueObject("SELECT operator_name FROM operators where operator_id='".$result->operator_id."' ");
							?>
							 <td><?php echo $opr_info->operator_name;?></td>
						
							
							<td><?php echo $result->account_no;?></td>	
							<td align="center"><?php echo round($result->amount,2);?></td>
							<td align="center"><?php echo round($result->com_amount,2);?></td>
							<td align="center"><?php echo round($result->tran_amount,2);?></td>
							
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php 
						$totalrecharge = $db->queryUniqueValue("SELECT SUM(recharge.amount) AS totalRecharge FROM apps_recharge recharge  LEFT JOIN transactions tran ON recharge.recharge_id=tran.transaction_ref_no LEFT JOIN apps_user user ON recharge.uid=user.uid  $sWhere");
						$totalcommission = $db->queryUniqueValue("SELECT SUM(com.amount) AS totalcommission FROM $statement");
						$totaltransamount = $db->queryUniqueValue("SELECT SUM(tran.amount) AS totalRecharge FROM apps_recharge recharge  LEFT JOIN transactions tran ON recharge.recharge_id=tran.transaction_ref_no LEFT JOIN apps_user user ON recharge.uid=user.uid  $sWhere");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="6"><b>Total</b></td>
							<td align="right"><b class="text-red"><?php echo round($totalrecharge,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($totalcommission,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($totaltransamount,2);?></b></td>
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