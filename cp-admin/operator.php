<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$msg = isset($_GET['msg']) && $_GET['msg']!='' ? mysql_real_escape_string($_GET['msg']) : '';

if(isset($_POST['updateCancel'])) {
	if(isset($_POST['api']) && $_POST['api']!='') {
		$db->query("UPDATE operators SET api_id='".$_POST["api"]."' ");
		header("location:operator.php?msg=success");
		exit();
	}
}

$sWhere = "WHERE opr.operator_id!='' ";
if(isset($_GET['s']) && $_GET['s']!='') {
	$sWhere .= " AND ( opr.operator_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR opr.operator_code LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= " AND opr.service_type='".mysql_real_escape_string($_GET['type'])."' ";
}
if(isset($_GET['api']) && $_GET['api']!='') {
	$sWhere .= " AND opr.api_id='".mysql_real_escape_string($_GET['api'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND opr.status='".mysql_real_escape_string($_GET['status'])."' ";
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
				<div class="box-filter">
					<form method="post" action="#" class="form-horizontal">
						<div class="col-sm-6 pull-right">
							<div class="form-group">
								<label class="col-sm-6 control-label">Select api to shift all operator</label>
								<div class="col-sm-4">
									<select name="api" class="form-control input-sm">
										<option value="">Select API</option>
										<?php
										$query = $db->query("SELECT * FROM api_list WHERE status='1'");
										while($result = $db->fetchNextObject($query)) { ?>
										<option value="<?php echo $result->api_id;?>"><?php echo $result->api_name;?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-2">
									<input type="submit" name="update" value="Save" class="btn btn-sm btn-warning">
								</div>
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
							<th>API Code</th>
							<th>Key</th>
							<th>Service(s)</th>
							<th>Amount</th>
							<th>SurCharge</th>
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
							<td align="center"><?php echo $result->operator_id;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td align="center"><?php echo getBillingType($result->billing_type);?></td>
							<td align="center"><?php echo getCommissionType($result->commission_type=='p');?></td>
							<td><?php echo $result->operator_code;?></td>
							<td><?php echo $result->operator_longcode;?></td>
							<td><?php echo $result->service_name;?></td>	
							<td><?php echo $result->minimum_amount;?> - <?php echo $result->maximum_amount;?></td>
							<td><?php if($result->is_surcharge=='y') { echo $result->surcharge_value." ".getSurchargeType($result->surcharge_type=='p'); }?></td>
							<td><?php echo $result->api_name;?></td>
							<td style="text-align:center;">
								<?php if($result->service_type=='1' || $result->service_type=='2' || $result->service_type=='3') { ?>
								<a href="denomination.php?o=<?php echo $result->operator_id;?>" title="Denominations" class="btn btn-xs btn-default">&nbsp;<i class="fa fa-inr"></i>&nbsp;</a>
								<?php } ?>
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
								<a href="#" onClick="editRow('<?php echo $result->operator_id;?>');" title="Edit" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
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
