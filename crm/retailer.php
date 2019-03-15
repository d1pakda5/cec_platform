<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user.user_type='5' AND assign_id='".$_SESSION['accmgr']."' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND (user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.aadhaar LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.panno LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.gstin LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND user.status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND user.status!='9' ";
}
if(isset($_GET['dist']) && $_GET['dist']!='') {
	$sWhere .= " AND user.dist_id='".mysql_real_escape_string($_GET['dist'])."' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid $sWhere ORDER BY user.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('retailer.php');

$sArray = "";
if(isset($_GET['status']) && $_GET['status']!='') {
	$sArray = " AND status IN (0,1) ";
} else {
	$sArray = " AND status!='9' ";
}
$arUser1 = array();
$qry1 = $db->query("SELECT * FROM apps_user WHERE user_type='4' AND assign_id='".$_SESSION['accmgr']."' ORDER BY company_name ASC ");
while($rst1 = $db->fetchNextObject($qry1)) {
	$arUser1[] = array('uid'=>$rst1->uid, 'name'=>$rst1->company_name);
}

$arUser = array();
$qry = $db->query("SELECT * FROM apps_user WHERE user_type='4' AND assign_id='".$_SESSION['accmgr']."' ");
while($rst = $db->fetchNextObject($qry)) {
	$arUser[] = array('uid'=>$rst->uid, 'name'=>$rst->company_name, 'status'=>$rst->status);
}

function distName($arUser, $id) {
	$result = "";
	foreach($arUser as $key=>$data) {
		if($data['uid'] == $id) {
			$result = $data['name'];
		}
	}
	return $result;
}

$meta['title'] = "Retailer";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Retailer</div>
			<div class="pull-right">				
				<a href="retailer-add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Retailer</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<input type="hidden" name="muid" value="<?php if(isset($_GET['muid'])) { echo $_GET['muid']; }?>">
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<select name="duid" class="form-control">
									<option value="">---Select Distributor---</option>
									<?php foreach($arUser1 as $key=>$data) { ?>
									<option value="<?php echo $data['uid'];?>" <?php if(isset($_GET['duid']) && $_GET['duid'] == $data['uid']) { ?> selected="selected"<?php } ?>><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" class="form-control">
									<option value="">---Select Status---</option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									<option value="9" <?php if(isset($_GET['status']) && $_GET['status'] == "9") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="show" class="form-control">
									<option value="">Show</option>
									<option value="50" <?php if(isset($_GET['show'])&&$_GET['show']=="50") { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if(isset($_GET['show'])&&$_GET['show']=="100") { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if(isset($_GET['show'])&&$_GET['show']=="250") { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if(isset($_GET['show'])&&$_GET['show']=="500") { ?> selected="selected"<?php } ?>>500</option>
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
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="2%">S.No</th>
							<th width="5%">UID</th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="1%">TDS</th>
							<th width="1%">GST</th>
							<th width="5%">Cut</th>
							<th width="6%">Bal.(Rs)</th>
							<th>Distributor</th>
						
						</tr>
					</thead>
					<tbody>
						<?php						
						$query = $db->query("SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><b><?php echo $result->uid;?></b></td>
							<td><?php echo ucwords($result->company_name);?></td>
							<td><?php echo $result->mobile;?></td>
							<td><?php echo getTaxStatus($result->tds_deduct);?></td>
							<td><?php echo getTaxStatus($result->gst_deduct);?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							<td><?php echo ucwords(distName($arUser, $result->dist_id));?></td>
						
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS walletBalance FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="6"></td>
							<td align="right"><b class="text-primary"><?php echo round($row->cuttoffBalance,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->walletBalance,2);?></b></td>
							<td colspan="6"></td>
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