<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");


$meta['title'] = "API's";
include("header.php");
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">API's</div>
			<div class="pull-right">				
				<a href="#" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"> <i class="fa fa-list"></i> List API's</h3>
			</div>
			<div class="box-body no-padding">
				<table class="table">
					<thead>
						<tr>
							<th width="6%">S. No.</th>
							<th>API Name</th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT * FROM api_list ORDER BY api_id ASC ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->api_name;?></td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<a href="#" onClick="actionRow('<?php echo $result->api_id;?>', 'suspend');" title="Active">
										<i class="fa fa-lg fa-check-circle text-green"></i>
									</a>
								<?php }else {?>
									<a href="#" onClick="actionRow('<?php echo $result->api_id;?>', 'activate');" title="Suspend">
										<i class="fa fa-lg fa-minus-circle text-red"></i>
									</a>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="#" onClick="editRow('<?php echo $result->api_id;?>');" title="Edit" class="btn btn-xs btn-primary">Edit</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function editRow(param1, param2, param3) {
	if(param1!="") {
		location.href="api-edit.php?id="+param1;
	}
}
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="api-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script> 
<?php include("footer.php");?>