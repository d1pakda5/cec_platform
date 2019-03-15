<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE date LIKE '%".$aFrom."%' ";


//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-all-user.php');



$meta['title'] = "API Balance Report";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
    
    // alert($(".click_it").attr("val"));
});


jQuery(document).ready(function() {
    
 
    
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});


});

</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ API Balance</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Reports</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">

										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										
								<input type="submit" value="Filter" class="btn btn-warning">
									</div>
								</div>
							</div>
						</div>
					
						
						
						
					
					</form>
				</div>
				<table class="table table-striped table-bordered table-condensed-sm">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th width="8%">Date</th>
							<th>API 2</th>
							<th>API 6</th>
							<th>API 7</th>
							<th>API 9</th>
							<th>API 10</th>
							<th>API 14</th>
							<th>API 16</th>
							<th>Virtual Balance</th>
							
						</tr>
					</thead>
					<tbody>
						<?php
						$query = $db->query("SELECT *, virtual as vb FROM api_balance $sWhere LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						   
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->date;?></td>
							<td><?php echo $result->api2;?></td>
							<td><?php echo $result->api6;?></td>
							<td><?php echo $result->api7;?></td>
							<td><?php echo $result->api9;?></td>
							<td><?php echo $result->api10;?></td>
							<td><?php echo $result->api14;?></td>
							<td><?php echo $result->api14;?></td>
							<td><?php echo $result->vb;?></td>
							
    					</tr>
						<?php } ?>
					</tbody>
					
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php'); ?>