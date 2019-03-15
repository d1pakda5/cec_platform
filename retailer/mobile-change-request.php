<?php
session_start();
if(!isset($_SESSION['retailer'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
if(!isset($_GET['token']) || $_GET['token'] != $token) { exit("Token not match"); }
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['old_mobile'] == '' || $_POST['confirm_mobile'] == '') {
		$error = 1;		
	} else {		
		$old_mobile = htmlentities(addslashes($_POST['old_mobile']),ENT_QUOTES);
		$confirm_mobile = htmlentities(addslashes($_POST['confirm_mobile']),ENT_QUOTES);
		$exists = $db->queryUniqueObject("SELECT * FROM apps_user WHERE mobile = '".$confirm_mobile."' ");
		if($exists) {
			$error = 2;
		} else {
			$db->execute("INSERT INTO `mobile_change_request`(`request_id`, `request_user`, `request_to`, `mobile_old`, `mobile_new`, `request_date`, `request_status`) VALUES ('', '".$_SESSION['retailer_uid']."', '0', '".$old_mobile."', '".$confirm_mobile."', NOW(), '0')");
			$error = 3;	
		}	
	}
}

$meta['title'] = "Mobile Change";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#mobileForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to submit request?")) {
        form.submit();
      }
		},
	  rules: {
	  	old_mobile: {
				required: true,
	      minlength: 10,
				maxlength: 10
			},
			new_mobile: {
				required: true,
	      minlength: 10,
				maxlength: 10
			},
			confirm_mobile: {
				required: true,
	      minlength: 10,
				maxlength: 10,
				equalTo: "#new_mobile"
			}
	  },
		highlight: function(element) {
			jQuery(element).closest('.jrequired').addClass('text-red');
		}
	});
});
</script>
<div class="content">
	<div class="container">
		<div class="page-header">
			<div class="page-title">My Account <small>/ Mobile Change Request</small></div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Request submitted successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Mobile number is used by other user!
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
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Mobile Change</h3>
					</div>
					<form action="" method="post" id="mobileForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-xs-12">Old Mobile <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="old_mobile" id="old_mobile" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">New Mobile <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="new_mobile" id="new_mobile" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Confirm Mobile </label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="confirm_mobile" id="confirm_mobile" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
							    <?php if($_SESSION['retailer_uid']=='20032374')
								{?>
									<button type="submit" disabled name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
								

								<?php } else {
								?>
									<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Submit
								</button>
								

								<?php }?>
							
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> List Mobile Change</h3>
					</div>
					<div class="box-body min-height-300">
						<table class="table table-basic">
							<thead>
								<tr>
									<th>Date</th>
									<th>Mobile</th>
									<th><i class="fa fa-arrow-right"></i></th>
									<th>New Mobile</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$query = $db->query("SELECT * FROM mobile_change_request WHERE request_user = '".$_SESSION['retailer_uid']."' ORDER BY request_date DESC LIMIT 10 ");
								if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
								while($result = $db->fetchNextObject($query)) {
								?>
								<tr>
									<td><?php echo date("d/m/Y H:i:s", strtotime($result->request_date));?></td>
									<td><?php echo $result->mobile_old;?></td>
									<td><i class="fa fa-arrow-right"></i></td>
									<td><b><?php echo $result->mobile_new;?></b></td>
									<td>
										<?php if($result->status == '1') {?>
										<i class="fa fa-check text-green"></i>
										<?php } else if ($result->status == '2') { ?>
										<i class="fa fa-times text-red"></i>
										<?php } else { ?>
										<i class="fa fa-minus text-blue"></i>
										<?php } ?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>