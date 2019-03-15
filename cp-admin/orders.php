<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$tbl = new ListTable();
$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime('-30 DAYS', strtotime($from)));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE odr.order_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND (odr.recharge_id='".mysql_real_escape_string($_GET['s'])."' OR odr.customer_mobile='".mysql_real_escape_string($_GET['s'])."' OR odr.customer_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR odr.customer_email LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['product']) && $_GET['product'] != '') {
	$sWhere .= " AND odr.product_id='".mysql_real_escape_string($_GET['product'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid'] != '') {
	$sWhere .= " AND odr.agent_uid='".mysql_real_escape_string($_GET['uid'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND odr.order_status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND odr.order_status='pending' ";
}
//
$statement = "orders odr LEFT JOIN products pro ON odr.product_id=pro.id LEFT JOIN apps_user usr ON odr.agent_uid=usr.uid $sWhere ORDER BY odr.order_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('orders.php');

$meta['title'] = "Orders";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
$(document).ready(function() {
	$('#from, #to').datepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Orders</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Orders</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" class="">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control input-sm">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control input-sm">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<select name="product" id="product" class="form-control input-sm">
									<option value="">Products </option>
									<?php
									$query = $db->query("SELECT * FROM products WHERE status='1' ORDER BY product_name ASC");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->id;?>"<?php if(isset($_GET['product']) && $_GET['product']==$result->id) {?> selected="selected"<?php } ?>><?php echo $result->product_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="status" class="form-control input-sm">
									<option value="">Status</option>
									<option value="pending"<?php if(isset($_GET['status']) && $_GET['status']=='pending') {?> selected="selected"<?php } ?>>Pending</option>
									<option value="sumitted"<?php if(isset($_GET['status']) && $_GET['status']=='submitted') {?> selected="selected"<?php } ?>>Submitted</option>
									<option value="completed"<?php if(isset($_GET['status']) && $_GET['status']=='completed') {?> selected="selected"<?php } ?>>Completed</option>
									<option value="refunded"<?php if(isset($_GET['status']) && $_GET['status']=='refunded') {?> selected="selected"<?php } ?>>Refunded</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" size="8" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="UID" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-success btn-sm">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th>Date</th>
							<th>Txn No</th>
							<th>User</th>
							<th>Product</th>
							<th>Amount</th>
							<th>Status</th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT odr.*, pro.product_name, usr.company_name FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo date("d/m/Y H:i", strtotime($result->order_date));?></td>
							<td><?php echo $result->recharge_id;?></td>
							<td><?php echo $result->company_name;?> (<?php echo $result->agent_uid;?>)</td>
							<td><?php echo $result->product_name;?></td>
							<td><?php echo $result->order_amount;?></td>
							<td><?php echo $result->order_status;?></td>
							<td style="text-align:center;">
								<a href="order-detail.php?id=<?php echo $result->id;?>" class="btn btn-xs btn-success"><i class="fa fa-expand"></i></a>
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
<?php include('footer.php'); ?>