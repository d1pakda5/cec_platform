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
	if($_POST['min_amount'] == '' || $_POST['max_amount'] == '' || $_POST['surcharge_amount'] == '') {
		$error = 1;		
	} else {
		$min_amount = htmlentities(addslashes($_POST['min_amount']),ENT_QUOTES);
		$max_amount = htmlentities(addslashes($_POST['max_amount']),ENT_QUOTES);
		$surcharge_amount = htmlentities(addslashes($_POST['surcharge_amount']),ENT_QUOTES);
		$exist_info = $db->queryUniqueObject("SELECT * FROM mt_slab WHERE mt_slab_id != '".$request_id."' AND ( ( min_amount BETWEEN '".$min_amount."' AND '".$max_amount."' ) OR ( max_amount BETWEEN '".$min_amount."' AND '".$max_amount."' ) ) ");
		if($exist_info) {
			$error = 2;		
		} else {		
			$db->execute("UPDATE `mt_slab` SET `min_amount`='".$min_amount."', `max_amount`='".$max_amount."', `surcharge_type`='".$_POST['type']."', `surcharge_amount`='".$surcharge_amount."', `status`='".$_POST['status']."' WHERE mt_slab_id = '".$request_id."' ");
			$error = 3;
		}
	}
}

$slab_info = $db->queryUniqueObject("SELECT * FROM mt_slab WHERE mt_slab_id = '".$request_id."' ");
if(!$slab_info) header("location:money-transfer-slab.php");

$meta['title'] = "Money Transfer - Amount Slab - Edit";
include('header.php');
?>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#slabForm').validate({
		submitHandler : function(form) {
			if (confirm("Are you sure want to update slab?")) {
				form.submit();
			}
		},
		rules: {
			min_amount: {
				required: true,
				number: true
			},
			max_amount: {
				required: true,
				number: true
			},
			surcharge_amount: {
				required: true,
				number: true
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
			<div class="page-title">Amount Slab <small>/ Money Transfer / Add</small></div>
			<div class="pull-right">
				<a href="money-transfer-slab.php" class="btn btn-primary"><i class="fa fa-th-list"></i></a>
			</div>
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
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-pencil"></i> Update slab</h3>
			</div>
			<form action="" method="post" id="slabForm" class="form-horizontal">
			<div class="box-body padding-50">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="col-sm-4 control-label">Amount Range :</label>
							<div class="col-sm-8">
								<div class="row">
									<div class="col-sm-4 jrequired">
										<input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Minimum Amount" value="<?php echo round($slab_info->min_amount,2);?>" />
									</div>
									<div class="col-sm-1">
										<p class="form-control-static">To</p>
									</div>
									<div class="col-sm-4 jrequired">
										<input type="text" name="max_amount" id="max_amount" class="form-control" placeholder="Maximum Amount" value="<?php echo round($slab_info->max_amount,2);?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Surcharge Type :</label>
							<div class="col-sm-8 jrequired">
								<div class="radio">
									<label>
										<input type="radio" name="type" id="typef" value="f" <?php if($slab_info->surcharge_type=='f') {?>checked="checked"<?php }?> /> Fixed
									</label>
									<label>
										<input type="radio" name="type" id="typep" value="p" <?php if($slab_info->surcharge_type=='p') {?>checked="checked"<?php }?> /> Percentage
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Surcharge Amount :</label>
							<div class="col-sm-8 jrequired">
								<input type="text" name="surcharge_amount" id="surcharge_amount" class="form-control" placeholder="Surcharge Amount" value="<?php echo round($slab_info->surcharge_amount,2);?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status :</label>
							<div class="col-sm-8 jrequired">
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<option value="1" <?php if($slab_info->status=='1') {?>selected="selected"<?php } ?>>Active</option>
									<option value="0" <?php if($slab_info->status=='0') {?>selected="selected"<?php } ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="row">
					<div class="col-md-12">
						<button type="submit" name="submit" id="submit" class="btn btn-info pull-right">
							<i class="fa fa-save"></i> Save
						</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
