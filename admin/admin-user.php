<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE admin_id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
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
							<th width="3%"></th>
							<th width="3%"></th>
							<th width="3%"></th>
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
								<?php if($result->user_level == 's') {?>
									Super Admin
								<?php } else if($result->user_level == 's') {?>
									Admin
								<?php } else {?>
									User
								<?php } ?>
							</td>
							<td style="text-align:center;">
								<a href="admin-user-permission.php?id=<?php echo $result->admin_id;?>" title="Permission" class="btn btn-xs btn-danger"><i class="fa fa-lock"></i></a>
							</td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<i class="fa fa-lg fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-lg fa-minus-circle text-red"></i>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="admin-user-edit.php?id=<?php echo $result->admin_id;?>" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
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
<?php include('footer.php'); ?>