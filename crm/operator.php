<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
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
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Operators</h3>
			</div>			
			<div class="box-body no-padding">
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th>S. No.</th>
							<th>Operator Name</th>
							<th>Operator Code</th>
							<th>Service Type(s)</th>
							<th>Amount</th>
							<th>SurCharge</th>
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
