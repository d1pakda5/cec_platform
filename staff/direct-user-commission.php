<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
if(empty($sP['dir_ret_commission'])) { 
	include('permission.php');
	exit(); 
}
$request_id = isset($_GET['uid']) && $_GET['uid'] != '' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$token = isset($_GET['token']) && $_GET['token'] != '' ? mysql_real_escape_string($_GET['token']) : 0;
if($token != hashToken($request_id)) {
	exit("Token not match");
}

if(isset($_POST['submit'])) {
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id = '".$_SESSION['staff']."' ");
	if($admin_info) {
		if($admin_info->pin==$pin) {
			for($i = 1; $i<=count($_POST['operator_id']); $i++) {
			
				$commission_type[$i] = isset($_POST['commission_type'][$i]) ? $_POST['commission_type'][$i] : "%";
				$is_surcharge[$i] = isset($_POST['is_surcharge'][$i]) ? $_POST['is_surcharge'][$i] : "n";
				$surcharge_type[$i] = isset($_POST['surcharge_type'][$i]) ? $_POST['surcharge_type'][$i] : "f";				
				
				$exists = $db->queryUniqueObject("SELECT id FROM usercommissions WHERE operator_id='".mysql_real_escape_string($_POST['operator_id'][$i])."' AND uid='".$request_id."'");
				if($exists) {
					
					if($_POST['user_type']=='1') {
						$db->execute("UPDATE usercommissions SET comm_api='".mysql_real_escape_string($_POST['commission_ap'][$i])."', commission_type='".$commission_type[$i]."', is_surcharge='".$is_surcharge[$i]."', surcharge_type='".$surcharge_type[$i]."', surcharge_value='".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status='".$_POST['operator_status'][$i]."' WHERE id='".$_POST['comm_id'][$i]."' AND uid='".$request_id."' ");
					} else {
						$db->execute("UPDATE usercommissions SET comm_dist='".mysql_real_escape_string(0)."', comm_ret='".mysql_real_escape_string($_POST['commission_rt'][$i])."', commission_type='".$commission_type[$i]."', is_surcharge='".$is_surcharge[$i]."', surcharge_type='".$surcharge_type[$i]."', surcharge_value='".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status='".$_POST['operator_status'][$i]."' WHERE id='".$_POST['comm_id'][$i]."' AND uid='".$request_id."' ");
					}
					
				} else {
					
					if($_POST['user_type']=='1') {
						$db->execute("INSERT INTO usercommissions(uid, operator_id, comm_api, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."', '".mysql_real_escape_string($_POST['commission_ap'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
					} else {						
						$db->execute("INSERT INTO usercommissions(uid, operator_id, comm_dist, comm_ret, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."', '".mysql_real_escape_string(0)."', '".mysql_real_escape_string($_POST['commission_rt'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
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
	
	header("location:direct-user-commission.php?uid=".$request_id."&token=".$token."&error=".$error);
	exit();
}

if(isset($_POST['inactive'])) {
	$db->execute("UPDATE usercommissions SET status = 0 WHERE  uid='".$request_id."' and operator_id in('15','19','22','4','12','18','25','36','37','39','40','41','67','42','43','44','45','60','46','47','48','49','50','55','56','57','58','59','61','62','63','64','65','68','69','70','71','51','52','66','72','53','54','101','102','103','104','105','106','107','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99','100')");
	}
	
if(isset($_POST['active'])) {
	$db->execute("UPDATE usercommissions SET status = 1 WHERE  uid='".$request_id."' and operator_id in('15','19','22','4','12','18','25','36','37','39','40','41','67','42','43','44','45','60','46','47','48','49','50','55','56','57','58','59','61','62','63','64','65','68','69','70','71','51','52','66','72','53','54','101','102','103','104','105','106','107','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99','100')");
	}


$user = $db->queryUniqueObject("SELECT * FROM apps_user WHERE uid='".$request_id."' ");
if(!$user) { header("location:index.php"); exit(); }

$meta['title'] = "Commissions Settings";
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
			<div class="page-title">Commission <small>/ <?php echo getUserType($user->user_type);?> </small></div>
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
								<?php if($user->user_type == '6') {?>		
								
								<th width="10%"> Direct Retailer</th>
								<?php } else { ?>
								<th width="10%">API</th>
								<?php } ?>
								<th>Scharge</th>
								<th>Value</th>
								<th>In %</th>
								<th width="1%">Status</th>
							</tr>
							<tr>
								<?php if($user->user_type=='6') {?>	
								<td colspan="1"></td>
								<td colspan="3" style="text-align: center;">
								<td colspan="5" style="text-align: center;"><b class="text-red">Insert Direct Retailer Commission</b></td>
								<td colspan="1"></td>
								<?php } ?>
							</tr>
							<tr>
								<?php if($user->user_type=='6') {?>	
								
								<td colspan="4" style="text-align: center;">
									
								</td>
								
								<td colspan="1" style="text-align: center;">
									<label>Mobile</label><br>
									<input type="text" size="5" name="comm_DRpre" id="comm_DRpre" onchange="fill('DRpre')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Mobile(Exp)</label><br>
									<input type="text" size="5" name="comm_DRexp" id="comm_DRexp" onchange="fill('DRexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH</label><br>
									<input type="text" size="5" name="comm_DRdth" id="comm_DRdth" onchange="fill('DRdth')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH(Exp)</label><br>
									<input type="text" size="5" name="comm_DRdexp" id="comm_DRdexp" onchange="fill('DRdexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Utility</label><br>
									<input type="text" size="5" name="comm_DRutl" id="comm_DRutl" onchange="fill('DRutl')" class="" placeholder="Enter" />
								</td>
								<td colspan="1"></td>
								<?php }?>
							</tr>
							<tr>
								<td colspan="4"><b class="text-red">Insert Multiple Type Commission</b></td>
								<?php if($user->user_type=='6') {?>	
								
								<td><input type="text" size="5" name="comm_rt" id="comm_rt" onchange="fill('rt')" class="form-control input-sm" placeholder="Enter" /></td>
								<?php } else { ?>
								<td><input type="text" size="5" name="comm_ap" id="comm_ap" onchange="fill('ap')" class="form-control input-sm" placeholder="Enter" /></td>
								<?php } ?>
								<td colspan="3">
									
								</td>
								<td colspan="1">
									<input type="submit" style="font-size: 12px;padding: 1px;" name="inactive" value="Inactive Exps" class="btn btn-warning" />
								
									<input type="submit" style="font-size: 12px;padding: 1px; width:76px;" name="active" value="Active Exps" class="btn btn-success" />
								</td>
							</tr>
							<?php
							$i = 0;														
							$query = $db->query("SELECT opr.*, ser.service_name FROM operators opr LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id ORDER BY opr.service_type,opr.operator_name ASC ");
							while($result = $db->fetchNextObject($query)) {
								$comm_info = $db->queryUniqueObject("SELECT * FROM usercommissions WHERE uid='".$user->uid."' AND operator_id='".$result->operator_id."' ");
								if($comm_info) {
								    $comm_dist = $comm_info->comm_dist;
									$comm_ret = $comm_info->comm_ret;
									$comm_api = $comm_info->comm_api;
									$commission_type = $result->commission_type;
									$is_surcharge = $result->is_surcharge;
									$surcharge_type = $result->surcharge_type;
									$surcharge_value = $comm_info->surcharge_value;
									$status = $comm_info->status;
									$id = $comm_info->id;
								} else {
								
									$comm_dist = "";
									$comm_ret = "";
									$comm_api = "";
									$commission_type = $result->commission_type;
									$is_surcharge = $result->is_surcharge;
									$surcharge_type = $result->surcharge_type;
									$surcharge_value = $result->surcharge_value;
									$status = $result->status;
									$id = $comm_info->id;

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
								<?php if($user->user_type=='6') {					
								if($result->operator_id>='73' && $result->operator_id<='94') { ?>	
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt fill-DRexp" />
								</td>
								<?php } else if($result->operator_id=='1'||$result->operator_id=='2'||$result->operator_id=='5'||$result->operator_id=='7'||$result->operator_id=='8'||$result->operator_id=='10'||$result->operator_id=='11'||$result->operator_id=='13'||$result->operator_id=='14'||$result->operator_id=='16'||$result->operator_id=='17'||$result->operator_id=='20'||$result->operator_id=='23'||$result->operator_id=='24'||$result->operator_id=='26'||$result->operator_id=='28'||$result->operator_id=='29'||$result->operator_id=='31'||$result->operator_id=='32'||$result->operator_id=='33'||$result->operator_id=='34'||$result->operator_id=='35'||$result->operator_id=='38'){?>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt fill-DRpre" />
								</td>
								<?php } else if($result->operator_id=='3'||$result->operator_id=='6'||$result->operator_id=='9'||$result->operator_id=='21'||$result->operator_id=='27'||$result->operator_id=='30'){?>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt fill-DRdth" />
								</td>
								<?php } else if($result->operator_id>='95' && $result->operator_id<='100'){?>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt fill-DRdexp" />
								</td>
								<?php } else if($result->operator_id=='4'||$result->operator_id=='12'||$result->operator_id=='18'||$result->operator_id=='25'||$result->operator_id=='36'||$result->operator_id=='37'||$result->operator_id=='39'||$result->operator_id=='40'||$result->operator_id=='41'||$result->operator_id=='67'||$result->operator_id=='42'||$result->operator_id=='43'||$result->operator_id=='44'||$result->operator_id=='45'||$result->operator_id=='60'||$result->operator_id=='46'||$result->operator_id=='47'||$result->operator_id=='48'||$result->operator_id=='49'||$result->operator_id=='50'||$result->operator_id=='55'||$result->operator_id=='56'||$result->operator_id=='57'||$result->operator_id=='58'||$result->operator_id=='59'||$result->operator_id=='61'||$result->operator_id=='62'||$result->operator_id=='63'||$result->operator_id=='64'||$result->operator_id=='65'||$result->operator_id=='68'||$result->operator_id=='69'||$result->operator_id=='70'||$result->operator_id=='71'||$result->operator_id=='51'||$result->operator_id=='52'||$result->operator_id=='66'||$result->operator_id=='72'||$result->operator_id=='53'||$result->operator_id=='54'||$result->operator_id=='101'||$result->operator_id=='102'||$result->operator_id=='103'||$result->operator_id=='104'){?>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt fill-DRutl" />
								</td>
								<?php } else{?>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="form-control input-sm fill-rt" />
								</td>
								
								<?php } ?>
								<?php } else { ?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="form-control input-sm fill-ap" />
								</td>
								<?php } ?>
								<td align="center">
									<input type="checkbox" id="is_surcharge<?php echo $i;?>" name="is_surcharge[<?php echo $i;?>]" value="y" <?php if($is_surcharge=='y') {?>checked="checked"<?php } ?> />
								</td>
								<td>
									<input type="text" size="5" id="surcharge_amt<?php echo $i;?>" name="surcharge_amt[<?php echo $i;?>]" value="<?php echo $surcharge_value;?>" class="form-control input-sm" />
								</td>
								<td><b><?php if($is_surcharge=='y') {echo getSurchargeType($surcharge_type);}?></b></td>	
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
								<?php if($user->user_type == '6') { ?>
								<td colspan="4"></td>
								<?php } else { ?>
								<td colspan="4"></td>
								<?php } ?>
								<td colspan="2"><input type="password" name="pin" id="pin" size="8" placeholder="PIN" class="form-control" /></td>
								<td colspan="3">
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
<?php include("footer.php");?>