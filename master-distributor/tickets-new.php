<?php
session_start();
if(!isset($_SESSION['mdistributor'])) header("location:login.php");
include('../config.php');
include('common.php');
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['title'] == '' || $_POST['message'] == '' || $_POST['urgency'] == '') {
		$error = 1;		
	} else {
		
		$title = htmlentities(addslashes($_POST['title']),ENT_QUOTES);
		$message = htmlentities(addslashes($_POST['message']),ENT_QUOTES);
				
		$db->execute("INSERT INTO `tickets`(`ticket_id`, `submited_by`, `title`, `message`, `urgency`, `submit_date`, `is_read`, `is_admin_read`, `last_reply_date`, `status`) VALUES ('', '".$_SESSION['mdistributor_uid']."', '".$title."', '".$message."', '".$_POST['urgency']."', NOW(), '0', '0', NOW(), '".$_POST['status']."')");
		$error = 3;		
	}
}
$array['status'] = getTicketStatusList();
$array['urgency'] = getTicketUrgencyList();

$meta['title'] = "Tickets ";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#ticketForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to submit ticket?")) {
        form.submit();
      }
		},
	  rules: {
	  	title: {
				required: true
			},
			message: {
				required: true
			},
			urgency: {
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
	<div class="container">
		<div class="page-header">
			<div class="page-title">Support Tickets <small>/ Add New</small></div>
			<div class="pull-right">
				<a href="tickets.php" class="btn btn-info"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<?php if($error == 3) { ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i> Submited successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 2) { ?>
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i> Oops, Please upload recipt copy!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 1) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-times"></i> Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Create new ticket</h3>
					</div>
					<form action="" method="post" id="ticketForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">								
								<div class="form-group">
									<label class="col-xs-12">Title <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<input type="text" name="title" id="title" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Message <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<textarea name="message" id="message" rows="10" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-sm-6">
											<label class="col-xs-12">Urgency <i class="text-red">*</i></label>
											<div class="col-xs-12 jrequired">
												<select name="urgency" id="urgency" class="form-control">
													<?php foreach($array['urgency'] as $data) {?>
													<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<label class="col-xs-12">Status <i class="text-red">*</i></label>
											<div class="col-xs-12 jrequired">
												<select name="status" id="status" class="form-control">
													<?php foreach($array['status'] as $data) {?>
													<option value="<?php echo $data['id'];?>" <?php if($data['id'] == '0') {?>selected="selected"<?php } ?>><?php echo $data['name'];?></option>
													<?php } ?>
												</select>
											</div>
										</div>
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
			<div class="col-sm-3">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-angle-right"></i> Notepad</h3>
					</div>
					<div class="box-body min-height-300">
						<textarea name="notepad" id="notepad" rows="20" class="form-control"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>