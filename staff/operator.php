<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['operator']['opr'])) { 
	include('permission.php');
	exit(); 
}
if(isset($_POST['updateCancel'])) {
	$db->query("UPDATE operators SET api_id='".$_POST["api"]."'");
}

$sWhere = "WHERE opr.operator_id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( opr.operator_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR opr.operator_code LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}
if(isset($_GET['type']) && $_GET['type'] != '') {
	$sWhere .= " AND opr.service_type = '".mysql_real_escape_string($_GET['type'])."' ";
}
if(isset($_GET['api']) && $_GET['api'] != '') {
	$sWhere .= " AND opr.api_id = '".mysql_real_escape_string($_GET['api'])."' ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= " AND opr.status = '".mysql_real_escape_string($_GET['status'])."' ";
}
$statement = "operators opr LEFT JOIN service_type type ON opr.service_type = type.service_type_id LEFT JOIN api_list api ON opr.api_id = api.api_id $sWhere ORDER BY opr.service_type, opr.operator_name ASC";

$meta['title'] = "Operator";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Operators</div>
			<div class="pull-right">				
				<a href="operator-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Operators</h3>
			</div>			
			<div class="box-body no-padding">
				<div class="box-filter padding-20">
					<form method="post" action="operator.php">
					<div class="pull-right">
						<div class="col-sm-8">
							<div class="form-group">								
								<select name="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM api_list WHERE status = '1'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->api_id;?>"><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<input type="submit" name="update" value="Save" class="btn btn-warning">
							</div>
						</div>
					</div>
					</form>
				</div>
				<table class="table table-basic">
					<thead>
						<tr>
							<th>S. No.</th>
							<th>Operator Name</th>
							<th>Operator Code</th>
							<th>Service Type(s)</th>
							<th>Amount</th>
							<th>Charge</th>
							<th>API</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT opr.*, type.service_name, api.api_name FROM {$statement} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->operator_code;?></td>
							<td><?php echo $result->service_name;?></td>	
							<td><?php echo $result->minimum_amount;?> - <?php echo $result->maximum_amount;?></td>
							<td><?php if($result->surcharge == 'y') { echo $result->surcharge_amount; }?></td>
							<td><?php echo $result->api_name;?></td>
							<td style="text-align:center;">
								<a href="denomination.php?o=<?php echo $result->operator_id;?>" title="Denominations" class="btn btn-xs btn-info">DENO</a>
							</td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<a href="#" onClick="actionRow('<?php echo $result->operator_id;?>', 'suspend');" title="Active">
										<i class="fa fa-lg fa-check-circle text-green"></i>
									</a>
								<?php }else {?>
									<a href="#" onClick="actionRow('<?php echo $result->operator_id;?>', 'activate');" title="Suspend">
										<i class="fa fa-lg fa-minus-circle text-red"></i>
									</a>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="#" onClick="editRow('<?php echo $result->operator_id;?>');" title="Edit" class="btn btn-xs btn-primary">Edit</a>
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
		location.href="operator-edit.php?id="+param1;
	}
}
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="operator-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script> 
<?php include('footer.php'); ?>
