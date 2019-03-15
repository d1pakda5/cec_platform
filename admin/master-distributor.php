<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user_type = '3' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND user.status = '".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND user.status != '9' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 50);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('master-distributor.php');

$meta['title'] = "Master Distributor";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
	jQuery(".sendSms").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Master Distributor</div>
			<div class="pull-right">				
				<a href="master-distributor-add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Master Distributor</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="status" class="form-control">
									<option value=""></option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									<option value="9" <?php if(isset($_GET['status']) && $_GET['status'] == "9") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
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
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-basic">
					<thead>
						<tr>
							<th width="6%">S. No</th>
							<th width="8%">UID</th>
							<th width="1%"></th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="8%">Cut. (Rs)</th>
							<th width="10%">Bal. (Rs)</th>
							<th width="3%">D</th>
							<th width="3%">R</th>
							<th width="3%"></th>
							<th width="3%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
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
							<td>
								<a href="rpt-user-transactions.php?uid=<?php echo $result->uid;?>" title="Transactions">
									<?php echo $result->uid;?>
								</a>
							</td>
							<td>
								<a href="user-login.php?uid=<?php echo $result->uid;?>&token=<?php echo hashToken($result->uid);?>" target="_blank" data-toggle="tooltip" title="Login">
									<i class="fa fa-lg fa-unlock-alt text-info"></i>
								</a>
							</td>
							<td><?php echo $result->company_name;?></td>
							<td><?php echo $result->mobile;?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							<td style="text-align:center;">
								<a href="distributor.php?muid=<?php echo $result->uid;?>" title="Distributor">
									<i class="fa fa-user text-fuchsia"></i>
								</a>
							</td>
							<td style="text-align:center;">
								<a href="retailer.php?muid=<?php echo $result->uid;?>" title="Retailer">
									<i class="fa fa-user text-teal"></i>
								</a>
							</td>
							<td style="text-align:center;">
								<a href="user-commission.php?uid=<?php echo $result->uid;?>&token=<?php echo hashToken($result->uid);?>" title="Commission" class="btn btn-xs btn-success">
									<i class="fa fa-lg fa-shield"></i>
								</a>
							</td>
							<td style="text-align:center;">
								<a href="user-whitelabel.php?id=<?php echo $result->user_id;?>" title="details" class="btn btn-xs btn-info">
									<i class="fa fa-lg fa-cog"></i>
								</a>
							</td>
							<td style="text-align:center;">
								<a class="sendSms fancybox.ajax" href="send-sms-user.php?id=<?php echo $result->user_id;?>" title="Send SMS">
									<i class="fa fa-envelope text-yellow"></i>
								</a>
							</td>
							<td align="center">
								<?php if($result->is_access == 'y') {?>
									<i class="fa fa-lg fa-unlock text-green"></i>
								<?php }else {?>
									<i class="fa fa-lg fa-lock text-red"></i>
								<?php }?>
							</td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<i class="fa fa-lg fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-lg fa-minus-circle text-red"></i>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="view-user-profile.php?id=<?php echo $result->user_id;?>" title="details" class="btn btn-xs btn-primary">
									<i class="fa fa-eye"></i>
								</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT user.uid, wallet.balance, wallet.cuttoff, SUM(cuttoff) AS cuttoffBalance, SUM(balance) AS walletBalance FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="5"><b>Total Balance</b></td>
							<td align="right"><b class="text-primary"><?php echo round($row->cuttoffBalance,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->walletBalance,2);?></b></td>
							<td colspan="8"></td>
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