<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include('../config.php');


$meta['title'] = "Monthly Sale Report";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){	
	var today = new Date(),
    yyyy = today.getFullYear(),
    inpYear = $('#year'),
    html = '';

	for (var i = 0; i < 5; i++, yyyy++) {
	    html = html + '<option>' + yyyy + '</option>';
	};    

	inpYear.html(html);



});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"> Monthly Sale Report <small>Select month/year</small></div>
			<div class="pull-right">
				<a href="admin-dashboard.php" class="btn btn-primary"><i class="fa fa-reply"></i> Dashboard</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Sale Target</h3>
					</div>
					<form action="monthly_sale_rpt.php" method="post" id="" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-sm-4 control-label">Month :</label>
									<div class="col-sm-8 jrequired">
										<select name="month" id="month" class="form-control">
										    <option value=''>--Select Month--</option>
										    <option value='01'>Janaury</option>
										    <option value='02'>February</option>
										    <option value='03'>March</option>
										    <option value='04'>April</option>
										    <option value='05'>May</option>
										    <option value='06'>June</option>
										    <option value='07'>July</option>
										    <option value='08'>August</option>
										    <option value='09'>September</option>
										    <option value='10'>October</option>
										    <option value='11'>November</option>
										    <option value='12'>December</option>
										 </select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Year :</label>
									<div class="col-sm-8 jrequired">
										<select name="year" id="year" class="form-control">
										    
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Notepad</h3>
					</div>
					<div class="box-body min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<textarea name="note" id="note" rows="21" cols="15" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>