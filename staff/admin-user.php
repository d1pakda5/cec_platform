<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE admin_id!='' and user_level='a' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND ( fullname LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR email LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR mobile LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR username LIKE '%".mysql_real_escape_string($_GET['s'])."%' ) ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND status = '".mysql_real_escape_string($_GET['status'])."' ";
}
$statement = "apps_admin $sWhere ORDER BY admin_id ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 50);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('admin-user.php');

$meta['title'] = "Admin Users";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Admin Users</div>
			<div class="pull-right">				
				<a href="admin-user-add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Admin Users</h3>
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
							<th>Name</th>
							<th width="12%">Username</th>
							<th width="12%">Mobile</th>
							<th>Email</th>
							<th>Type</th>
							<th>Money Transfer</th>
							
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->fullname;?></td>
							<td><?php echo $result->username;?></td>
							<td><?php echo $result->mobile;?></td>
							<td><?php echo $result->email;?></td>
							<td>
								<?php if($result->user_level=='s') {?>
									Super Admin
								<?php } else if($result->user_level=='a') {?>
									Account Manager
								<?php } else if($result->user_level=='u') {?>
									User
								<?php } else {?>
									NA
								<?php } ?>
							</td>
							<td style="text-align:center;">
								<?php if($result->user_level=='a') {
								if($result->is_money=='a') {?>
								<a href="#" onClick="actionRow('<?php echo $result->admin_id;?>', 'moneyinactive');" title="Money Transfer Active">
									<i class="fa fa-check-square-o text-green"></i>
								</a>
								<?php } else { ?>
								<a href="#" onClick="actionRow('<?php echo $result->admin_id;?>', 'moneyactive');" title="Money Transfer InActive">
									<i class="fa fa-check-square-o text-red"></i>
								</a>
								<?php }} ?>
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
<script>
    function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf=confirm("Are you sure you want to continue");
		if(conf) {
			location.href="user-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script>
<?php include('footer.php'); ?>