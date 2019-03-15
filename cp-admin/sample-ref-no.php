<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");

$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;


if(isset($_POST['submit'])) {
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['admin']."' ");
	if($admin_info) {
		if($admin_info->pin==$pin) {
			for($i = 1; $i<=count($_POST['operator_id']); $i++) {
			
						
				
				$exists = $db->queryUniqueObject("SELECT operator_id FROM operators WHERE operator_id='".mysql_real_escape_string($_POST['operator_id'][$i])."'");
				if($exists) {
					
				
						$db->execute("UPDATE operators SET sample_ref_no='".mysql_real_escape_string($_POST['ref_no'][$i])."' WHERE operator_id='".$_POST['operator_id'][$i]."'");
					
					
				} else {
					
					
						$db->execute("INSERT INTO operators (sample_ref_no) values('".mysql_real_escape_string($_POST['ref_no'][$i])."') WHERE operator_id='".$_POST['operator_id'][$i]."'");
								
				}
			}
			
			$error = 3;
			 
		} else {
			$error = 2;
		}
	}
	else {
		$error = 1;		
	}
	
	header("location:sample-ref-no.php?error=".$error);
	exit();
}





$meta['title'] = "Transaction Settings";
include("header.php");
?>
<script type = "text/javascript">

</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
				
		<?php if($error == 3) { ?>
		<div class="alert alert-success" role="alert">
			Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==2) { ?>
		<div class="alert alert-warning" role="alert">
			Oops, Invalid Pin!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==1) { ?>
		<div class="alert alert-danger" role="alert">
			Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }	?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Set Transaction no.</h3>
					</div>
					<div class="box-body no-padding min-height-480">
						<form id="form1" method="post" action="" class="form-inline">
						
						<table class="table table-condensed table-bordered">
							<tr>
								<th width="6%">S. No.</th>
								<th>Operator Name</th>
								<th width="50%">Number</th>
							</tr>
						
							<?php
							$i = 0;		
							$usercommid = "";												
							$query = $db->query("SELECT * FROM operators WHERE service_type in('1','2') ORDER BY service_type ASC ");
							while($result = $db->fetchNextObject($query)) {
								$ref_info = $db->queryUniqueObject("SELECT * FROM operators WHERE operator_id='".$result->operator_id."' ");
								if($ref_info) {
									$operator_id = $ref_info->id;
									$ref_no = $ref_info->sample_ref_no;
									
								} else {
									$operator_id = $ref_info->id;
									$ref_no = $ref_info->sample_ref_no;;
								}
							 ?>
							<tr id="<?php echo $i++;?>">
								<td align="center">
									<?php echo $i;?>
									<input type="hidden" name="operator_id[<?php echo $i;?>]" value="<?php echo $result->operator_id;?>" />
								
								</td>
								<td><?php echo $result->operator_name;?></td>
							    <td>
									<input type="text" size="30" id="ref_no<?php echo $i;?>" name="ref_no[<?php echo $i;?>]" value="<?php echo $ref_no;?>" />
								</td>
																
							</tr>
							<?php } ?>
							<tr>
								<td colspan='100%'>&nbsp;</td>
							</tr>
							<tr>
								<td colspan="1"></td>
								<td colspan="1"><input type="text" name="pin" id="pin" size="8" placeholder="PIN" class="form-control" /></td>
								<td colspan="4">
									<input type="submit" name="submit" value="Update" class="btn btn-success" />
									<input type="reset" name="reset" value="Reset" class="btn btn-default" />
								</td>
							</tr>
						</table>
						</form>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>
<script type="text/javascript">
function actionRow(id, action) {
	if(id!="" && action!="") {
		var conf=confirm("Are you sure you want to "+action+" this service/operator");
		if(conf) {
			location.href="users-operators-status.php?id="+id+"&action="+action;
		}
	}
}
</script> 
<?php include("footer.php");?>