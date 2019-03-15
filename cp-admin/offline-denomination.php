<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');

if(isset($_POST['yes'])) {
    	$db->execute("UPDATE `offline_denominations` SET  `status` = '1' WHERE id = '1' ");
    }
    if(isset($_POST['no'])) {
    	$db->execute("UPDATE `offline_denominations` SET  `status` = '0' WHERE id = '1' ");
    }

$sWhere = "WHERE deno.service_type_id != '' ";
if(isset($_GET['s']) && $_GET['s'] != '') {
	$sWhere .= " AND ( deno.amount_values LIKE '%".mysql_real_escape_string($_GET['s'])."%' )  ";
}

if(isset($_GET['o']) && $_GET['o'] != '') {
	$sWhere .= " AND deno.service_type_id = '".mysql_real_escape_string($_GET['o'])."' ";
}
if(isset($_GET['user_type']) && $_GET['user_type'] != '') {
	$sWhere .= " AND deno.user_type = '".mysql_real_escape_string($_GET['user_type'])."' ";
}
$statement = "offline_denominations deno LEFT JOIN service_type ser ON deno.service_type_id = ser.service_type_id $sWhere";

$meta['title'] = "Offline Denomination";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Offline Denomination</div>
			&nbsp; &nbsp; &nbsp;
			<div class="pull-right">				
				<a href="offline-denomination-add.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
			</div>&nbsp; &nbsp; &nbsp;
			<div class="pull-right">
				    <?php
				    	
				    $utility_status = $db->queryUniqueValue("SELECT status from offline_denominations where id=1 and service_type_id=0 ");
				    ?>
				    <form id="form1" method="post" action="" class="form-inline">
				    <h4 style="float:left"> &nbsp; &nbsp; &nbsp; Do you want Utilities in Offline Mode? </h4>&nbsp; &nbsp; &nbsp; 
				    <?php 
				    if($utility_status=='0')
				    {?>
				    <input type="submit"  name="yes" value="YES" class="btn btn-danger" />&nbsp; &nbsp; &nbsp;
								
								
				    
				   <?php  }
				    else
				    {?>
				    <input type="submit"  name="no" value="NO" class="btn btn-success" />&nbsp; &nbsp; &nbsp;
				   
				   <?php  }?>
				   </form>
				</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Offline Denomination</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-3">
							<div class="form-group">
								<select name="o" id="o" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT * FROM service_type WHERE status = '1'");
									while($result = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $result->service_type_id;?>" <?php if(isset($_GET['o']) && $_GET['o'] == $result->service_type_id){?>selected="selected"<?php } ?>><?php echo $result->service_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="col-sm-3">
							<div class="form-group">
								<select name="user_type" id="user_type" class="form-control">
									<option value="">--Select User--</option>
									<option value="1" <?php if(isset($_GET['user_type']) && $_GET['user_type'] == "1"){?>selected="selected"<?php } ?>>API User</option>
									<option value="5" <?php if(isset($_GET['user_type']) && $_GET['user_type'] == "5"){?>selected="selected"<?php } ?>>Retailer</option>
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
							<th>Service Name</th>
							<th>User Type</th>
							<th>Amount Range</th>
							<th>Amount(s)</th>
							<th width="4%"></th>
							<th width="8%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT deno.*, deno.status as denostatus ,ser.* FROM {$statement} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->service_name;?></td>
							<td><?php if($result->user_type=='1'){echo "API User";}
							elseif($result->user_type=='5') {echo "Retailer";}
							;?></td>
							<td><?php echo ("".$result->amount_from." - ".$result->amount_to."");?></td>
							<td><?php echo $result->amount_values;?></td>
						
							<td align="center">
								<?php if($result->denostatus == '1') {?>
									<a href="#" onClick="actionRow('<?php echo $result->id;?>', 'suspend');" title="Active">
										<i class="fa fa-lg fa-check-circle text-green"><?php echo  $result->status ?></i>
									</a>
								<?php }
								else if($result->denostatus == '0') {?>
									<a href="#" onClick="actionRow('<?php echo $result->id;?>', 'activate');" title="Suspend">
										<i class="fa fa-lg fa-minus-circle text-red"></i>
									</a>
								<?php }?>
							</td>
							<td style="text-align:center;">
								<a href="#" onClick="editRow('<?php echo $result->id;?>');" title="Edit" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
								<a href="#" onClick="actionRow('<?php echo $result->id;?>', 'delete');" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
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
		location.href="offline-denomination-edit.php?id="+param1;
	}
}
function actionRow(vlu, avlu) {
	if(vlu!="" && avlu!="") {
		var conf = confirm("Are you sure you want to continue");
		if(conf) {
			location.href="offline-denomination-action.php?id="+vlu+"&action="+avlu;
		}
	}
}
</script> 
<?php include('footer.php'); ?>
