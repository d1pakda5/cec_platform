<?php
session_start();
if(!isset($_SESSION['distributor'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('common.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;

if(isset($_POST['submit'])) {	
	if($_POST['reply'] == '' || $_POST['status'] == '') {
		$error = 1;
	} else {
		$reply = htmlentities(addslashes($_POST['reply']),ENT_QUOTES);		
		$db->execute("INSERT INTO `tickets_reply`(`reply_id`, `ticket_id`, `message`, `uid`, `reply_date`) VALUES ('', '".$request_id."', '".$reply."', '".$_SESSION['distributor_uid']."', NOW())");		
		$db->execute("UPDATE `tickets` SET `is_admin_read` = '0', `last_reply_date` = NOW(), `status`='".$_POST['status']."' WHERE `ticket_id` = '".$request_id."' ");	
		header("location:tickets-detail.php?id=".$request_id."&error=3");
	}
}
$ticket = $db->queryUniqueObject("SELECT * FROM tickets WHERE ticket_id = '".$request_id."' AND submited_by = '".$_SESSION['distributor_uid']."' ");
if(!$ticket) header("location:tickets.php");

$array['status'] = getTicketStatusList();
$array['urgency'] = getTicketUrgencyList();

$meta['title'] = "Tickets ";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#ticketForm').validate({
	  rules: {
	  	reply: {
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
					<div class="box-body">
						<div class="panel panel-warning">
								<div class="panel-heading">
									Submit on date <b><?php echo date("d/m/Y G:i", strtotime($ticket->submit_date));?></b>							
							</div>
							<div class="panel-body">
								<strong>Title:</strong> <?php echo nl2br($ticket->title);?><br>
								<strong>Urgency:</strong> <?php echo getTicketUrgency($array['urgency'],$ticket->urgency);?><br>
								<strong>Status:</strong> <?php echo getTicketStatus($array['status'],$ticket->status);?><br>
								===================================<br>
								<?php echo nl2br($ticket->message);?>
							</div>
						</div>
						<?php
						$scnt = 1;
						$query = $db->query("SELECT * FROM tickets_reply WHERE ticket_id = '".$request_id."' ORDER BY reply_id ASC");
						while($result = $db->fetchNextObject($query)) {
							$user_reply_name = "";
							$user_reply_type = 0;
							if($result->uid == '') {
								$admin_reply = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$result->admin_user."' ");
								if($admin_reply) {
									$user_reply_name = $admin_reply->fullname;
									$user_reply_type = 0;
								}
							} else {
								$user_reply = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$result->uid."' ");
								if($user_reply) {
									$user_reply_name = $user_reply->company_name;
									$user_reply_type = $user_reply->user_type;
								}
							} ?>
						<div class="panel <?php if($user_reply_type == '0') {?>panel-default<?php } else { ?>panel-info<?php } ?>">
							<div class="panel-heading">
									<?php echo $user_reply_name;?> [<b><?php if($user_reply_type == '0') { echo "Support Admin";} else { echo getUserType($user_reply_type); }?></b>] reply on date <b><?php echo date("d/m/Y G:i", strtotime($result->reply_date));?></b>							
							</div>
							<div class="panel-body">
								<?php echo nl2br($result->message);?>
							</div>
						</div>
						<?php } ?>
						<div class="well">
							<form action="" method="post" id="ticketForm" class="form-horizontal">
								<div class="form-group">
									<label class="col-xs-12">Message <i class="text-red">*</i></label>
									<div class="col-xs-12 jrequired">
										<textarea name="reply" id="reply" rows="10" class="form-control"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-xs-12">Status <i class="text-red">*</i></label>
									<div class="col-xs-4 jrequired">
										<select name="status" id="status" class="form-control">
											<?php foreach($array['status'] as $data) {?>
											<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-xs-8">
										<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
											<i class="fa fa-reply"></i> Submit Reply
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
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