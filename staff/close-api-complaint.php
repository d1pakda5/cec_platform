<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
if(empty($sP['is_close_api_complaint'])) { 
	include('permission.php');
	exit(); 
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {
	if($_POST['request_id'] == '' || $_POST['remark'] == '' ) {
		$error = 1;		
	} else {
	     $db->execute("Update apps_recharge set is_api_complaint=2, api_complaint_remark='".$_POST['remark']."' WHERE recharge_id = '".$_POST['request_id']."' ");
		 $error = 4;
		 header("location:rpt-recharge.php?error=4");
		} 
					
	}



$meta['title'] = "Close API Complaint";
include('header.php');
?>
 
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">


 
	

	jQuery('#fundForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want close complaint")) {
        form.submit();
      }
		},
	  rules: {
			remark: {
				required: true
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">API Complaint  <small>/ Close</small></div>
			<div class="pull-right">
				<a href="fund-request.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		
		<?php if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some parameters are missing!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Close API Complaint</h3>
					</div>
					<form action="" method="post" id="fundForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-6">								
								
								<div class="form-group">
									<label class="col-sm-4 control-label"> Recharge Id :</label>
									<div class="col-sm-8 jrequired">
										<input type="text" readonly="true" name="request_id" id="request_id" value="<?php echo $_GET['id'] ?>" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label"> Remark :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="remark" id="remark" class="form-control"></textarea>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-6">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			
	</div>
</div>

<?php include('footer.php'); ?>