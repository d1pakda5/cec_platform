<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
$request_id = isset($_GET['uid']) && $_GET['uid']!='' ? mysql_real_escape_string($_GET['uid']) : 0;
$error = isset($_GET['error']) && $_GET['error']!='' ? mysql_real_escape_string($_GET['error']) : 0;
$token = isset($_GET['token']) && $_GET['token']!='' ? mysql_real_escape_string($_GET['token']) : 0;
if($token != hashToken($request_id)) {
	exit("Token not match");
}

if(isset($_POST['submit'])) {
	$pin = hashPin(htmlentities(addslashes($_POST['pin']),ENT_QUOTES));
	$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['admin']."' ");
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
						$db->execute("UPDATE usercommissions SET comm_dist='".mysql_real_escape_string($_POST['commission_ds'][$i])."', comm_ret='".mysql_real_escape_string($_POST['commission_rt'][$i])."', commission_type='".$commission_type[$i]."', api_id='".mysql_real_escape_string($_POST['api_id'][$i])."', is_surcharge='".$is_surcharge[$i]."', surcharge_type='".$surcharge_type[$i]."', surcharge_value='".mysql_real_escape_string($_POST['surcharge_amt'][$i])."', status='".$_POST['operator_status'][$i]."' WHERE id='".$_POST['comm_id'][$i]."' AND uid='".$request_id."' ");
					}
					
				} else {
					
					if($_POST['user_type']=='1') {
						$db->execute("INSERT INTO usercommissions(uid, operator_id, api_id, comm_api, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."','".$api_id[$i]."', '".mysql_real_escape_string($_POST['commission_ap'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
					} else {						
						$db->execute("INSERT INTO usercommissions(uid, operator_id, api_id, comm_dist, comm_ret, commission_type, is_surcharge, surcharge_type, surcharge_value, status) values('".$request_id."', '".mysql_real_escape_string($_POST['operator_id'][$i])."','".$api_id[$i]."', '".mysql_real_escape_string($_POST['commission_ds'][$i])."', '".mysql_real_escape_string($_POST['commission_rt'][$i])."', '".$commission_type[$i]."', '".$is_surcharge[$i]."', '".$surcharge_type[$i]."', '".$_POST['surcharge_amt'][$i]."', '".$_POST['operator_status'][$i]."') ");
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
	
	header("location:usercommissions.php?uid=".$request_id."&token=".$token."&error=".$error);
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
								<?php if($user->user_type == '4') {?>		
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
								<?php if($user->user_type=='4') {?>	
								<td colspan="1"></td>
								<td colspan="4" style="text-align: center;"><b class="text-red">Insert Distributor Commission</b></td>
								<td colspan="5" style="text-align: center;"><b class="text-red">Insert Retailer Commission</b></td>
								<td colspan="1"></td>
								<?php } else {?>	
								<td colspan="1"></td>
								<td colspan="4" style="text-align: center;">
								<td colspan="5" style="text-align: center;"><b class="text-red">Insert API Commission</b></td>
								<td colspan="1"></td>
								<?php } ?>
							</tr>
							<tr>
								<?php if($user->user_type=='4') {?>	
								<td colspan="1" style="text-align: center;">
									<label>Mobile</label><br>
									<input type="text" size="5" name="comm_Dpre" id="comm_Dpre" onchange="fill('Dpre')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Mobile(Exp)</label><br>
									<input type="text" size="5" name="comm_Dexp" id="comm_Dexp" onchange="fill('Dexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH</label><br>
									<input type="text" size="5" name="comm_Ddth" id="comm_Ddth" onchange="fill('Ddth')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH(Exp)</label><br>
									<input type="text" size="5" name="comm_Ddexp" id="comm_Ddexp" onchange="fill('Ddexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Utility</label><br>
									<input type="text" size="5" name="comm_Dutl" id="comm_Dutl" onchange="fill('Dutl')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Mobile</label><br>
									<input type="text" size="5" name="comm_Rpre" id="comm_Rpre" onchange="fill('Rpre')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Mobile(Exp)</label><br>
									<input type="text" size="5" name="comm_Rexp" id="comm_Rexp" onchange="fill('Rexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH</label><br>
									<input type="text" size="5" name="comm_Rdth" id="comm_Rdth" onchange="fill('Rdth')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH(Exp)</label><br>
									<input type="text" size="5" name="comm_Rdexp" id="comm_Rdexp" onchange="fill('Rdexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Utility</label><br>
									<input type="text" size="5" name="comm_Rutl" id="comm_Rutl" onchange="fill('Rutl')" class="" placeholder="Enter" />
								</td>
								<td colspan="1"></td>
								<?php } else { ?>
								<td colspan="5" style="text-align: center;">
									
								</td>
								
								<td colspan="1" style="text-align: center;">
									<label>Mobile</label><br>
									<input type="text" size="5" name="comm_Apre" id="comm_Apre" onchange="fill('Apre')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Mobile(Exp)</label><br>
									<input type="text" size="5" name="comm_Aexp" id="comm_Aexp" onchange="fill('Aexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH</label><br>
									<input type="text" size="5" name="comm_Adth" id="comm_Adth" onchange="fill('Adth')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>DTH(Exp)</label><br>
									<input type="text" size="5" name="comm_Adexp" id="comm_Adexp" onchange="fill('Adexp')" class="" placeholder="Enter" />
								</td>
								<td colspan="1" style="text-align: center;">
									<label>Utility</label><br>
									<input type="text" size="5" name="comm_Autl" id="comm_Autl" onchange="fill('Autl')" class="" placeholder="Enter" />
								</td>
								<td colspan="1"></td>
								<?php }?>
							</tr>
							<tr>
								<td colspan="4"><b class="text-red">Insert Multiple Type Commission</b></td>
								<?php if($user->user_type=='4') {?>	
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
							$query = $db->query("SELECT opr.*, ser.service_name FROM operators opr LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id  ORDER BY opr.service_type,opr.operator_name ASC ");
							while($result = $db->fetchNextObject($query)) {
								$comm_info = $db->queryUniqueObject("SELECT * FROM usercommissions WHERE uid='".$user->uid."' AND operator_id='".$result->operator_id."' ");
								if($comm_info) {
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
								<?php if($user->user_type=='4') {
										if($result->operator_id>='73' && $result->operator_id<='94') { ?>	
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds fill-Dexp" />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt fill-Rexp" />
								</td>
								<?php } else if($result->operator_id=='1'||$result->operator_id=='2'||$result->operator_id=='5'||$result->operator_id=='7'||$result->operator_id=='8'||$result->operator_id=='10'||$result->operator_id=='11'||$result->operator_id=='13'||$result->operator_id=='14'||$result->operator_id=='16'||$result->operator_id=='17'||$result->operator_id=='20'||$result->operator_id=='23'||$result->operator_id=='24'||$result->operator_id=='26'||$result->operator_id=='28'||$result->operator_id=='29'||$result->operator_id=='31'||$result->operator_id=='32'||$result->operator_id=='33'||$result->operator_id=='34'||$result->operator_id=='35'||$result->operator_id=='38'){?>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds fill-Dpre" />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt fill-Rpre" />
								</td>
								<?php } else if($result->operator_id=='3'||$result->operator_id=='6'||$result->operator_id=='9'||$result->operator_id=='21'||$result->operator_id=='27'||$result->operator_id=='30'){?>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds fill-Ddth" />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt fill-Rdth"  />
								</td>
								<?php } else if($result->operator_id>='95' && $result->operator_id<='100'){?>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds fill-Ddexp" />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt fill-Rdexp" />
								</td>
								<?php } else if($result->operator_id=='4'||$result->operator_id=='12'||$result->operator_id=='18'||$result->operator_id=='25'||$result->operator_id=='36'||$result->operator_id=='37'||$result->operator_id=='39'||$result->operator_id=='40'||$result->operator_id=='41'||$result->operator_id=='67'||$result->operator_id=='42'||$result->operator_id=='43'||$result->operator_id=='44'||$result->operator_id=='45'||$result->operator_id=='60'||$result->operator_id=='46'||$result->operator_id=='47'||$result->operator_id=='48'||$result->operator_id=='49'||$result->operator_id=='50'||$result->operator_id=='55'||$result->operator_id=='56'||$result->operator_id=='57'||$result->operator_id=='58'||$result->operator_id=='59'||$result->operator_id=='61'||$result->operator_id=='62'||$result->operator_id=='63'||$result->operator_id=='64'||$result->operator_id=='65'||$result->operator_id=='68'||$result->operator_id=='69'||$result->operator_id=='70'||$result->operator_id=='71'||$result->operator_id=='51'||$result->operator_id=='52'||$result->operator_id=='66'||$result->operator_id=='72'||$result->operator_id=='53'||$result->operator_id=='54'||$result->operator_id=='101'||$result->operator_id=='102'||$result->operator_id=='103'||$result->operator_id=='104'){?>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds fill-Dutl " />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt fill-Rutl " />
								</td>
								<?php } else{?>
								<td>
									<input type="text" size="5" id="commission_ds<?php echo $i;?>" name="commission_ds[<?php echo $i;?>]" value="<?php echo $comm_dist;?>" class="fill-ds " />
								</td>
								<td>
									<input type="text" size="5" id="commission_rt<?php echo $i;?>" name="commission_rt[<?php echo $i;?>]" value="<?php echo $comm_ret;?>" class="fill-rt " />
								</td>
								<?php } ?>
								<?php } else { 
								if($result->operator_id>='73' && $result->operator_id<='94') { ?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap fill-Aexp" />
								</td>
								<?php } else if($result->operator_id=='1'||$result->operator_id=='2'||$result->operator_id=='5'||$result->operator_id=='7'||$result->operator_id=='8'||$result->operator_id=='10'||$result->operator_id=='11'||$result->operator_id=='13'||$result->operator_id=='14'||$result->operator_id=='16'||$result->operator_id=='17'||$result->operator_id=='20'||$result->operator_id=='23'||$result->operator_id=='24'||$result->operator_id=='26'||$result->operator_id=='28'||$result->operator_id=='29'||$result->operator_id=='31'||$result->operator_id=='32'||$result->operator_id=='33'||$result->operator_id=='34'||$result->operator_id=='35'||$result->operator_id=='38'){?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap fill-Apre" />
								</td>
								<?php } else if($result->operator_id=='3'||$result->operator_id=='6'||$result->operator_id=='9'||$result->operator_id=='21'||$result->operator_id=='27'||$result->operator_id=='30'){?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap fill-Adth" />
								</td>
								<?php } else if($result->operator_id>='95' && $result->operator_id<='100'){?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap fill-Adexp" />
								</td>
								<?php } else if($result->operator_id=='4'||$result->operator_id=='12'||$result->operator_id=='18'||$result->operator_id=='25'||$result->operator_id=='36'||$result->operator_id=='37'||$result->operator_id=='39'||$result->operator_id=='40'||$result->operator_id=='41'||$result->operator_id=='67'||$result->operator_id=='42'||$result->operator_id=='43'||$result->operator_id=='44'||$result->operator_id=='45'||$result->operator_id=='60'||$result->operator_id=='46'||$result->operator_id=='47'||$result->operator_id=='48'||$result->operator_id=='49'||$result->operator_id=='50'||$result->operator_id=='55'||$result->operator_id=='56'||$result->operator_id=='57'||$result->operator_id=='58'||$result->operator_id=='59'||$result->operator_id=='61'||$result->operator_id=='62'||$result->operator_id=='63'||$result->operator_id=='64'||$result->operator_id=='65'||$result->operator_id=='68'||$result->operator_id=='69'||$result->operator_id=='70'||$result->operator_id=='71'||$result->operator_id=='51'||$result->operator_id=='52'||$result->operator_id=='66'||$result->operator_id=='72'||$result->operator_id=='53'||$result->operator_id=='54'||$result->operator_id=='101'||$result->operator_id=='102'||$result->operator_id=='103'||$result->operator_id=='104'){?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap fill-Autl" />
								</td>
								<?php } else{?>
								<td>
									<input type="text" size="5" id="commission_ap<?php echo $i;?>" name="commission_ap[<?php echo $i;?>]" value="<?php echo $comm_api;?>" class="fill-ap" />
								</td>
								<?php } ?>
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
								<?php if($user->user_type=='4') { ?>
								<td colspan="4"></td>
								<?php } else { ?>
								<td colspan="3"></td>
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