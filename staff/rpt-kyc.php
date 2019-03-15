<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("common.php");
include("../system/class.pagination.php");
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));


$sWhere = "WHERE id!='0' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( uid='".mysql_real_escape_string($_GET['s'])."' OR firstname='".mysql_real_escape_string($_GET['s'])."' OR middlename LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR lastname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR fathersname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR mothersname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR phone LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR email LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR city LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR pincode LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR pancard LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR aadhaar LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR gsting LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR adrprooftypeno LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['f']) && $_GET['f']!='' && isset($_GET['t']) && $_GET['t']!='') {
	$sWhere .= " AND submitdate BETWEEN '".$aFrom."' AND '".$aTo."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND uid='".mysql_real_escape_string($_GET['user'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND status='0' ";
}

$statement = "userskyc {$sWhere} ORDER BY submitdate DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-kyc.php');
$meta['title'] = "Users KYC Request";
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
			<div class="page-title">KYC <small>/ Request</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Recharge</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" class="">
						<div class="col-md-3 col-sm-6">
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
						<div class="col-md-3 col-sm-6">
							<div class="form-group">
								<label>Search</label>
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile" class="form-control">
							</div>
						</div>
						<div class="col-md-2 col-sm-4">
							<div class="form-group">
								<label>Status</label>
								<select name="status" class="form-control">
									<option value="">Select</option>
									<option value="0"<?php if(isset($_GET['status']) && $_GET['status']=='0'){?> selected="selected"<?php } ?>>Pending</option>
									<option value="1"<?php if(isset($_GET['status']) && $_GET['status']=='1'){?> selected="selected"<?php } ?>>Verified</option>
									<option value="2"<?php if(isset($_GET['status']) && $_GET['status']=='2'){?> selected="selected"<?php } ?>>Resubmit</option>
								</select>
							</div>
						</div>						
						<div class="col-md-2 col-sm-4">
							<div class="form-group">
								<label>Show</label>
								<select name="show" class="form-control">
									<option value="10" <?php if($limit == '10') { ?> selected="selected"<?php } ?>>10</option>
									<option value="25" <?php if($limit == '25') { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if($limit == '100') { ?> selected="selected"<?php } ?>>100</option>
									<option value="100" <?php if($limit == '250') { ?> selected="selected"<?php } ?>>250</option>
									<option value="100" <?php if($limit == '500') { ?> selected="selected"<?php } ?>>500</option>
								</select>
							</div>
						</div>
						<div class="col-md-2 col-sm-4">
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
							<th>#</th>
							<th>UID</th>
							<th>Name</th>
							<th>Mobile</th>
							<th>Business Name</th>							
							<th>Email</th>
							<th>PAN</th>
							<th>Aadhaar</th>
							<th>City</th>
							<th>Date</th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $row->uid;?></td>
							<td><?php echo $row->firstname;?> <?php echo $row->lastname;?></td>
							<td><?php echo $row->mobile;?></td>
							<td><?php echo $row->businessname;?></td>
							<td><?php echo $row->email;?></td>
							<td><?php echo $row->pancard;?></td>
							<td><?php echo $row->aadhaar;?></td>
							<td><?php echo $row->city;?></td>
							<td><?php echo date("d/m/Y h:i A", strtotime($row->submitdate));?></td>	
							<td><a href="kyc-verification.php?id=<?php echo $row->id;?>" title="Action" class="btn btn-xs btn-default"><i class="fa fa-cog"></i></a></td>
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