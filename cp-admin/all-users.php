<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user.uid!='0' ";

if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= "AND user.user_type='".mysql_real_escape_string($_GET['type'])."' ";
} else {
	$sWhere .= "AND user.user_type IN (1,3,4,5,6) ";
}
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= "AND (user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['deduct']) && $_GET['deduct'] != '') {
	$sWhere .= "AND user.is_deduct='".mysql_real_escape_string($_GET['deduct'])."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= "AND user.status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= "AND user.status!='9' ";
}

if(isset($_GET['balance']) && $_GET['balance'] != '') {
	$order =  "ORDER BY wallet.balance+0 ".mysql_real_escape_string($_GET['balance'])." ";
} else
{
    $order ="ORDER BY user.user_id DESC";
}

$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid $sWhere $order";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 50);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('all-users.php');

$meta['title'] = "Users";
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
function doExcel(){
    var deduct=$("#deduct").val();
	window.location='excel/user.php?deduct='+deduct;
}
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
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control">
									<option value="">Select</option>
									<option value="1"<?php if(isset($_GET['type']) && $_GET['type']=="1") { ?> selected="selected"<?php } ?>>API USER</option>
									<option value="3"<?php if(isset($_GET['type']) && $_GET['type']=="3") { ?> selected="selected"<?php } ?>>MASTER DISTRIBUTOR</option>
									<option value="4"<?php if(isset($_GET['type']) && $_GET['type']=="4") { ?> selected="selected"<?php } ?>>DISTRIBUTOR</option>
									<option value="5"<?php if(isset($_GET['type']) && $_GET['type']=="5") { ?> selected="selected"<?php } ?>>RETAILER</option>
									<option value="6"<?php if(isset($_GET['type']) && $_GET['type']=="6") { ?> selected="selected"<?php } ?>>DIRECT RETAILER</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="status" class="form-control">
									<option value="">Select</option>
									<option value="1" <?php if(isset($_GET['status']) && $_GET['status'] == "1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
									<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == "0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									<option value="9" <?php if(isset($_GET['status']) && $_GET['status'] == "9") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="deduct" id="deduct" class="form-control">
									<option value="">All DDC</option>
									<option value="0" <?php if(isset($_GET['deduct']) && $_GET['deduct'] == "0") { ?> selected="selected"<?php } ?>>DDC</option>
									<option value="1" <?php if(isset($_GET['deduct']) && $_GET['deduct'] == "1") { ?> selected="selected"<?php } ?>>NDDC</option>
									
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="balance" id="balance" class="form-control">
									<option value="">Balance Sort</option>
									<option value="ASC" <?php if(isset($_GET['balance']) && $_GET['balance'] == "ASC") { ?> selected="selected"<?php } ?>>Low To High</option>
									<option value="DESC" <?php if(isset($_GET['balance']) && $_GET['balance'] == "DESC") { ?> selected="selected"<?php } ?>>High To Low</option>
									
								</select>
							</div>
						</div>
						<div class="col-md-2">
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
						<div class="col-md-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
								<button type="button" onclick="doExcel('rptUser')" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="2%">S.</th>
							<th width="8%">Type</th>
							<th width="8%">UID</th>
							<th width="8%">Added Date</th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="5%">Cut</th>
							<th width="10%">Bal.(Rs)</th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
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
							<td><?php echo getUserType($result->user_type);?></td>
							<td><a href="rpt-user-transactions.php?uid=<?php echo $result->uid;?>" title="Transactions"><?php echo $result->uid;?></a></td>
							<td><?php echo $result->added_date;?></td>
							<td><?php echo strtoupper($result->company_name);?></td>
							<td><?php echo $result->mobile;?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							<td style="text-align:center;">
								<a class="sendSms fancybox.ajax" href="send-sms-user.php?id=<?php echo $result->user_id;?>" title="Send SMS">
									<i class="fa fa-envelope text-yellow"></i>
								</a>
							</td>
							<td style="text-align:center;">
								<?php if($result->is_deduct=='0') {?>
								<a href="#" onClick="actionRow('<?php echo $result->user_id;?>', 'nodeduct');" title="NDDC">
									<i class="fa fa-check-square-o text-maroon"></i>
								</a>
								<?php } else { ?>
								<a href="#" onClick="actionRow('<?php echo $result->user_id;?>', 'deduct');" title="DDC">
									<i class="fa fa-check-square-o text-gray"></i>
								</a>
								<?php } ?>
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
								<a href="view-user-profile.php?id=<?php echo $result->user_id;?>" title="details" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
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
							<td colspan="4"></td>
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