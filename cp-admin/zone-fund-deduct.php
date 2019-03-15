<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$date = isset($_GET["date"]) && $_GET["date"] != '' ? mysql_real_escape_string($_GET["date"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($date));
$aTo = date("Y-m-d 23:59:59", strtotime($date));

$utype = isset($_GET["utype"]) && $_GET["utype"]!= '' ? mysql_real_escape_string($_GET["utype"]) : '5';
$hour = isset($_GET["hour"]) && $_GET["hour"]!='' ? mysql_real_escape_string($_GET["hour"]) : '4';

$sWhere = "WHERE rc.status='0' AND rc.request_date BETWEEN '".$aFrom."' AND '".$aTo."' AND us.user_type='".$utype."' ";
$sUids = "";
if(isset($_GET['hour']) && $_GET['hour']!='') {	
	$aTime = date("Y-m-d H:i:s", strtotime('-'.$hour.' HOURS', time()));	
	$qry = $db->query("SELECT txntouid FROM transactions_adm WHERE txndate < '".$aTime."' ");
	while($rlt = $db->fetchNextObject($qry)) {
		$sUids .= $rlt->uid.", ";
	}
	$sUids .= '0';
	if($sUids=='0') {
	
	} else {
		$sWhere .= "AND rc.uid IN ($sUids) ";
	}
} else {
	$sWhere .= "";
}
if(isset($_GET['bal']) && $_GET['bal']!='') {
	$sWhere .= "AND wt.balance > '".mysql_real_escape_string($_GET['bal'])."' ";
}
//Having
$sHaving = "";
if(isset($_GET['tot']) && $_GET['tot']!='') {
	$sHaving .= "HAVING totalAmount > '".mysql_real_escape_string($_GET['tot'])."' ";
}

$statement = "apps_recharge rc LEFT JOIN apps_user us ON rc.uid=us.uid LEFT JOIN apps_wallet wt ON rc.uid=wt.uid $sWhere GROUP BY rc.uid $sHaving ORDER BY totalAmount DESC, rc.request_date ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('zone-fund-deduct.php');

$meta['title'] = "Transaction Reports";
include('header.php');
$adm_array = array();
$query = $db->query("SELECT * FROM apps_admin");
while($row = $db->fetchNextObject($query)) {
	$adm_array[] = array('id'=>$row->admin_id, 'name'=>$row->fullname);
}
function getadmuser($array, $admin_id) {
	$result = $admin_id;
	foreach($array as $key=>$value) {
		if($value['id'] == $admin_id) {
			$result = ucwords($value['name']);
		}
	}
	return $result;
}
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
	jQuery('#date').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#checkAll').click( function() {
		jQuery(".rowSelect").prop('checked', jQuery(this).is(':checked'));
	});
	jQuery('#btnBulk').click(function() {
		if(confirm('Do you want to update bulk complaint?')){
			jQuery.fancybox.showLoading();
			var dt = jQuery('#bulkDeductFrm').serialize();
			jQuery.ajax({
				type: "POST",
				url: "ajax/bulk-deduct.php",
				dataType: 'text',
				data: dt,
				success: function(data){
					jQuery.fancybox(data);
					window.location.reload();
				}
			});
		}
		return false
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Fund Deduct</div>
			<div class="pull-right">
				<a href="admin-dashboard.php" class="btn btn-primary"><i class="fa fa-reply"></i> Dashboard</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Users</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter">
					<form method="get">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="date" id="date" value="<?php if(isset($_GET['date'])) { echo $_GET['date']; }?>" placeholder="Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<select name="hour" id="hour" class="form-control">
											<option value="">Last Deduct</option>
											<option value="1"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='1') { ?> selected="selected"<?php } ?>>1 Hour</option>
											<option value="2"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='2') { ?> selected="selected"<?php } ?>>2 Hour</option>
											<option value="3"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='3') { ?> selected="selected"<?php } ?>>3 Hour</option>
											<option value="4"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='4') { ?> selected="selected"<?php } ?>>4 Hour</option>
											<option value="5"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='5') { ?> selected="selected"<?php } ?>>5 Hour</option>
											<option value="6"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='6') { ?> selected="selected"<?php } ?>>6 Hour</option>
											<option value="7"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='7') { ?> selected="selected"<?php } ?>>7 Hour</option>
											<option value="8"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='8') { ?> selected="selected"<?php } ?>>8 Hour</option>
											<option value="9"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='9') { ?> selected="selected"<?php } ?>>9 Hour</option>
											<option value="10"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='10') { ?> selected="selected"<?php } ?>>10 Hour</option>
											<option value="11"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='11') { ?> selected="selected"<?php } ?>>11 Hour</option>
											<option value="12"<?php if(isset($_GET["hour"]) && $_GET["hour"]=='12') { ?> selected="selected"<?php } ?>>12 Hour</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="utype" id="utype" class="form-control">
									<option value="">Select User</option>
									<option value="1"<?php if(isset($_GET["utype"]) && $_GET["utype"]=='1') { ?> selected="selected"<?php } ?>>API User</option>
									<option value="5"<?php if(isset($_GET["utype"]) && $_GET["utype"]=='5') { ?> selected="selected"<?php } ?>>Retailer</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="tot" id="tot" value="<?php if(isset($_GET['tot'])) { echo $_GET['tot']; }?>" placeholder="Total Recharge Amount" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="text" name="bal" id="bal" value="<?php if(isset($_GET['bal'])) { echo $_GET['bal']; }?>" placeholder="Minimum Balance" class="form-control">
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="show" class="form-control">
									<option value="25">Show</option>
									<option value="25" <?php if(isset($_GET['show'])&&$_GET['show']=="25") { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if(isset($_GET['show'])&&$_GET['show']=="50") { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if(isset($_GET['show'])&&$_GET['show']=="100") { ?> selected="selected"<?php } ?>>100</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-success">
							</div>
						</div>
					</form>
				</div>
				<form id="bulkDeductFrm">
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="1%"><input type="checkbox" name="checkall" id="checkAll" /></th>
							<th width="15%">Date</th>
							<th>User</th>
							<th width="12%">Total Recharge</th>
							<th width="12%">Current Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT rc.*, SUM(IF(rc.status='0', rc.amount, 0)) AS totalAmount, (SELECT balance FROM apps_wallet WHERE uid=rc.uid) AS balance, us.company_name FROM {$statement} LIMIT {$startpoint}, {$limit}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td><input type="checkbox" name="uid" value="<?php echo $row->uid;?>" class="rowSelect" /></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($row->request_date));?></td>
							<td><?php if($row->company_name) { echo strtoupper($row->company_name)." (".$row->uid.")"; } else { echo "Admin";}?></td>
							<td align="right"><?php echo round($row->totalAmount,2);?></td>
							<td align="right"><?php echo round($row->balance,2);?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<div class="box-filter">					
					<div class="col-sm-3">
						<div class="form-group">
							<input type="text" name="amount" id="amount" placeholder="Enter Amount to Deduct" class="form-control">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<input type="submit" value="Deduct" id="btnBulk" class="btn btn-danger">
						</div>
					</div>
				</div>
				</form>
				<!--End of Deduct-->
			</div>
		</div>
	</div>
</div>
<?php include('footer.php');?>