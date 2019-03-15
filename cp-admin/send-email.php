<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

$meta['title'] = "Send Email";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#sendemailForm").bind('submit', function(e) {
		e.preventDefault();
		jQuery.ajax({
			type : "POST",
			cache : false,
			url : 'ajax/send-bulk-email-process.php',
			data : jQuery(this).serializeArray(),
			success : function(data) {
			  alert(data);
			 
			}
		});
		return false;
	});
});
jQuery(document).ready(function(){
	jQuery("#user_type, #status").change(function(){
		var user = jQuery("#user_type").val();
		var status = jQuery("#status").val();
		jQuery.ajax({ 
			url: "ajax/user-email-ids.php",
			type: "POST",
			data: "type="+user+"&status="+status,
			async: false,
			success: function(data) {
				jQuery("#emails").html(data);
			}
		});
	});
	jQuery('#sendemailForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to send email?")) {
        form.submit();
      }
		},
	  rules: {
	  
		
			emails: {
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
			<div class="page-title">Send SMS <small>/ Edit</small></div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Duplicate entry some fields are already exists!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some fields are empty!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Send SMS</h3>
					</div>
					<form action="" method="post" id="sendemailForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-4 control-label">Users :</label>
									<div class="col-sm-8 jrequired">
										<div class="row">
											<div class="col-md-8">
												<select name="user_type" id="user_type" class="form-control">
													<option value=""></option>
													<option value="0">All</option>
													<option value="1">API User</option>
													<option value="3">Master Distributor</option>
													<option value="4">Distributor</option>
													<option value="5">Retailer</option>
													<option value="6">Direct Retailer</option>
													<option value="">New</option>
												</select>
											</div>
											<div class="col-md-4">
												<select name="status" id="status" class="form-control">
													<option value="1"></option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
													<option value="9">Deleted User</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							
								<div class="form-group">
									<label class="col-sm-4 control-label"> Email IDs :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="emails" id="emails" rows="3" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label"> Message :</label>
									<div class="col-sm-8 jrequired">
										<textarea name="message" id="message" rows="5" class="form-control"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Send Email
								</button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
