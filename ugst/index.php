<?php
session_start();
include('config.php');
//
include('class.gst.php');
$gst = new GST();
//
include('../system/class.pagination.php');
$tbl = new ListTable();

//
$months = $gst->getMonthList();
$meta['title'] = "GST Invoice Reports";
include('header.php');
?>
<script>
$(document).ready(function() {
	$('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	$('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
});
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
	</div>
</div>
<?php include('footer.php');?>