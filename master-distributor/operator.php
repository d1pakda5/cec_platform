<?php
session_start();
if(!isset($_SESSION['mdistributor'])) header("location:login.php");
include('../config.php');
include('common.php');

$meta['title'] = "Operator";
include('header.php');
?>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">Operators</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Operators</h3>
			</div>			
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>S.NO</th>
							<th>Product Name</th>
							<th>Keyword</th>
							<th>Service</th>
							<th>Min</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php					
						$scnt = 1;
						$query = $db->query("SELECT opr.operator_name, opr.operator_longcode, opr.minimum_amount, opr.maximum_amount, opr.service_type, opr.status, ser.service_name, com.id, com.status AS com_status FROM operators opr LEFT JOIN service_type ser ON opr.service_type = ser.service_type_id LEFT JOIN apps_commission com ON opr.operator_id = com.operator_id AND com.uid = '".$_SESSION['mdistributor_uid']."' ORDER BY opr.service_type, opr.operator_name ASC");
						while($row = $db->fetchNextObject($query)) { ?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $row->operator_name;?></td>
							<td><?php echo $row->operator_longcode;?></td>
							<td><?php echo $row->service_name;?></td>
							<td><?php echo $row->minimum_amount;?> Rs.</td>
						
							<td style="text-align: center;">
								<?php if($row->com_status=='1') {?>
										<i class="fa fa-check-circle text-green"></i>
									<?php }else {?>
										<i class="fa fa-minus-circle text-red"></i>
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
<?php include('footer.php'); ?>