<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

if(isset($_POST['apply'])) {
	if(isset($_POST['userids'])) {
		foreach($_POST['userids'] as $data) {
			$db->query("UPDATE apps_user SET assign_id='".$_POST['assign_user']."' WHERE user_id='".$data."' ");
		}
		header("location:assign-manager.php");
		exit();
	}
}
$sWhere = "WHERE user.status='1' AND assign_id='0' ";
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= "AND user.user_type='".mysql_real_escape_string($_GET['type'])."' ";
}
if(isset($_GET['dist']) && $_GET['dist']!='') {
	$sWhere .= "AND user.dist_id='".mysql_real_escape_string($_GET['dist'])."' ";
}
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= "AND (user.fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.company_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.uid LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR user.username LIKE '%".mysql_real_escape_string($_GET['s'])."%') ";
}
if(isset($_GET['u']) && $_GET['u'] != '') {
	$sWhere .= "AND (user.uid LIKE '%".mysql_real_escape_string($_GET['u'])."%') ";
}
$statement = "apps_user user LEFT JOIN apps_wallet wallet ON user.uid=wallet.uid $sWhere ORDER BY user.user_id DESC";

$arUser = array();
$qry = $db->query("SELECT * FROM apps_user WHERE user_type='4' AND status='1' ");
while($rst = $db->fetchNextObject($qry)) {
	$arUser[] = array('uid'=>$rst->uid, 'name'=>$rst->company_name, 'status'=>$rst->status);
}
//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 50);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('assign-manager.php');

$meta['title'] = "Users";
include('header.php');
?>

<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" href="http://select2.github.io/select2//select2-3.5.3/select2.css?ts=2015-08-29T20%3A09%3A48%2B00%3A00">
  <script src="http://select2.github.io/select2/select2-3.5.3/select2.js?ts=2015-08-29T20%3A09%3A48%2B00%3A00"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>

$(document).ready(function() {
    $("#distt").select2();
	$('#checkAll').click( function() {
		$(".itemSelect").prop('checked', $(this).is(':checked'));
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
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
							<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="u" value="<?php if(isset($_GET['u'])) { echo $_GET['u']; }?>" placeholder="UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="typee" class="form-control">
									<option value="">Select Type</option>
									<option value="1" <?php if(isset($_GET['type']) && $_GET['type'] == "1") { ?> selected="selected"<?php } ?>>Api User</option>
									<option value="4" <?php if(isset($_GET['type']) && $_GET['type'] == "4") { ?> selected="selected"<?php } ?>>Distributor</option>
									<option value="5" <?php if(isset($_GET['type']) && $_GET['type'] == "5") { ?> selected="selected"<?php } ?>>Retailor</option>
									<option value="6" <?php if(isset($_GET['type']) && $_GET['type'] == "6") { ?> selected="selected"<?php } ?>>Direct Retailor</option>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<select name="dist" id="distt" class="" style="width: 100%;">
									<option value="">---Select Distributor---</option>
									<?php foreach($arUser as $key=>$data) { ?>
									<option value="<?php echo $data['uid'];?>" <?php if(isset($_GET['dist']) && $_GET['dist']==$data['uid']) { ?> selected="selected"<?php } ?>><?php echo strtoupper($data['name']);?></option>
									<?php } ?>
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
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<form id="frmComplaint" method="post">
				<div class="box-filter padding-20" style="border-top:1px solid #ddd;">
					<div class="col-sm-3">
						<div class="form-group">
							<select name="assign_user" class="form-control input-sm">
								<option value="">Assign To User</option>
								<?php 
								$query = $db->query("SELECT * FROM apps_admin WHERE user_level='a' AND status='1' ");
								while($result = $db->fetchNextObject($query)) { ?>
								<option value="<?php echo $result->admin_id;?>"><?php echo strtoupper($result->fullname);?></option>
								<?php } ?>
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
							<th width="2%">S.</th>
							<th width="2%"><input type="checkbox" id="checkAll" /></th>
							<th width="8%">Type</th>
							<th width="8%">UID</th>
							<th>Name</th>
							<th width="8%">Mobile</th>
							<th width="5%">Cut</th>
							<th width="10%">Bal.(Rs)</th>
							<th width="10%">Distributor</th>
							<th width="1%"></th>
						</tr>
					</thead>
						<tbody>
						<?php						
						$query = $db->query("SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement} LIMIT {$startpoint}, {$limit} ");
					//	echo "SELECT user.*, wallet.balance, wallet.cuttoff FROM {$statement} LIMIT {$startpoint}, {$limit} ";
						
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							$dist_name = "";
							$adms = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$result->dist_id."'");
							if($adms) {
								$dist_name = $adms->company_name;
							}
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td align="center">
								<input type="checkbox" class="itemSelect" name="userids[]" value="<?php echo $result->user_id;?>" />
							</td>
							<td><?php echo getUserType($result->user_type);?></td>
							<td><a href="rpt-user-transactions.php?uid=<?php echo $result->uid;?>" title="Transactions"><?php echo $result->uid;?></a></td>
							<td><?php echo strtoupper($result->company_name);?></td>
							<td><?php echo $result->mobile;?></td>
							<td align="right"><b class="text-primary"><?php echo round($result->cuttoff,2);?></b></td>	
							<td align="right"><b class="text-primary"><?php echo round($result->balance,2);?></b></td>
							<td align="center"><?php echo $dist_name;?></td>							
							<td align="center">
								<?php if($result->status == '1') {?>
									<i class="fa fa-lg fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-lg fa-minus-circle text-red"></i>
								<?php }?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				</form>
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