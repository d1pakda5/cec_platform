<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['ds_user']['view'])) { 
	include('permission.php');
	exit(); 
}
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user.user_type='4' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND (user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.aadhaar LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.panno LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.gstin LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND user.status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= " AND user.status!='9' ";
}
if(isset($_GET['muid']) && $_GET['muid'] != '') {
	$sWhere .= " AND user.mdist_id='".mysql_real_escape_string($_GET['muid'])."' ";
}
if(isset($_GET['admin_id']) && $_GET['admin_id'] != '') {
	$sWhere .= " AND user.assign_id='".mysql_real_escape_string($_GET['admin_id'])."' ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid $sWhere ORDER BY user.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"]!='' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('distributor.php');

if(isset($_GET['status']) && $_GET['status']!='') {
	$sArray = " AND status IN (0,1) ";
} else {
	$sArray = " AND status!='9' ";
}
$arUser1 = array();
$qry1 = $db->query("SELECT * FROM apps_admin WHERE user_level='a' and status='1' $sArray ");
while($rst1 = $db->fetchNextObject($qry1)) {
	$arUser1[] = array('admin_id'=>$rst1->admin_id, 'name'=>$rst1->fullname);
}


$arUser = array();
$qry = $db->query("SELECT * FROM apps_user WHERE user_type='3' ");
while($rst = $db->fetchNextObject($qry)) {
	$arUser[] = array('uid'=>$rst->uid, 'name'=>$rst->company_name, 'status'=>$rst->status);
}
function mDistName($arUser, $id) {
	$result = "";
	foreach($arUser as $key=>$data) {
		if($data['uid'] == $id) {
			$result = $data['name'];
		}
	}
	return $result;
}

$meta['title'] = "Distributor";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Distributor</div>
			<div class="pull-right">				
				<a href="distributor-add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Distributor</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
							<div class="col-sm-4">
							<div class="form-group">
								<select name="admin_id" class="form-control">
									<option value="">---Select Account Manager---</option>
									<?php foreach($arUser1 as $key=>$data) { ?>
									<option value="<?php echo $data['admin_id'];?>" <?php if(isset($_GET['admin_id']) && $_GET['admin_id'] == $data['admin_id']) { ?> selected="selected"<?php } ?>><?php echo $data['name'];?></option>
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
							<th width="3%">S.No</th>
							<th width="5%">UID</th>
							<th>Name</th>
							<th width="5%">Mobile</th>
							<th width="1%">TDS</th>
							<th width="1%">GST</th>
							<th width="7%">Cut. (Rs)</th>
							<th width="7%">Bal. (Rs)</th>
							<th>Account Manager</th>
							<th width="1%">R</th>
							<th width="1%">KYC</th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%">Txns</th>
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
							<td><?php
							$row = $db->queryUniqueObject("SELECT fullname FROM apps_admin WHERE admin_id =(SELECT assign_id from apps_user where uid= '".$result->uid."')");
							if($row) {		
                                		echo $row->fullname;
                                	  } else {
                                		echo " ";
                                	  }
							?></td>
							<td>
								<a href="retailer.php?duid=<?php echo $result->uid;?>" title="Retailer" class="btn btn-xs btn-default"><i class="fa fa-user"></i></a>
							</td>
							<td style="text-align:center;">
								<?php if($result->is_kyc=='1'){?>
								<a href="kyc.php?uid=<?php echo $result->uid;?>" title="KYC ACCEPTED" class="btn btn-xs btn-success"><i class="fa fa-file-text-o"></i></a>
								<?php } else if($result->is_kyc=='2') { ?>
								<a href="#<?php echo $result->uid;?>" title="KYC REJECTED" class="btn btn-xs btn-warning"><i class="fa fa-file-text-o"></i></a>
								<?php }  else { ?>
								<a href="#<?php echo $result->uid;?>" title="KYC NOT SUBMITTED" class="btn btn-xs btn-danger"><i class="fa fa-file-text-o"></i></a>
								<?php } ?>
							</td>						
							<td style="text-align:center;">
								<a href="usercommissions.php?uid=<?php echo $result->uid;?>&token=<?php echo hashToken($result->uid);?>" title="Commission" class="btn btn-xs btn-success"><i class="fa fa-shield"></i></a>
							</td>
							<td align="center">
								<span class="btn btn-xs btn-default">
								<?php if($result->is_access=='y') {?>
									<i class="fa fa-unlock text-green"></i>
								<?php }else {?>
									<i class="fa fa-lock text-red"></i>
								<?php }?>
								</span>
							</td>
							<td align="center">								
								<span class="btn btn-xs btn-default">
								<?php if($result->status=='1') {?>
									<i class="fa fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-minus-circle text-red"></i>
								<?php }?>
								</span>
							</td>
							<td style="text-align:center;">
								<a href="user-profile.php?id=<?php echo $result->user_id;?>" title="details" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
							</td>
							<td style="text-align:center;">
								<a href="rpt-user-transactions.php?uid=<?php echo $result->uid;?>" title="User Trasnsaction Report" class="btn btn-xs btn-default"><i class="fa fa-line-chart"></i></a>
							</td>
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
							<td colspan="7"></td>
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