<?php
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include('../system/class.pagination.php');
$tbl = new ListTable();

$sWhere = "WHERE usr.uid!='0' ";
if(isset($_GET['type']) && $_GET['type']!='') {
	$sWhere .= "AND usr.user_type='".mysql_real_escape_string($_GET['type'])."' ";
} else {
	$sWhere .= "AND usr.user_type IN (1,3,4,5) ";
}
if(isset($_GET['state']) && $_GET['state']!='') {
	$sWhere .= "AND (usr.states='".mysql_real_escape_string($_GET['state'])."' OR kyc.state='".mysql_real_escape_string($_GET['state'])."') ";
}
if(isset($_GET['s']) && $_GET['s']!='') {
	$string = mysql_real_escape_string($_GET['s']);
	$sWhere .= "AND (usr.fullname LIKE '%".$string."%' OR usr.company_name LIKE '%".$string."%' OR usr.uid LIKE '%".$string."%' OR usr.mobile LIKE '%".$string."%' OR usr.username LIKE '%".$string."%' OR kyc.firstname LIKE '%".$string."%' OR kyc.middlename LIKE '%".$string."%' OR kyc.lastname LIKE '%".$string."%' OR kyc.fathersname LIKE '%".$string."%' OR kyc.mothersname LIKE '%".$string."%' OR kyc.mobile LIKE '%".$string."%' OR kyc.phone LIKE '%".$string."%' OR kyc.email LIKE '%".$string."%' OR kyc.city LIKE '%".$string."%' OR kyc.pincode LIKE '%".$string."%' OR kyc.adrprooftypeno LIKE '%".$string."%' OR kyc.businessname LIKE '%".$string."%' OR kyc.pancard LIKE '%".$string."%' OR kyc.aadhaar LIKE '%".$string."%' OR kyc.gstin LIKE '%".$string."%') ";
}
if(isset($_GET['status']) && $_GET['status'] != '') {
	$sWhere .= "AND usr.status='".mysql_real_escape_string($_GET['status'])."' ";
} else {
	$sWhere .= "AND usr.status!='9' ";
}
if(isset($_GET['kyc']) && $_GET['kyc']!='') {
	$sWhere .= "AND usr.is_kyc='".mysql_real_escape_string($_GET['kyc'])."' ";
}
$statement = "apps_user usr LEFT JOIN userskyc kyc ON usr.uid=kyc.uid $sWhere ORDER BY usr.user_id DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (isset($_GET["show"]) && $_GET["show"] != '' ? $_GET["show"] : 100);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('kyc-list.php');

$meta['title'] = "Users KYC";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />
<script>
jQuery(document).ready(function() {
	jQuery(".sendSms").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
});
function doExcel(){
	window.location='excel/user.php';
}
</script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Users KYC</div>
		</div>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Users</h3>
			</div>			
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get">
						<div class="col-sm-4">
							<div class="form-group">
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search" class="form-control input-sm">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="type" id="type" class="form-control input-sm">
									<option value="">Select</option>
									<option value="1"<?php if(isset($_GET['type']) && $_GET['type']=="1") { ?> selected="selected"<?php } ?>>API USER</option>
									<option value="3"<?php if(isset($_GET['type']) && $_GET['type']=="3") { ?> selected="selected"<?php } ?>>MASTER DISTRIBUTOR</option>
									<option value="4"<?php if(isset($_GET['type']) && $_GET['type']=="4") { ?> selected="selected"<?php } ?>>DISTRIBUTOR</option>
									<option value="5"<?php if(isset($_GET['type']) && $_GET['type']=="5") { ?> selected="selected"<?php } ?>>RETAILER</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<select name="state" id="state" class="form-control input-sm">
									<option value="">Select state</option>
									<?php $query = $db->query("SELECT * FROM states");
									while($row = $db->fetchNextObject($query)) { ?>
									<option value="<?php echo $row->states;?>"<?php if(isset($_GET['state']) && $_GET['state']==$row->states) { ?> selected="selected"<?php } ?>><?php echo $row->states;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="kyc" class="form-control input-sm">
									<option value="">Kyc</option>
									<option value="1"<?php if(isset($_GET['kyc']) && $_GET['kyc']=="1") { ?> selected="selected"<?php } ?>>Submitted</option>
									<option value="0"<?php if(isset($_GET['kyc']) && $_GET['kyc']=="0") { ?> selected="selected"<?php } ?>>Not Submitted</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="status" class="form-control input-sm">
									<option value="">Status</option>
									<option value="1"<?php if(isset($_GET['status']) && $_GET['status']=="1") { ?> selected="selected"<?php } ?>>ACTIVE</option>
									<option value="0"<?php if(isset($_GET['status']) && $_GET['status']=="0") { ?> selected="selected"<?php } ?>>INACTIVE</option>
									<option value="9"<?php if(isset($_GET['status']) && $_GET['status']=="9") { ?> selected="selected"<?php } ?>>TRASH</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<select name="show" class="form-control input-sm">
									<option value="">Show</option>
									<option value="50" <?php if(isset($_GET['show'])&&$_GET['show']=="50") { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if(isset($_GET['show'])&&$_GET['show']=="100") { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if(isset($_GET['show'])&&$_GET['show']=="250") { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if(isset($_GET['show'])&&$_GET['show']=="500") { ?> selected="selected"<?php } ?>>500</option>
								</select>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								<input type="submit" value="Filter" class="btn btn-warning btn-sm">
							</div>
						</div>
					</form>
				</div>
				<table class="table table-condensed table-striped table-bordered">
					<thead>
						<tr>
							<th width="2%">S.</th>
							<th width="1%"><input type="checkbox" id="checkall"></th>
							<th>UID</th>
							<th>Name</th>
							<th>Type</th>
							<th>Mobile</th>
							<th>AADHAR</th>
							<th>PAN</th>
							<th>GSTIN</th>
							<th>State</th>
							<th>City</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php						
						$query = $db->query("SELECT usr.user_id, usr.uid, usr.company_name, usr.user_type, usr.mobile, usr.is_kyc, usr.status, kyc.aadhaar, kyc.pancard, kyc.gstin, kyc.state, kyc.city  FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($row = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><input type="checkbox" name="oprid[]" class="checkitems" value="<?php echo $row->operator_id;?>"></td>
							<td><a href="kyc.php?uid=<?php echo $row->uid;?>" title="User Kyc" target="_blank"><?php echo $row->uid;?></a></td>
							<td><?php echo strtoupper($row->company_name);?></td>
							<td><?php echo getUserType($row->user_type);?></td>
							<td><?php echo $row->mobile;?></td>
							<td><?php echo $row->aadhaar;?></td>
							<td><?php echo $row->pancard;?></td>
							<td><?php echo $row->gstin;?></td>
							<td><?php echo $row->state;?></td>
							<td><?php echo $row->city;?></td>
							<td align="center">
								<?php if($row->is_kyc=='1') {?>
									<i class="fa fa-map-marker text-green"></i>
								<?php }?>
							</td>
							<td align="center">
								<?php if($row->status == '1') {?>
									<i class="fa fa-check-circle text-green"></i>
								<?php }else {?>
									<i class="fa fa-times-circle text-red"></i>
								<?php }?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#checkall").click( function() {
		$(".checkitems").prop("checked", $(this).is(":checked"));
	});
});
</script>
<?php include('footer.php'); ?>