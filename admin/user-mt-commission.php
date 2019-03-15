<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$request_id = isset($_GET['uid']) && $_GET['uid'] != '' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$token = isset($_GET['token']) && $_GET['token'] != '' ? mysql_real_escape_string($_GET['token']) : 0;
if($token != hashToken($request_id)) {
	exit("Token not match");
}

if(isset($_POST['submit'])) {
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['admin']."' ");
	if($admin_info) {
		if($admin_info->pin == $pin) {
			for($i = 1; $i<=count($_POST['mt_slab_id']); $i++) {
				$exists = $db->queryUniqueObject("SELECT id FROM apps_mt_commission WHERE mt_slab_id = '".mysql_real_escape_string($_POST['mt_slab_id'][$i])."' AND uid = '".$request_id."' ");
				if($exists) {
					$is_per[$i] = isset($_POST['is_percentage'][$i]) ? $_POST['is_percentage'][$i] : "n";
					if($_POST['user_type'] == '1') {
						$db->execute("UPDATE apps_mt_commission SET comm_api = '".mysql_real_escape_string($_POST['commission_ap'][$i])."',  is_percentage = '".$is_per[$i]."', surcharge_value = '".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status = '".$_POST['slab_status'][$i]."' WHERE id = '".$_POST['comm_id'][$i]."' AND uid = '".$request_id."' ");
					} else {
						$db->execute("UPDATE apps_mt_commission SET comm_mdist = '".mysql_real_escape_string($_POST['commission_md'][$i])."', comm_dist = '".mysql_real_escape_string($_POST['commission_ds'][$i])."', comm_ret = '".mysql_real_escape_string($_POST['commission_rt'][$i])."', is_percentage = '".$is_per[$i]."', surcharge_value = '".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status = '".$_POST['slab_status'][$i]."' WHERE id = '".$_POST['comm_id'][$i]."' AND uid = '".$request_id."' ");
					}
				} else {
					$is_per[$i] = isset($_POST['is_percentage'][$i]) ? $_POST['is_percentage'][$i] : "n";
					if($_POST['user_type'] == '1') {
						$db->execute("INSERT INTO apps_mt_commission(id, uid, mt_slab_id, comm_api, is_percentage, surcharge_value, status) values('', '".$request_id."', '".mysql_real_escape_string($_POST['mt_slab_id'][$i])."', '".mysql_real_escape_string($_POST['commission_ap'][$i])."', '".$is_per[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['slab_status'][$i]."') ");
					} else {						
						$db->execute("INSERT INTO apps_mt_commission(id, uid, mt_slab_id, comm_mdist, comm_dist, comm_ret, is_percentage, surcharge_value, status) values('', '".$request_id."', '".mysql_real_escape_string($_POST['mt_slab_id'][$i])."', '".mysql_real_escape_string($_POST['commission_md'][$i])."', '".mysql_real_escape_string($_POST['commission_ds'][$i])."', '".mysql_real_escape_string($_POST['commission_rt'][$i])."', '".$is_per[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['slab_status'][$i]."') ");
					}
				}
			}
			$error = 3;
			 
		} else {
			$error = 2;
		}
	} else {
		$error = 1;
		
	}
}

$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid = '".$request_id."' ");
if(!$user) header("location:index.php");

$meta['title'] = "Money Transfer Commissions Settings";
include("header.php");
?>
<script type = "text/javascript">
function fill(t){
	jQuery('.fill-'+t).each(function() {
    	jQuery(this).val(jQuery('#comm_'+t).val());	
	});
}
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Money Transfer Commission <small>/ <?php echo getUserType($user->user_type);?> </small></div>
			<div class="pull-right">				
				<a href="user-commission.php?uid=<?php echo $user->uid;?>&token=<?php echo hashToken($user->uid);?>" class="btn btn-primary"><i class="fa fa-align-left"></i> Recharge</a>
			</div>
		</div>		
		<?php if($error == 3) { ?>
		<div class="alert alert-success" role="alert">
			Updated successfully
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==2) { ?>
		<div class="alert alert-warning" role="alert">
			Oops, Invalid Pin!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }elseif($error==1) { ?>
		<div class="alert alert-danger" role="alert">
			Oops, Some manditory fields are empty.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }	?>
		<div class="row">
			<div class="col-sm-12">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title"><i class="fa fa-pencil-square"></i> Update Commission</h3>
					</div>
					<div class="box-body no-padding min-height-480">
						<form id="form1" method="post" action="" class="form-inline">
						<input type="hidden" name="user_type" id="user_type" value="<?php echo $user->user_type;?>" />
						<table class="table table-condensed">
							<thead>
								<tr>
									<th width="6%">S. No.</th>
									<th>Operator Name</th>
									<th>Percentage</th>
									<th>Value</th>
									<?php if($user->user_type == '3') {?>		
									<th width="10%">Master Dist</th>
									<th width="10%">Distributor</th>
									<th width="10%">Retailer</th>
									<?php } else { ?>
									<th width="10%">API</th>
									<?php } ?>
									<th width="5%">Status</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="4">Input All</td>
									<?php if($user->user_type == '3') {?>	
									<td><input type="text" size="5" name="comm_md" id="comm_md" onchange="fill('md')" class="form-control input-sm" placeholder="Enter" /></td>
									<td><input type="text" size="5" name="comm_ds" id="comm_ds" onchange="fill('ds')" class="form-control input-sm" placeholder="Enter" /></td>
									<td><input type="text" size="5" name="comm_rt" id="comm_rt" onchange="fill('rt')" class="form-control input-sm" placeholder="Enter" /></td>
									<?php } else { ?>
									<td><input type="text" size="5" name="comm_ap" id="comm_ap" onchange="fill('ap')" class="form-control input-sm" placeholder="Enter" /></td>
									<?php } ?>
									<td></td>
								</tr>
								<?php
								$i = 0;														
								$query = $db->query("SELECT * FROM mt_slab ORDER BY min_amount ASC ");
								while($result = $db->fetchNextObject($query)) {
									$comm_info = $db->queryUniqueObject("SELECT * FROM apps_mt_commission WHERE uid = '".$user->uid."' AND mt_slab_id = '".$result->mt_slab_id."' ");
								 ?>
								<tr id="<?php echo $i++;?>" <?php if(!$comm_info) {?>class="bg-danger"<?php }?>>
									<td align="center">
										<?php echo $i;?>
										<input type="hidden" name="mt_slab_id[<?php echo $i;?>]" value="<?php echo $result->mt_slab_id;?>" />
										<input type="hidden" name="comm_id[<?php echo $i;?>]" value="<?php if($comm_info) { echo $comm_info->id;}?>" />
										<input type="hidden" name="slab_status[<?php echo $i;?>]" value="<?php echo $result->status;?>" />
									</td>
									<td><?php echo round($result->min_amount,2);?> - <?php echo round($result->max_amount,2);?> Rs.</td>
								<?php if($comm_info) { ?>
									<td>
										<input type="checkbox" id="is_percentage<?php echo $i;?>" name="is_percentage[<?php echo $i;?>]" value="y" <?php if($comm_info->is_percentage == 'y') {?>checked="checked"<?php } ?> /> Yes
									</td>
									<td>
										<input type="text" size="5" id="surcharge_amt<?php echo $i;?>" name="surcharge_amt[<?php echo $i;?>]" value="<?php echo round($comm_info->surcharge_value,2);?>" class="form-control input-sm" />
									</td>	
									<?php 
									//For MD, Dist, Retailer
									if($user->user_type == '3') {?>
									<td>
										<input type="text" size="5" id="commission_md<?php echo $i;?>" name="commission_md[<?php echo $i;?>]" value="<?php echo $comm_info->comm_mdist;?>" class="form-control input-sm fill-md" />
									</td>
									<td>
										<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_info->comm_dist;?>" class="form-control input-sm fill-ds" />
									</td>
									<td>
										<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_info->comm_ret;?>" class="form-control input-sm fill-rt" />
									</td>
									<?php } else { ?>
									<td>
										<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_info->comm_api;?>" class="form-control input-sm fill-ap" />
									</td>
									<?php } ?>
									<td align="center">
										<?php if($comm_info->status == '1') {?>
											<i class="fa fa-check-circle text-green"></i>
										<?php }else {?>
											<i class="fa fa-minus-circle text-red"></i>
										<?php }?>
									</td>
								<?php } else { ?>
									<td>
										<input type="checkbox" id="is_percentage<?php echo $i;?>" name="is_percentage[<?php echo $i;?>]" value="y" /> Yes
									</td>
									<td>
										<input type="text" id="surcharge_amt<?php echo $i;?>" name="surcharge_amt[<?php echo $i;?>]" value="<?php echo round($result->surcharge_amount,2);?>" class="form-control input-sm" placeholder="Surcharge" />
									</td>
									<?php 
									//For MD, Dist, Retailer
									if($user->user_type == '3') {?>	
									<td>
										<input type="text" size="5" id="commission_md<?php echo $i;?>" name="commission_md[<?php echo $i;?>]" class="form-control input-sm fill-md" />
									</td>
									<td>
										<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" class="form-control input-sm fill-ds" />
									</td>
									<td>
										<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" class="form-control input-sm fill-rt" />
									</td>
									<?php } else { ?>
									<td>
										<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" class="form-control input-sm fill-ap" />
									</td>
									<?php } ?>								
									<td align="center">
										<?php if($result->status == '1') {?>
											<i class="fa fa-check-circle text-green"></i>
										<?php }else {?>
											<i class="fa fa-minus-circle text-red"></i>
										<?php }?>
									</td>
								<?php } ?>
								</tr>
								<?php } ?>
								<tr>
									<td colspan='100%'>&nbsp;</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="100%" class="text-right">
										<input type="text" name="pin" id="pin" size="8" placeholder="PIN" class="form-control" />
										<input type="submit" name="submit" value="Update" class="btn btn-success" />
										<input type="reset" name="reset" value="Reset" class="btn btn-default" />
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>
<?php include("footer.php");?>