<?php
session_start();

include('../config.php');


include('../system/class.pagination.php');
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"] != '' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"] != '' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");

 

$meta['title'] = "User Transaction Reports";
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
function doExcel1(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/user-transaction1.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function doExcel2(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/user-transaction2.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function doExcel3(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/user-transaction3.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function doExcel4(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/user-transaction4.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
function doExcel5(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var type = jQuery('#type').val();
	var uid = jQuery('#uid').val();
	window.location='excel/user-transaction5.php?from='+from+'&to='+to+'&type='+type+'&uid='+uid;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ User Transaction</small></div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List User Transactions</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
									</div>
								</div>
							</div>
						</div>												
						<div class="col-sm-3">
							<div class="form-group">
								<input type="text" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="User UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-5">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control">
									<option value=""></option>
									<option value="FUND" <?php if(isset($_GET['type']) && $_GET['type'] == "FUND") { ?> selected="selected"<?php } ?>>FUND</option>
									<option value="RECHARGE" <?php if(isset($_GET['type']) && $_GET['type'] == "RECHARGE") { ?> selected="selected"<?php } ?>>RECHARGE</option>
									<option value="FAILURE" <?php if(isset($_GET['type']) && $_GET['type'] == "FAILURE") { ?> selected="selected"<?php } ?>>FAILURE</option>
									<option value="REFUND" <?php if(isset($_GET['type']) && $_GET['type'] == "REFUND") { ?> selected="selected"<?php } ?>>REFUND</option>
									<option value="REVERT" <?php if(isset($_GET['type']) && $_GET['type'] == "REVERT") { ?> selected="selected"<?php } ?>>REVERT</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<button type="button" onclick="doExcel1()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel-1</button>
								<button type="button" onclick="doExcel2()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel-2</button>
								<button type="button" onclick="doExcel3()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel-3</button>
								<button type="button" onclick="doExcel4()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel-4</button>
								<button type="button" onclick="doExcel5()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel-5</button>
							</div>
						</div>
					</form>
				</div>
				
			</div>
		</div>
	
	</div>
</div>
<?php include('footer.php');?>