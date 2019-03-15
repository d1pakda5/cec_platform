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

$sWhere = "WHERE comp.complaint_date BETWEEN '".$aFrom."' AND '".$aTo."' ";

if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( comp.title LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND comp.status = '".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND comp.status = '0' ";
}
$statement = "complaints comp LEFT JOIN apps_recharge rch ON comp.txn_no = rch.recharge_id LEFT JOIN operators opr ON rch.operator_id = opr.operator_id LEFT JOIN api_list api ON rch.api_id = api.api_id $sWhere ORDER BY comp.complaint_date DESC";
//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 5 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('complaints.php');

$array['status'] = getComplaintStatusList();
$array['recharge'] = getRechargeStatusList();

$meta['title'] = "Complaints";
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
	jQuery('#checkAll').click( function() {
		jQuery(".itemSelect").prop('checked', jQuery(this).is(':checked'));
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Complaints</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Complaints</h3>
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
						<div class="col-sm-3">
							<div class="form-group">
								<select name="o" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT operator_id,operator_name FROM operators ORDER BY service_type,operator_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->operator_id;?>" <?php if(isset($_GET['o']) && $_GET['o']==$result->operator_id) {?> selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="rs" id="rs" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge'] as $key=>$data) { ?>
									<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['rs']) && $_GET['rs'] == $data['id']) {?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php foreach($array['status'] as $data) { ?>
									<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status']) && $_GET['status'] == $data['id']) {?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
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
				<form action="complaint-action.php" method="post" id="mainForm">
				<div class="box-filter row">
					<div class="col-sm-12">
						<div class="col-sm-3">
							<div class="form-group">
								<select name="action" id="action" class="form-control">
									<option value="">BULK ACTION</option>
									<option value=""></option>
									<option value="0">SUCCESS</option>
									<option value="2">FAILED</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Submit" class="btn btn-primary">
							</div>
						</div>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th width="1%">
								<input type="checkbox" id="checkAll" />
							</th>
							<th>Date</th>
							<th>Txn No</th>
							<th width="10%">Operator</th>
							<th width="10%">Mobile/Account</th>
							<th width="5%">Amount</th>
							<th>Ref No</th>
							<th width="1%">Status</th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT comp.*, rch.recharge_id, rch.account_no, rch.amount, rch.status as rch_status, rch.operator_ref_no, opr.operator_name FROM {$statement} LIMIT {$startpoint}, {$limit}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center">
								<input type="checkbox" class="itemSelect" name="items[]" value="<?php echo $result->txn_no;?>" />
							</td>
							<td><?php echo $result->complaint_date;?></td>
							<td><?php echo $result->txn_no;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->account_no;?></td>	
							<td><?php echo round($result->amount);?></td>
							<td><?php echo $result->operator_ref_no;?></td>
							<td><?php echo getRechargeStatusLabel($array['recharge'], $result->rch_status);?></td>	
							<td><a class="fancyDetails fancybox.ajax btn btn-xs btn-success" href="recharge-details.php?id=<?php echo $result->recharge_id;?>">
								<i class="fa fa-lg fa-plus"></i></a></td>
							<td style="text-align:center;">
								<a class="fancyDetails fancybox.ajax btn btn-xs btn-primary" href="x.php?id=<?php echo $result->complaint_id;?>" title="Complaint Details"><i class="fa fa-cube"></i></a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				</form>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
