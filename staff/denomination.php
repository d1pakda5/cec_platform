<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(empty($sP['operator']['denom'])) { 
	include('permission.php');
	exit(); 
}
if(isset($_POST['update'])) {
	$db->query("UPDATE operators SET api_id='".$_POST["api"]."'");
}

$sWhere = "WHERE deno.operator_id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( opr.operator_name LIKE '%".mysql_real_escape_string($_GET['s'])."%' OR deno.amount_values LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}
if(isset($_GET['a']) && $_GET['a'] != '') {
	$sWhere .= " AND deno.api_id = '".mysql_real_escape_string($_GET['a'])."' ";
}
if(isset($_GET['o']) && $_GET['o'] != '') {
	$sWhere .= " AND deno.operator_id = '".mysql_real_escape_string($_GET['o'])."' ";
}
$statement = "operators_denominations deno LEFT JOIN operators opr ON deno.operator_id = opr.operator_id LEFT JOIN api_list api ON deno.api_id = api.api_id $sWhere ORDER BY opr.service_type, opr.operator_name ASC";

$meta['title'] = "Operator Denomination";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Operator Denomination</div>
			<div class="pull-right">				
				<a href="denomination-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Operator Denomination</h3>
			</div>			
			<div class="box-body no-padding">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-3">
							<div class="form-group">
								<select name="o" id="o" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM operators WHERE status = '1' ORDER BY service_type,operator_name ASC ");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->operator_id;?>" <?php if(isset($_GET['o']) && $_GET['o'] == $result->operator_id){?>selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<select name="a" id="a" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM api_list WHERE status = '1' ORDER BY api_name ASC ");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->api_id;?>" <?php if(isset($_GET['a']) && $_GET['a'] == $result->api_id){?>selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
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
							<th width="6%">S. No.</th>
							<th>Operator Name</th>
							<th>Amount(s)</th>
							<th width="20%">API</th>
							<th width="4%"></th>
							<th width="8%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT deno.*, opr.operator_name, api.api_name FROM {$statement} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->operator_name;?></td>
							<td><?php echo $result->amount_values;?></td>
							<td><?php echo $result->api_name;?></td>
							<td align="center">
								<?php if($result->status == '1') {?>
									<a href="#" onClick="actionRow('<?php echo $result->denomination_id;?>', 'suspend');" title="Active">
										<i class="fa fa-lg fa-check-circle text-green"></i>
									</a>
								<?php }else {?>
									<a href="#" onClick="actionRow('<?php echo $result->denomination_id;?>', 'activate');" title="Suspend">
										<i class="fa fa-lg fa-minus-circle text-red"></i>
									</a>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="#" onClick="editRow('<?php echo $result->denomination_id;?>');" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
								<a href="#" onClick="actionRow('<?php echo $result->denomination_id;?>', 'delete');" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
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
		location.href="denomination-edit.php?id="+param1;
	}
}
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="denomination-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script> 
<?php include('footer.php'); ?>
