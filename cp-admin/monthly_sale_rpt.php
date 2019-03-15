<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$month = $_POST['month'];
$year = $_POST['year'];

$meta['title'] = "Monthly Sale Report";
include('header.php');

?>
<script>
jQuery(document).ready(function() {
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
});
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/transaction.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function report(ele)
{
	var admin_id = jQuery(ele).attr('admin_id');
	var month = jQuery(ele).attr('month');
	var year = jQuery(ele).attr('year');
	window.open('monthly_sale_rpt_manager.php?admin_id='+admin_id+'&month='+month+'&year='+year+'&type=4');
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Monthly Sale</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> Monthly Sale By Manager</h3>
			</div>			
			<div class="box-body no-padding min-height-480" style="min-height: 559px;">
			
			<div class="grid-view" style="height: 400px;margin-top: 20px;">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Sr #</th>
						<th>Account Manager</th>
						<th>Target</th>
						<th>Achievement</th>
						<th>Achievement %</th>	
						<th>Balance</th>
						<th>View Details</th>	
					</tr>
				</thead>

				<tbody>
				<?php

				$leads=$db->query("SELECT * from apps_admin where user_level='a' and status='1'");
				$target_total = 0;
				$achievement_total = 0;
				$balance_total = 0;
				$final_target_total=0;
				$final_achievement_total=0;
				$final_balance_total=0;
				$j=0;
				while($row = $db->fetchNextObject($leads)) {
					//print_r($row);
					
					$admin_id=$row->admin_id;
					$date=$year.'-'.$month;
				
					$target=$db->queryUniqueValue("select target_value from monthly_sale_target where acc_manager_id=".$admin_id." and dist_ret_id='-1' and month=".$month." and year=".$year);
					$target=($target=="")?0:$target;				
					 $achievement=$db->queryUniqueValue("select sum(sale_amount) from daily_sale_entries where account_manager_id=".$admin_id." and sale_date LIKE '%".$date."%' and emp_uid not in('20008944','20031521','20024794','20026870','20013532','20025430')");
					$achievement=($achievement!="")?$achievement:0;
					$balance=$target-$achievement;

					$calculate_target=($target==0?1:$target);
					$achievement_percent=number_format(($achievement/$calculate_target)*100, 2, '.', '');
					echo "<tr>";
					$manager_name=$row->fullname;
					echo '<td>'.(++$j).'</td>';
					echo '<td>'.$manager_name.'</td>';
					echo '<td>'.($target).'</td>';
					echo '<td>'.($achievement).'</td>';
					echo '<td>'.$achievement_percent.'</td>';
					echo '<td>'.($balance).'</td>';
			    	echo '<td><button type="button" admin_id="'.$admin_id.'" month="'.$month.'" year="'.$year.'" onclick="report(this)" class="btn btn-info"><i class="fa fa-book"></i> View Details</button></td>';
					echo "</tr>";
					$target_total += $target;
					$achievement_total += $achievement;
					$balance_total += $balance;
			}		
				$final_target_total = $target_total;
				$final_achievement_total = $achievement_total;
				$final_balance_total = $balance_total;
				

				$calculate_target=($final_target_total==0?1:$final_target_total);
				$achievement_percent=number_format(($final_achievement_total/$calculate_target)*100, 2, '.', '');
				echo "<tr>";
				echo '<td style="font-weight:bold;background:#b3b3b3;"></td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;">All Manager Total</td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;">'.($final_target_total).'</td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;">'.($final_achievement_total).'</td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;">'.$achievement_percent.'</td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;">'.($final_balance_total).'</td>';
				echo '<td style="font-weight:bold;background:#b3b3b3;"></td>';
				echo "</tr>";
				
				?>
				</tbody>
			</table>
		</div>

			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php');?>