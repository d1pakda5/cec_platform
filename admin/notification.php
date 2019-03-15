<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " WHERE notification_content LIKE '%".mysql_real_escape_string($_GET['s'])."%' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND status = '".mysql_real_escape_string($_GET['status'])."' ";
}
$statement = "notifications $sWhere ORDER BY notification_date DESC";
//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 50 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('notification.php');

$meta['title'] = "Notifications";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Notifications</div>
			<div class="pull-right">				
				<a href="notification-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Notifications</h3>
			</div>			
			<div class="box-body no-padding">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning">
							</div>
						</div>
					</form>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>S. No.</th>
							<th width="45%">Notification</th>
							<th>Type</th>
							<th>User Type</th>
							<th>Date</th>
							<th></th>
							<th width="7%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit}");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>							
							<td><?php if($result->ctype=='t') { echo $result->notification_content; } else { echo "Image File........"; }?></td>
							<td><?php if($result->ntype=='s') { echo "Scroll";} else { echo "Pop Up";}?></td>
							<td><?php echo getUserType($result->user_type);?></td>
							<td><?php echo date("d-m-Y H:i:s", strtotime($result->notification_date));?></td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<a href="#" onClick="actionRow('<?php echo $result->notification_id;?>', 'suspend');" title="Active">
										<i class="fa fa-lg fa-check-circle text-green"></i>
									</a>
								<?php }else {?>
									<a href="#" onClick="actionRow('<?php echo $result->notification_id;?>', 'activate');" title="Suspend">
										<i class="fa fa-lg fa-minus-circle text-red"></i>
									</a>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="#" onClick="editRow('<?php echo $result->notification_id;?>');" title="Edit" class="btn btn-xs btn-edit"><i class="fa fa-pencil"></i></a>
								<a href="#" onClick="actionRow('<?php echo $result->notification_id;?>', 'delete');" title="Edit" class="btn btn-xs btn-delete"><i class="fa fa-trash"></i></a>
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
function editRow(param1, param2, param3) {
	if(param1!="") {
		location.href="notification-edit.php?id="+param1;
	}
}
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="notification-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script> 
<?php include('footer.php'); ?>
