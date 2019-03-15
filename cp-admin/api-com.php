<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE user_type='1' AND status='1' ";
$statement = "apps_user $sWhere ORDER BY user_id ASC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"]!='' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('api-com.php');

$meta['title'] = "Distributor";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Distributor</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Distributor</h3>
			</div>			
			<div class="box-body no-padding min-height-480">				
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="3%">S.No</th>
							<th width="5%">UID</th>
							<th>Name</th>
							<th width="15%">Mobile</th>
							<th width="5%"></th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
						<?php						
						$query = $db->query("SELECT * FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
							$display = true;
							$com = $db->queryUniqueObject("SELECT * FROM usercommissions WHERE uid='".$result->uid."' AND operator_id='1' ");
							if($com) {
								$display = false;
							}
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><b><?php echo $result->uid;?></b></td>
							<td><?php echo ucwords($result->company_name);?></td>
							<td><?php echo $result->mobile;?></td>
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
								<?php if($display) { ?>
								<a href="updatecoms.php?uid=<?php echo $result->uid;?>" title="Commission" class="btn btn-xs btn-success"><i class="fa fa-shield"></i></a>
								<?php } ?>
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