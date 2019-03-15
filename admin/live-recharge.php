<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$meta['title'] = "Recharge";
include('header.php');
?>
<style>
table {
	width:100%;
	font-family: monospace;
	font-size:13px;
	background:#000;
	color:#FFFFFF;
}
.table-condensed > thead > tr > th,
.table-condensed > tbody > tr > th,
.table-condensed > tfoot > tr > th,
.table-condensed > thead > tr > td,
.table-condensed > tbody > tr > td,
.table-condensed > tfoot > tr > td {
  padding: 5px 3px;
}
</style>
<script>
$(document).ready(function(){    
    loadstation();
});
function loadstation(){
	$("#dataRecharge").load("ajax-recharge.php");
	setTimeout(loadstation, 5000);
}
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Live <small>/ Recharge</small></div>
		</div>
		<div class="box">
			<div class="box-body no-padding min-height-480">
				<table class="table-condensed">					
					<tbody id="dataRecharge">						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>