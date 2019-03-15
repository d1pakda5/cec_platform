<?php
session_start();
if(!isset($_SESSION['accmgr'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$month = $_POST['month'];
$year = $_POST['year'];
$admin_id=$_SESSION['accmgr'];

$meta['title'] = "Monthly Sale Report By Manager";
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
	window.location='monthly_sale_rpt_manager.php?admin_id='+admin_id+'&month='+month+'&year='+year;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/tableHeadFixer.js"></script>
<?php
	$manager=$db->queryUniqueObject("SELECT * from apps_admin where user_level='a' and status='1' and admin_id='".$admin_id."'");

	$target=$db->queryUniqueValue("select target_value from monthly_sale_target where acc_manager_id=".$admin_id." and month=".$month." and year=".$year);

	$target=($target=="")?0:$target;
	$date=$year.'-'.$month;

	 $achievement=$db->queryUniqueValue("select sum(sale_amount) from daily_sale_entries where account_manager_id=".$admin_id." and sale_date LIKE '%".$date."%' and emp_uid not in('20008944','20031521','20024794','20026870','20013532','20025430')");

	$achievement=($achievement!="")?$achievement:0;
	$balance=$target-$achievement;

	$calculate_target=($target==0?1:$target);
	$achievement_percent=number_format(($achievement/$calculate_target)*100, 2, '.', '');


?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Monthly Sale</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> Monthly Sale Report By Manager</h3>
			</div>			
			<div class="box-body no-padding >
				<div class="col-md-12" id="top">
	<div class="span6 col-md-6">
		<table class="table table-bordered">
			<tr>
				<th>Manager Name</th>
				<td><?php echo $manager->fullname; ?></td>
			</tr>
			<tr>
				<th>Month/Year</th>
				<td><?php echo  date("F / Y", strtotime($year."-".$month."-01"));?></td>
			</tr>
			<tr>
				<th>Target</th>
				<td><?php echo $target;?></td>
			</tr>
		</table>
	</div>
	<div class="span6 col-md-6">
		<table class="table table-bordered">
			<tr>
				<th>Achievement</th>
				<td><?php echo $achievement;?></td>
			</tr>
			<tr>
				<th>Achievement %</th>
				<td><?php echo $achievement_percent;?></td>
			</tr>
			<tr>
				<th>Balance</th>
				<td><?php echo $balance;?></td>
			</tr>
		</table>
	</div>
</div>
</div>



<?php
$last_day=date("t", strtotime($year."-".$month."-01"));

$myRetailors=$db->query("SELECT * from apps_user where status='1'and user_type!='3' and assign_id='".$admin_id."'");
?>

<div class="span12" style="margin-left: 0px;margin-top: 20px;">
<div class="grid-view" style="height: 400px;">
	<table id="report" class="table table-bordered table-stripped">
		<thead>
			<tr>
				<th style="vertical-align: middle;" rowspan="2">Sr #</th>
				<th style="vertical-align: middle;" rowspan="2">API User</th>
				<th style="vertical-align: middle;" rowspan="2">Distributor</th>
				<th style="vertical-align: middle;" rowspan="2">Retailor</th>
				<th style="vertical-align: middle;" rowspan="2">Target</th>
				<th style="vertical-align: middle;" rowspan="2">Total</th>
				<th style="vertical-align: middle;" rowspan="2">Balance</th>	
				<?php
				for($i=1;$i<=$last_day;$i++)
				{
					echo '<th style="text-align:center;">'.$i.'</th>';	
				}
				?>
			</tr>
			<tr>
				<?php
				for($i=1;$i<=$last_day;$i++)
				{
					$day_name=date("D", strtotime($year."-".$month."-".$i));

					echo '<th>'.$day_name.'</th>';	
				}
				?>
			</tr>
		</thead>

		<tbody>
			<?php
			$target_total=0;
			$total_total=0;
			$balance_total=0;
			$day_total=array();
			for($j=0;$j<$last_day;$j++)
			{
				$day_total[]=0;
			}
			$k=0;

			while($result = $db->fetchNextObject($myRetailors)) 
			{
				$data=$myRetailors[$i];

				$target=$db->queryUniqueValue("select target_value from monthly_sale_target where dist_ret_id=".$result->uid." and month=".$month." and year=".$year);

				$target=($target!=""?$target:0);

				echo '<tr>';
				echo '<td>'.(++$k).'</th>';
				if($result->user_type=="1"){
					echo '<td>'.$result->company_name.'</th>';
				}
				else{
					echo '<td></td>';

				}
				if($result->user_type=="4"){
					echo '<td>'.$result->company_name.'</th>';
				}
				else{
					echo '<td></th>';

				}
				if($result->user_type=="5"){
					echo '<td>'.$result->company_name.'</th>';
				}
				else{
					echo '<td></th>';

				}
				echo '<td>'.$target.'</td>';
				$row=array();	
				for($j=1;$j<=$last_day;$j++)
				{
					$dte=date("Y-m-d", strtotime($year."-".$month."-".$j));
					$sql="select sum(sale_amount) as sale_amount from daily_sale_entries where account_manager_id=".$admin_id." and sale_date='".$dte."' and emp_uid=".$result->uid;
					$resultDay=$db->queryUniqueObject($sql);
					$sale_amount=($resultDay->sale_amount=="")?0:$resultDay->sale_amount;
					$row[]=array('sale_amount' =>$sale_amount);
					$day_total[$j-1] = $day_total[$j-1] + $sale_amount;
				}

				$total=0;
				for($cont=0;$cont<count($row);$cont++)
				{
					$total=$total+$row[$cont]["sale_amount"];
				}
				
				$balance=$target-$total;

				$target_total = $target_total + $target;
				$total_total = $total_total + $total;
				$balance_total = $balance_total + $balance;
				echo '<td>'.$total.'</td>';
				echo '<td>'.$balance.'</td>';
				for($j=0;$j<count($row);$j++)
				{
					
						echo '<td>'.$row[$j]['sale_amount'].'</td>';
					
				}
				echo '</tr>';
			}
			?>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="text-align:right;"><b>Total</b></td>
				<td><?php echo $target_total;?> </td>
				<td><?php echo $total_total;?> </td>
				<td><?php echo $balance_total;?> </td>
				<?php
				for($i=0;$i<count($day_total);$i++)
				{
					echo '<td>'.$day_total[$i].'</td>';
				}
				?>
			</tr>
		</tbody>
	</table>
</div></div></div>
</div>
</div>


<script type="text/javascript">
	$("#report").tableHeadFixer({'left' : 7 ,'head' : true}); 
</script>
<style type="text/css">
	td{
		background-color: white;
	}
</style>

<?php include('footer.php');?>