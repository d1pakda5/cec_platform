<?php
session_start();

include('config.php');
$meta['title'] = "Recharge";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">	
<title>Live Recharge</title>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" id="theme-style">
<link rel="stylesheet" href="css/theme.css" type="text/css" />
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.js"></script>
<script>
$(document).ready(function(){    
    loadstation();
});
function loadstation(){
	$("#dataRecharge").load("ajax-recharge.php");
	setTimeout(loadstation, 5000);
}
</script>
<style>
table {
	width:100%;
	font-family: monospace;
	font-size:19px;
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
<body class="hold-transition login-page">

<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title" style="text-align:center; float:none !important; font-size:30px !important">Live <small>/ Recharge</small></div>
		</div>
		<div class="box">
			<div class="box-body no-padding min-height-480">
				<table class="table-condensed table-bordered">					
					<tbody id="dataRecharge">						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>