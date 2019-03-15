<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include('common.php');
if(empty($sP['dir_ret_commission'])) { 
	include('permission.php');
	exit(); 
}

$request_id = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$token = isset($_GET['token']) && $_GET['token']!='' ? mysql_real_escape_string($_GET['token']) : 0;
if($token != hashToken($request_id)) {
	exit("Token not match");
}

if(isset($_POST['submit'])) {
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['staff']."' ");
	if($admin_info) {
		if($admin_info->pin==$pin) {
			for($i = 1; $i<=count($_POST['operator_id']); $i++) {
			
				$commission_type[$i] = isset($_POST['commission_type'][$i]) ? $_POST['commission_type'][$i] : "%";
				$is_surcharge[$i] = isset($_POST['is_surcharge'][$i]) ? $_POST['is_surcharge'][$i] : "n";
				$surcharge_type[$i] = isset($_POST['surcharge_type'][$i]) ? $_POST['surcharge_type'][$i] : "f";				
				
				$exists = $db->queryUniqueObject("SELECT id FROM usercommissions WHERE operator_id='".mysql_real_escape_string($_POST['operator_id'][$i])."' AND uid='".$request_id."'");
				if($exists) {
					
					if($_POST['user_type']=='1') {
						$db->execute("UPDATE usercommissions SET comm_api='".mysql_real_escape_string($_POST['commission_ap'][$i])."', commission_type='".$commission_type[$i]."', api_id='".mysql_real_escape_string($_POST['api_id'][$i])."', is_surcharge='".$is_surcharge[$i]."', surcharge_type='".$surcharge_type[$i]."', surcharge_value='".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status='".$_POST['operator_status'][$i]."' WHERE id='".$_POST['comm_id'][$i]."' AND uid='".$request_id."' ");
					} else {
						$db->execute("UPDATE usercommissions SET comm_mdist='".mysql_real_escape_string($_POST['commission_md'][$i])."', comm_dist='".mysql_real_escape_string($_POST['commission_ds'][$i])."', comm_ret='".mysql_real_escape_string($_POST['commission_rt'][$i])."', commission_type='".$commission_type[$i]."', api_id='".mysql_real_escape_string($_POST['api_id'][$i])."', is_surcharge='".$is_surcharge[$i]."', surcharge_type='".$surcharge_type[$i]."', surcharge_value='".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status='".$_POST['operator_status'][$i]."' WHERE id='".$_POST['comm_id'][$i]."' AND uid='".$request_id."' ");
					}
					
				} else {
					
					if($_POST['user_type']=='1') {
						$db->execute("INSERT INTO usercommissions(uid, operator_id, api_id, comm_api, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."','".$api_id[$i]."', '".mysql_real_escape_string($_POST['commission_ap'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
					} else {						
						$db->execute("INSERT INTO usercommissions(uid, operator_id,comm_mdist, api_id, comm_dist, comm_ret, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."','".mysql_real_escape_string($_POST['commission_md'][$i])."','".$api_id[$i]."', '".mysql_real_escape_string($_POST['commission_ds'][$i])."', '".mysql_real_escape_string($_POST['commission_rt'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
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
	
	header("location:user-commission.php?uid=".$request_id."&token=".$token."&error=".$error);
	exit();
}

if(isset($_POST['inactive'])) {
	$db->execute("UPDATE usercommissions SET status = 0 WHERE  uid='".$request_id."' and operator_id in('15','19','22','4','12','18','25','36','37','39','40','41','67','42','43','44','45','60','46','47','48','49','50','55','56','57','58','59','61','62','63','64','65','68','69','70','71','51','52','66','72','53','54','101','102','103','104','105','106','107','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99','100')");
	}
	
if(isset($_POST['active'])) {
	$db->execute("UPDATE usercommissions SET status = 1 WHERE  uid='".$request_id."' and operator_id in('15','19','22','4','12','18','25','36','37','39','40','41','67','42','43','44','45','60','46','47','48','49','50','55','56','57','58','59','61','62','63','64','65','68','69','70','71','51','52','66','72','53','54','101','102','103','104','105','106','107','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99','100')");
	}


$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$request_id."' ");
if(!$user) { 
	header("location:index.php");
	exit();
}

$meta['title'] = "Commissions Settings";
include("header.php");
?>
<script type = "text/javascript">
function fill(t){
	$('.fill-'+t).each(function() {
    	$(this).val(jQuery('#comm_'+t).val());	
	});
}
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title"><?php echo $user->company_name;?> (<?php echo $user->uid;?>) <small>/ <?php echo getUserType($user->user_type);?> Commission </small> </div>
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
						<table class="table table-condensed table-bordered">
							<tr>
								<th width="6%">S. No.</th>
								<th>Operator Name</th>
								<th>Service Type</th>
								<th width="1%">Com</th>
								<?php if($user->user_type == '3') {?>		
								<th width="10%">Master Dist</th>
								<th width="10%">Distributor</th>
								<th width="10%">Retailer</th>
								<?php } else { ?>
								<th width="10%">API</th>
								<?php } ?>
								<th>Scharge</th>
								<th>Value</th>
								<th>In %</th>
								<th>API</th>
								<th width="1%">Status</th>
							</tr>
							<tr>
								<td colspan="4"><b class="text-red">Insert Multiple Type Commission</b></td>
								<?php if($user->user_type=='3') {?>	
								<td><input type="text" size="5" name="comm_md" id="comm_md" onchange="fill('md')" class="form-control input-sm" placeholder="Enter" /></td>
								<td><input type="text" size="5" name="comm_ds" id="comm_ds" onchange="fill('ds')" class="" placeholder="Enter" /></td>
								<td><input type="text" size="5" name="comm_rt" id="comm_rt" onchange="fill('rt')" class="" placeholder="Enter" /></td>
								<?php } else { ?>
								<td><input type="text" size="5" name="comm_ap" id="comm_ap" onchange="fill('ap')" class="" placeholder="Enter" /></td>
								<?php } ?>
								<td colspan="4">
									
								</td>
								<td colspan="1">
									<input type="submit" style="font-size: 12px;padding: 1px;" name="inactive" value="Inactive Exps" class="btn btn-warning" />
								
									<input type="submit" style="font-size: 12px;padding: 1px; width:76px;" name="active" value="Active Exps" class="btn btn-success" />
								</td>
							</tr>
							<?php
							$i = 0;		
							$usercommid = "";												
							$query = $db->query("SELECT opr.*, ser.service_name FROM operators opr LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE service_type!='10' ORDER BY opr.service_type,opr.operator_name ASC ");
							while($result = $db->fetchNextObject($query)) {
								$comm_info = $db->queryUniqueObject("SELECT * FROM usercommissions WHERE uid='".$user->uid."' AND operator_id='".$result->operator_id."' ");
								if($comm_info) {
									$comm_mdist = $comm_info->comm_mdist;
									$usercommid = $comm_info->id;
									$comm_dist = $comm_info->comm_dist;
									$comm_ret = $comm_info->comm_ret;
									$comm_api = $comm_info->comm_api;
									$commission_type = $result->commission_type;
									$is_surcharge = $result->is_surcharge;
									$surcharge_type = $result->surcharge_type;
									$surcharge_value = $comm_info->surcharge_value;
									$status = $comm_info->status;
									$id = $comm_info->id;
									$api_id= $comm_info->api_id;
								} else {
									$comm_mdist ="" ;
									$comm_dist = "";
									$comm_ret = "";
									$comm_api = "";
									$commission_type = $result->commission_type;
									$is_surcharge = $result->is_surcharge;
									$surcharge_type = $result->surcharge_type;
									$surcharge_value = $result->surcharge_value;
									$status = $result->status;
									$id = $comm_info->id;
									$api_id= $comm_info->api_id;

								}
							 ?>
							<tr id="<?php echo $i++;?>" <?php if(!$comm_info) {?>class="bg-danger"<?php }?>>
								<td align="center">
									<?php echo $i;?>
									<input type="hidden" name="operator_id[<?php echo $i;?>]" value="<?php echo $result->operator_id;?>" />
									<input type="hidden" name="comm_id[<?php echo $i;?>]" value="<?php if($comm_info) { echo $comm_info->id;}?>" />
									<input type="hidden" name="commission_type[<?php echo $i;?>]" value="<?php echo $commission_type;?>" />
									<input type="hidden" name="surcharge_type[<?php echo $i;?>]" value="<?php echo $surcharge_type;?>" />
									<input type="hidden" name="operator_status[<?php echo $i;?>]" value="<?php echo $status;?>" />
								</td>
								<td><?php echo $result->operator_name;?></td>
								<td><?php echo $result->service_name;?></td>								
								<td align="center"><b><?php echo getCommissionType($commission_type=='p');?></b></td>						
								<?php if($user->user_type=='3') {?>	
								<td>
									<input type="text" size="5" id="commission_md<?php echo $i;?>" name="commission_md[<?php echo $i;?>]" value="<?php echo $comm_mdist;?>" class="form-control input-sm fill-md" />
								</td>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds" />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt" />
								</td>
								<?php } else { ?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap" />
								</td>
								<?php } ?>
								<td align="center">
									<input type="checkbox" id="is_surcharge<?php echo $i;?>" name="is_surcharge[<?php echo $i;?>]" value="y" <?php if($is_surcharge=='y') {?>checked="checked"<?php } ?> />
								</td>
								<td>
									<input type="text" size="5" id="surcharge_amt<?php echo $i;?>" name="surcharge_amt[<?php echo $i;?>]" value="<?php echo $surcharge_value;?>" />
								</td>
								<td><b><?php if($is_surcharge=='y') {echo getSurchargeType($surcharge_type);}?></b></td>	
								<td><select name="api_id[<?php echo $i;?>]" id="api_id<?php echo $i;?>" class="form-control">
									<option value=""></option>
									<?php
									$query2 = $db->query("SELECT * FROM api_list WHERE status = '1'");
									while($result2 = $db->fetchNextObject($query2)) { ?>
									<option value="<?php echo $result2->api_id;?>"<?php if($api_id==$result2->api_id) {?> selected="selected"<?php } ?>><?php echo $result2->api_name;?></option>
									<?php } ?>
								</select></td>
								<td align="center">
								<?php if($status == "1") {?>
									<?php if($id) { ?>
									<a href="operator-action_direact_retailer.php?token=<?php echo $token;?>&id=<?php echo $id;?>&status=0" class="label label-success">Active</a>
									<?php } else { ?>
									<span class="label label-success">Active</span>
									<?php } ?>
								<?php } else {?>
									<?php if($id) { ?>
									<a href="operator-action_direact_retailer.php?token=<?php echo $token;?>&id=<?php echo $id;?>&status=1" class="label label-danger">Inactive</a>
									<?php } else { ?>
									<span class="label label-danger">Inactive</span>
									<?php } ?>
								<?php }?>
								</td>								
							</tr>
							<?php } ?>
							<tr>
								<td colspan='100%'>&nbsp;</td>
							</tr>
							<tr>
								<?php if($user->user_type=='3') { ?>
								<td colspan="6"></td>
								<?php } else { ?>
								<td colspan="4"></td>
								<?php } ?>
								<td colspan="2"><input type="text" name="pin" id="pin" size="8" placeholder="PIN" class="form-control" /></td>
								<td colspan="4">
									<input type="submit" name="submit" value="Update" class="btn btn-success" />
									<input type="reset" name="reset" value="Reset" class="btn btn-default" />
								</td>
							</tr>
						</table>
						</form>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>
<script type="text/javascript">
function actionRow(id, action) {
	if(id!="" && action!="") {
		var conf=confirm("Are you sure you want to "+action+" this service/operator");
		if(conf) {
			location.href="users-operators-status.php?id="+id+"&action="+action;
		}
	}
}
</script> 
<?php include("footer.php");?>