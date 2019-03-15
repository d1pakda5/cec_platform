<?php
session_start();
if(!isset($_SESSION['staff'])) {
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
		$db->execute("INSERT INTO `tickets_reply`(`reply_id`, `ticket_id`, `message`, `admin_user`, `reply_date`) VALUES ('', '".$request_id."', '".$reply."', '".$_SESSION['staff']."', NOW())");		
		$db->execute("UPDATE `tickets` SET `is_read` = '0', `last_reply_date` = NOW(), `status`='".$_POST['status']."' WHERE `ticket_id` = '".$request_id."' ");	
		$error = 2;
	}
}

$array['status'] = getTicketStatusList();
$array['urgency'] = getTicketUrgencyList();

$ticket = $db->queryUniqueObject("SELECT * FROM tickets WHERE ticket_id = '".$request_id."' ");
if(!$ticket) header("location:operator.php");
$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$ticket->submited_by."' ");

$meta['title'] = "Support Tickets";
include('header.php');
?>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">ID#<?php echo $ticket->ticket_id;?> <small>/ Support Tickets</small></div>
			<div class="pull-right">
				<a href="tickets.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Support Tickets</h3>
			</div>			
			<div class="box-body min-height-480">
				<table class="table">
					<tr>
						<th width="18%">Date</th>
						<th>Subject</th>
						<th width="10%">Urgency</th>
						<th width="10%">Status</th>
					</tr>
					<tr>
						<td><?php echo date("d/m/Y G:i", strtotime($ticket->submit_date));?></td>
						<td><?php echo $ticket->title;?></td>						
						<td><?php echo getTicketUrgency($array['urgency'],$ticket->urgency);?></td>		
						<td><?php echo getTicketStatus($array['status'],$ticket->status);?></td>
					</tr>
				</table>
				<br><br>
				<div class="panel panel-info">
					<div class="panel-heading">
						<?php echo $user->company_name;?> [<b><?php echo getUserType($user->user_type);?></b>] on data <b><?php echo date("d/m/Y G:i", strtotime($ticket->submit_date));?></b>
					</div>
					<div class="panel-body">
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
							<?php echo $user_reply_name;?> [<b><?php if($user_reply_type == '0') { echo "Support Admin";} else { echo getUserType($user_reply_type); }?></b>] on date <b><?php echo date("d/m/Y G:i", strtotime($result->reply_date));?></b>							
					</div>
					<div class="panel-body">
						<?php echo nl2br($result->message);?>
					</div>
				</div>
				<?php } ?>
				<?php if(isset($sP['is_support']) && $sP['is_support'] == 'y') { ?>
				<form action="" method="post" enctype="multipart/form-data" id="supportTicket" class="form-horizontal">
				<div class="panel panel-success mt-20">
					<div class="panel-heading">Reply</div>
					<div class="panel-body">					
						<div class="form-group">
							<label class="col-sm-2 control-label">Reply :</label>
							<div class="col-sm-10 jrequired">
								<textarea name="reply" id="reply" rows="6" class="form-control"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Status :</label>
							<div class="col-sm-4 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php foreach($array['status'] as $data) { ?>
									<option value="<?php echo $data['id'];?>"><?php echo $data['name'];?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm-6">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-reply"></i> Reply
								</button>
							</div>
						</div>
					</div>
				</div>
				</form>
				<?php } else {?> 
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>