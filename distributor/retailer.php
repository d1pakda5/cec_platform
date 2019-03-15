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

$sWhere = "WHERE user.user_type = '5' AND dist_id = '".$_SESSION['distributor_uid']."' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND user.status = '".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND user.status != '9' ";
}
if(isset($_GET['duid']) && $_GET['duid'] != '') {
	$sWhere .= " AND user.dist_id = '".mysql_real_escape_string($_GET['duid'])."' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 25);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('retailer.php');

$meta['title'] = "Retailer";
include('header.php');
?>
<div class="content">
	<div class="container">
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
			<div class="box-body min-height-480">
				<div class="box-filter no-padding">
					<form method="get">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<select name="status" class="form-control">
										<option value="">Status</option>
										<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
										<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<select name="show" class="form-control">
										<option value="">Show</option>
										<option value="10" <?php if(isset($_GET['show'])&&$_GET['show']=="10") { ?> selected="selected"<?php } ?>>10</option>
										<option value="25" <?php if(isset($_GET['show'])&&$_GET['show']=="25") { ?> selected="selected"<?php } ?>>25</option>
										<option value="50" <?php if(isset($_GET['show'])&&$_GET['show']=="50") { ?> selected="selected"<?php } ?>>50</option>
										<option value="100" <?php if(isset($_GET['show'])&&$_GET['show']=="100") { ?> selected="selected"<?php } ?>>100</option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<div class="form-group">
									<input type="submit" value="Filter" class="btn btn-warning">
								</div>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="3%">S.No</th>
							<th width="8%">UID</th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="5%">Cut</th>
							<th width="6%">Bal.(Rs)</th>
							<th width="3%"></th>
							<th width="3%"></th>
							<th width="3%"></th>
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
							<td><?php echo $result->uid;?></td>
							<td><?php echo $result->company_name;?></td>
							<td><?php echo $result->mobile;?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							</td>
							<td align="center">
								<?php if($result->is_access == 'y') {?>
									<i class="fa fa-unlock text-green"></i>
								<?php }else {?>
									<i class="fa fa-lock text-red"></i>
								<?php }?>
							</td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<i class="fa fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-minus-circle text-red"></i>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="view-user-profile.php?id=<?php echo $result->user_id;?>" title="details" class="btn btn-xs btn-inverse"><i class="fa fa-id-card"></i></a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT user.uid, wallet.balance, SUM(balance) AS walletBalance FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="4"><b>Total Balance</b></td>
							<td align="right"></td>
							<td align="right"><b class="text-red"><?php echo round($row->walletBalance,2);?></b></td>
							<td colspan="3"></td>
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