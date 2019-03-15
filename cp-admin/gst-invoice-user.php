<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

if(isset($_POST['apply'])) {
	if(isset($_POST['userids'])) {
		foreach($_POST['userids'] as $data) {
			$db->query("UPDATE gst_invoice_user SET gst_invoice='".$_POST['isinvoice']."' WHERE user_id='".$data."' ");
		}
		header("location:gst-invoice-user.php");
		exit();
	}
}
//////
$sWhere = "WHERE uid!='0' ";
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= "AND user_type='".mysql_real_escape_string($_GET['type'])."' ";
} else {
	$sWhere .= "AND user_type IN (1,4,5) ";
}
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= "AND (fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= "AND status='".mysql_real_escape_string($_GET['status'])."' ";
}
if(isset($_GET['state']) && $_GET['state'] != '') {
	$sWhere .= "AND states='".mysql_real_escape_string($_GET['state'])."' ";
}
if(isset($_GET['inv']) && $_GET['inv'] != '') {
	$sWhere .= "AND gst_invoice='".mysql_real_escape_string($_GET['inv'])."' ";
}
$statement = "gst_invoice_user $sWhere ORDER BY user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 50);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('gst-invoice-user.php');

$meta['title'] = "Users";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
$(document).ready(function() {
	$(".sendSms").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	$("#checkall").click( function() {
		$(".checkitems").prop("checked", $(this).is(":checked"));
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Users <small>/ All</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Users</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control input-sm">
									<option value="">Select</option>
									<option value="1"<?php if(isset($_GET['type']) && $_GET['type']=="1") { ?> selected="selected"<?php } ?>>API USER</option>
									<option value="4"<?php if(isset($_GET['type']) && $_GET['type']=="4") { ?> selected="selected"<?php } ?>>DISTRIBUTOR</option>
									<option value="5"<?php if(isset($_GET['type']) && $_GET['type']=="5") { ?> selected="selected"<?php } ?>>RETAILER</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="state" class="form-control input-sm">
									<option value="">Select</option>
									<?php
									$query = $db->query("SELECT * FROM states ORDER BY states ASC ");
									while($row = $db->fetchNextObject($query)) {?>
									<option value="<?php echo $row->states;?>"<?php if(isset($_GET['state']) && $_GET['state']==$row->states) { ?> selected="selected"<?php } ?>><?php echo $row->states;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="inv" class="form-control input-sm">
									<option value="">GST</option>
									<option value="0" <?php if(isset($_GET['inv']) && $_GET['inv'] == "0") { ?> selected="selected"<?php } ?>>No</option>
									<option value="1" <?php if(isset($_GET['inv']) && $_GET['inv'] == "1") { ?> selected="selected"<?php } ?>>Yes</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="status" class="form-control input-sm">
									<option value="">ALL</option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									<option value="9" <?php if(isset($_GET['status']) && $_GET['status'] == "9") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="show" class="form-control input-sm">
									<option value="">Show</option>
									<option value="50" <?php if(isset($_GET['show'])&&$_GET['show']=="50") { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if(isset($_GET['show'])&&$_GET['show']=="100") { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if(isset($_GET['show'])&&$_GET['show']=="250") { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if(isset($_GET['show'])&&$_GET['show']=="500") { ?> selected="selected"<?php } ?>>500</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning btn-sm">
							</div>
						</div>
					</form>
				</div>
				<form id="frmComplaint" method="post">
				<div class="box-filter padding-20" style="border-top:1px solid #ddd;">
					<div class="col-sm-1">
						GST Invoice
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<select name="isinvoice" class="form-control input-sm">
								<option value="">--Select--</option>
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<input type="submit" name="apply" id="btn-apply" value="Apply" class="btn btn-sm btn-info">
						</div>
					</div>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th>S. No</th>
							<th width="2%"><input type="checkbox" id="checkall"></th>
							<th>Type</th>
							<th>UID</th>
							<th>Name</th>
							<th>Mobile</th>
							<th>State</th>
							<th>GST Deduct</th>
							<th>GST Number</th>
							<th>GST Invoice</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php						
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><input type="checkbox" name="userids[]" class="checkitems" value="<?php echo $result->user_id;?>"></td>
							<td><?php echo getUserType($result->user_type);?></td>
							<td><?php echo $result->uid;?></td>
							<td><?php echo strtoupper($result->company_name);?></td>
							<td><?php echo $result->mobile;?></td>
							<td><?php echo $result->states;?></td>
							<td><?php echo getTaxStatus($result->gst_deduct);?></td>
							<td><?php echo $result->gstin;?></td>
							<td><?php echo getTaxStatus($result->gst_invoice);?></td>
							<td align="center">
								<?php if($result->status=='1') {?>
									<span class="label label-success">active</label>
								<?php }elseif($result->status=='9') {?>
									<span class="label label-danger">trash</label>
								<?php }else {?>
									<span class="label label-danger">inactive</label>
								<?php }?>
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
<script type="text/javascript">
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf=confirm("Are you sure you want to continue");
		if(conf) {
			location.href="users-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script>
<?php include('footer.php'); ?>