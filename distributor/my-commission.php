<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');

$meta['title'] = "Operator";
include('header.php');
?>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Commission</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Commission</h3>
			</div>			
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead>
						<tr>
							<th width="8%">S.NO</th>
							<th>Product Name</th>
							<th>Service Name</th>
							<th width="5%">Commission</th>
							<th width="5%">Retailer</th>
							<th width="15%">Surcharge</th>
						</tr>
					</thead>
					<tbody>
						<?php					
						$scnt = 1;
						$query = $db->query("SELECT opr.operator_name, opr.service_type, ser.service_name, com.comm_dist, com.comm_ret, com.is_surcharge, com.is_percentage, com.surcharge_value FROM operators opr LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id LEFT JOIN usercommissions com ON opr.operator_id=com.operator_id AND com.uid='".$_SESSION['distributor_uid']."' ORDER BY opr.service_type, opr.operator_name ASC");
						while($row = $db->fetchNextObject($query)) { ?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $row->operator_name;?></td>
							<td><?php echo $row->service_name;?></td>
							<td><?php echo $row->comm_dist;?></td>
							<td><?php echo $row->comm_ret;?></td>
							<td>
								<?php if($row->is_surcharge=='y') { ?>
									<?php echo $row->surcharge_value;?> <?php if($row->is_percentage=='y') { echo "%";}?>
								<?php } ?>
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