<?php 
$aRequestDmt = $db->queryUniqueObject("SELECT * FROM dmt_activation_request WHERE dmt_request_uid='".$aRetailer->uid."' ORDER BY dmt_request_date DESC");
$option_info = $db->queryUniqueObject("SELECT * FROM dmt_options WHERE dmt_option_name='retailer_activation_charge' ");
if($option_info) {
	$activation_charge = $option_info->dmt_option_value;
} else {
	$activation_charge = "0";
}
if($aRequestDmt){
?>
<div class="none">
	<?php if($aRequestDmt->dmt_request_status=='0') {?>
	<div class="alert alert-warning alert-shadow">
		<i class="fa fa-warning"></i> Your request for money transfer is pending and updated soon.
	</div>
	<?php } else { ?>
		<?php if($aRequestDmt->dmt_update_status=='2') { ?>
		<div class="alert alert-danger alert-shadow">
			<i class="fa fa-times"></i> Your request has been cancelled, To re-initiate money transfer activation please click request activation button.
		</div>
		<div class="text-center">
			<a href="#" class="btn btn-default" data-toggle="modal" data-target="#ajaxModal" data-remote="false">Request Activation</a>
		</div>
		<?php } elseif($aRequestDmt->dmt_update_status=='1') { ?>
		<div class="alert alert-success alert-shadow">
			<i class="fa fa-check"></i> Your request has been accepted, If you see this message please contact administrator.
		</div>
		<?php } else { ?>
		<div class="alert alert-warning alert-shadow">
			<i class="fa fa-warning"></i> Your request has been cancelled, please contact administrator or your distributor for re-initiate.
		</div>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<div class="alert alert-danger alert-shadow">
		<i class="fa fa-minus-circle"></i> This service is inactive in your account. To activate money transfer service please click request activation button.
	</div>
	<div class="text-center">
		<a href="#" class="btn btn-default" data-toggle="modal" data-target="#ajaxModal" data-remote="false">Request Activation</a>
	</div>
<?php } ?>
</div>
<div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
		<div class="modal-content">
  		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Request</h4>
			</div>
			<div class="modal-body">
				<form id="frmDmtRequest">
					<input type="hidden" name="uid" value="<?php echo $aRetailer->uid;?>">
					<input type="hidden" name="amount" value="<?php echo $activation_charge;?>">
				</form>
				<p>Activation Charges: <?php echo $activation_charge;?> Rs</p>
				<p>By submitting request you are agree with <a href="#" id="termsCond">terms and conditions</a></p>
			</div>
			<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="dmtRequest" class="btn btn-primary">Submit</button>
      </div>
		</div>
	</div>
</div>
<div class="modal fade" id="ajaxModalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
		<div class="modal-content">
  		<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Response</h4>
			</div>
			<div class="modal-body" id="getResponse">
				<p>Please wait......</p>
			</div>
			<div class="modal-footer">
        <button type="button" id="btnDismiss" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
		</div>
	</div>
</div>
<script>
jQuery(function () {
	jQuery("#dmtRequest").click(function() {
    jQuery.ajax({ 
			url : "ajax-dmt-request.php",
			type : "POST",
			data: jQuery("#frmDmtRequest").serialize(),
			async : false,
			success	: function(data) {
				jQuery('#ajaxModal').modal('hide');
				jQuery('#ajaxModalMsg').modal('show');
				jQuery("#getResponse").html(data);
			}
		});
	});
	jQuery("#btnDismiss").click(function() {
		window.location.reload();
	});
});
</script>