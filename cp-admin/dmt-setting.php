<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
$request_id = isset($_GET['id']) && $_GET['id'] != '' ? mysql_real_escape_string($_GET['id']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
if(isset($_POST['submit'])) {
	if($_POST['options']=='') {
		$error = 1;		
	} else {
		foreach($_POST['options'] as $opt=>$value) {
			$option_info = $db->queryUniqueObject("SELECT * FROM dmt_options WHERE dmt_option_name='".$opt."' ");
			if($option_info) {
				$db->execute("UPDATE `dmt_options` SET `dmt_option_value`='".$value."' WHERE `dmt_option_id`='".$option_info->dmt_option_id."' ");
			} else {
				$db->execute("INSERT INTO `dmt_options`(`dmt_option_name`, `dmt_option_value`) VALUES ('".$opt."', '".$value."')");	
			}
		}
		$error = 3;
	}
}
$opt = [];
$query = $db->query("SELECT dmt_option_name, dmt_option_value FROM dmt_options");
while($result = $db->fetchNextObject($query)) {
	$opt[] = array('name'=>$result->dmt_option_name, 'value'=>$result->dmt_option_value);
}
$meta['title'] = "Profile";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#optionForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update profile?")) {
        form.submit();
      }
		},
		ignore: [],
	  rules: {
	  	'options[retailer_activation_charge]': {
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
			<div class="page-title">Money Transfer <small>/ Settings</small></div>
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
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Settings</h3>
					</div>
					<form action="" method="post" id="optionForm" class="form-horizontal">
					<div class="box-body padding-50 min-height-300">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="col-sm-5 control-label">Retailer Activation Charge :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="options[retailer_activation_charge]" value="<?php if($opt[0]['name']=='retailer_activation_charge'){ echo $opt[0]['value'];}?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">Auto Activation :</label>
									<div class="col-sm-3 jrequired">
										<input type="text" name="options[retailer_activation_type]" value="<?php if($opt[1]['name']=='retailer_activation_type'){ echo $opt[1]['value'];}?>" class="form-control" />
									</div>
									<div class="col-sm-4 jrequired">
										<label class="col-sm-4 control-label">Yes or No</label>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">S-Paisa (DMT) Account Validation Charge :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="options[spaisa_account_validation]" value="<?php if($opt[2]['name']=='spaisa_account_validation'){ echo $opt[2]['value'];}?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">S-Paisa (DMT) Processing Charge :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="options[spaisa_processing]" value="<?php if($opt[3]['name']=='spaisa_processing'){ echo $opt[3]['value'];}?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">S-Paisa (DMT) 1-2500 :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="options[spaisa_slab_one]" value="<?php if($opt[4]['name']=='spaisa_slab_one'){ echo $opt[4]['value'];}?>" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-5 control-label">S-Paisa (DMT) 2501-5000 :</label>
									<div class="col-sm-7 jrequired">
										<input type="text" name="options[spaisa_slab_two]" value="<?php if($opt[5]['name']=='spaisa_slab_two'){ echo $opt[5]['value'];}?>" class="form-control" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-md-12">
								<button type="submit" name="submit" id="submit" class="btn btn-primary pull-right">
									<i class="fa fa-save"></i> Update Profile
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
