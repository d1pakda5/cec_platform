<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$error = 0;
$request_id = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : 0;
$user_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id = '".$request_id."' ");
if(!$user_info) {
	echo "ERROR,Invalid User ID";
	exit();
}
?>
<style>
.fancy-box .bg-status {
	background:#27ae61!important;
	border-color:#27ae61!important;
	color:#fff!important;
}
.fancy-box h3.box-title {
	font-size:18px!important;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#frmSms").bind('submit', function(e) {
		e.preventDefault();
		jQuery.ajax({
			type : "POST",
			cache : false,
			url : 'ajax/send-sms-process.php',
			data : jQuery(this).serializeArray(),
			success : function(data) {
			  jQuery.fancybox(data, {
					closeClick : false,
					autoSize : true,
					padding : 10,
					helpers : { 
						overlay : {closeClick: false}
					}
			  });
			}
		});
		return false;
	});
});
</script>
<div class="box fancy-box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">Send SMS</h3>
	</div>
	<div class="box-body">
		<div class="body-inner" style="padding:10px 40px;">			
			<div class="col-md-12">
			<form action="" method="post" id="frmSms" class="form-horizontal">				
				<div class="form-group">
					<label>Mobile</label>
					<input type="text" readonly="" name="mobile" id="mobile" value="<?php echo $user_info->mobile;?>" class="form-control" />
				</div>
				<div class="form-group">
					<label>API's</label>
					<select name="api" id="api" class="form-control">
						<option value=""></option>
						<?php
						$query = $db->query("SELECT * FROM sms_api WHERE status='1' ORDER BY sms_api_id ASC");
						while($result = $db->fetchNextObject($query)) { ?>
						<option value="<?php echo $result->sms_api_id;?>"><?php echo $result->api_name;?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>Message</label>
					<textarea name="message" id="message" rows="4" class="form-control"></textarea>
				</div>
				<div class="form-group text-right">
					<button type="submit" name="submit" id="submit" class="btn btn-info">
						<i class="fa fa-send"></i> Send
					</button>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>