<?php
session_start();
include('config.php');
$msg = isset($_GET['msg']) && $_GET['msg']!='' ? mysql_real_escape_string($_GET['msg']) : '';
$sWhere = "WHERE opr.operator_id!='' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND ( opr.operator_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR opr.operator_code LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}
if(isset($_GET['stype']) && $_GET['stype']!='') {
	$sWhere .= " AND opr.service_type='".mysql_real_escape_string($_GET['stype'])."' ";
}
if(isset($_GET['bill']) && $_GET['bill']!='') {
	$sWhere .= " AND opr.billing_type='".mysql_real_escape_string($_GET['bill'])."' ";
}
if(isset($_GET['group']) && $_GET['group']!='') {
	$sWhere .= " AND opr.item_group='".mysql_real_escape_string($_GET['group'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND opr.status='".mysql_real_escape_string($_GET['status'])."' ";
}
$statement = "operators opr LEFT JOIN service_type type ON opr.service_type=type.service_type_id $sWhere ORDER BY opr.service_type, opr.operator_name ASC";

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
		<?php if($msg=='success') { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Operators</h3>
			</div>			
			<div class="box-body no-padding">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-1">
							<div class="form-group">
								<select name="stype" id="stype" class="form-control input-sm">
									<option value="">Type</option>
									<option value="1">Prepaid</option>
									<option value="2">DTH</option>
									<option value="3">DataCard</option>
									<option value="4">Postpaid</option>
									<option value="5">Landline</option>
									<option value="6">Electricity</option>
									<option value="7">Gas</option>
									<option value="8">Insurance</option>
									<option value="9">DMT</option>
									<option value="10">Products</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="bill" id="bill" class="form-control input-sm">
									<option value="">Invoice</option>
									<option value="1">P2P</option>
									<option value="2">P2A</option>
									<option value="3">Surcharge</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="group" id="group" class="form-control input-sm">
									<option value="">Group</option>
									<option value="1">E-Recharge Value Prepaid</option>
									<option value="2">E-Recharge Value DTH</option>
									<option value="3">E-Recharge Commission</option>
									<option value="4">Surcharge on Bill Collection</option>
									<option value="5">Surcharge on DMT</option>
								</select>
							</div>
						</div>	
						<div class="col-sm-2">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning btn-sm">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>ID</th>
							<th>Operator Name</th>
							<th>Bill Type</th>
							<th>Com</th>
							<th>Service(s)</th>
							<th>Amount</th>
							<th>SurCharge</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT opr.*, type.service_name FROM {$statement} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td align="center"><?php echo $result->operator_id;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td align="center"><?php echo getBillingType($result->billing_type);?></td>
							<td align="center"><?php echo getCommissionType($result->commission_type=='p');?></td>
							<td><?php echo $result->service_name;?></td>	
							<td><?php echo $result->minimum_amount;?> - <?php echo $result->maximum_amount;?></td>
							<td><?php if($result->is_surcharge=='y') { echo $result->surcharge_value." ".getSurchargeType($result->surcharge_type=='p'); }?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>