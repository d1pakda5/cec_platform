<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user.user_type='5' AND wallet.balance BETWEEN '400' AND '500' AND user.status='1' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['muid']) && $_GET['muid'] != '') {
	$sWhere .= " AND user.mdist_id = '".mysql_real_escape_string($_GET['muid'])."' ";
}
if(isset($_GET['duid']) && $_GET['duid'] != '') {
	$sWhere .= " AND user.dist_id = '".mysql_real_escape_string($_GET['duid'])."' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid = wallet.uid $sWhere ORDER BY user.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('retailer-alt.php');

$sArray = "";
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sArray = " AND status IN (0,1) ";
} else {
	$sArray = " AND status != '9' ";
}
if(isset($_GET['muid']) && $_GET['muid'] != '') {
	$sArray = " AND mdist_id = '".mysql_real_escape_string($_GET['muid'])."' ";
}
$arUser1 = array();
$qry1 = $db->query("SELECT * FROM apps_user WHERE user_type = '4' $sArray ORDER BY company_name ASC ");
while($rst1 = $db->fetchNextObject($qry1)) {
	$arUser1[] = array('uid'=>$rst1->uid, 'name'=>$rst1->company_name);
}

$arUser = array();
$qry = $db->query("SELECT * FROM apps_user WHERE user_type = '4' ");
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

if(isset($_POST['bulkaction'])) {
	if(!empty($_POST['uid'])) {
		foreach($_POST['uid'] as $data=>$ids) {
			$db->query("UPDATE apps_user SET status='9' WHERE uid='".$data."'");
			//echo ."<br>";
		}		
	}
	header("location:retailer-alt.php?msg=success&paged=".$paged);
	exit();
}

$meta['title'] = "Retailer";
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
									<option value=""></option>
									<?php foreach($arUser1 as $key=>$data) { ?>
									<option value="<?php echo $data['uid'];?>" <?php if(isset($_GET['duid']) && $_GET['duid'] == $data['uid']) { ?> selected="selected"<?php } ?>><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
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
				<form id="inActiveRetailer" method="post">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="4%">S. No</th>
							<th width="1%"></th>
							<th width="8%">UID</th>
							<th width="1%"></th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="5%">Cut</th>
							<th width="6%">Bal.(Rs)</th>
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
							$trans = $db->queryUniqueObject("SELECT * FROM transactions WHERE account_id='".$result->uid."' ORDER BY transaction_date DESC ");
							if($trans) {
								$trans_done = "Y";
								$trans_amount = $trans->amount;
								$trans_closing = $trans->closing_balance;
								$trans_date = $trans->transaction_date;
							} else {
								$trans_done = "N";
								$trans_amount = "";
								$trans_closing = "";
								$trans_date = "";
							}
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td align="center"><input type="checkbox" name="uid[<?php echo $result->uid;?>]" <?php if($trans_done=='N') {?>checked="checked"<?php } ?> /></td>
							<td><a href="rpt-user-transactions.php?uid=<?php echo $result->uid;?>" title="Transactions"><?php echo $result->uid;?></a></td>
							
							<td><a href="user-login.php?uid=<?php echo $result->uid;?>&token=<?php echo hashToken($result->uid);?>" target="_blank" title="Login"><i class="fa fa-lg fa-unlock-alt text-info"></i></a></td>
							<td><?php echo $result->company_name;?></td>
							<td><?php echo $result->mobile;?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							<td align="right"><?php if($trans_date!='') {echo date("d-m-Y", strtotime($trans_date)); }?></td>
							<td align="right"><?php echo $trans_amount;?></td>
							<td align="right"><?php echo $trans_closing;?></td>
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
							<td colspan="5"></td>
						</tr>
					</tfoot>
				</table>
				<div class="col-xs-12"><input type="submit" name="bulkaction" value="Bulk Action" class="btn btn-default" /></div>
				</form>
			</div>
			
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php'); ?>