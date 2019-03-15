<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
session_start();
if(!isset($_SESSION['admin'])) {
	header("location:index.php");
	exit();
}
include('../config.php');
include("../system/class.pagination.php");
$error = isset($_GET['error']) && $_GET['error'] != '' ? mysql_real_escape_string($_GET['error']) : 0;
$tbl = new ListTable();

$from = isset($_GET["f"]) && $_GET["f"]!='' ? mysql_real_escape_string($_GET["f"]) : date("Y-m-d");
$to = isset($_GET["t"]) && $_GET["t"]!='' ? mysql_real_escape_string($_GET["t"]) : date("Y-m-d");
$aFrom = date("Y-m-d 00:00:00", strtotime($from));
$aTo = date("Y-m-d 23:59:59", strtotime($to));

$sWhere = "WHERE recharge.request_date BETWEEN DATE_SUB(NOW(), INTERVAL 2 MONTH) AND '".$aTo."' ";
// $sWhere = "WHERE 1 ";
//  $sWhere = "WHERE recharge.recharge_id!=''";
if(isset($_GET['s']) && $_GET['s']!='') {
	$aStr = mysql_real_escape_string($_GET['s']);
	$sWhere .= " AND (recharge.recharge_id='".$aStr."' OR recharge.account_no='".$aStr."' OR recharge.operator_ref_no='".$aStr."' OR recharge.api_txn_no='".$aStr."' OR user.company_name LIKE '%".$aStr."%') ";
}
else
{
    $sWhere .= " AND recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}

if(isset($_GET["f"]) && $_GET["f"]!='') {
    $sWhere .= " AND recharge.request_date BETWEEN '".$aFrom."' AND '".$aTo."' ";
}

if(isset($_GET['opr']) && $_GET['opr']!='') {
	$sWhere .= " AND recharge.operator_id='".mysql_real_escape_string($_GET['opr'])."' ";
}
if(isset($_GET['api']) && $_GET['api']!='') {
	$sWhere .= " AND recharge.api_id='".mysql_real_escape_string($_GET['api'])."' ";
}
if(isset($_GET['uid']) && $_GET['uid']!='') {
	$sWhere .= " AND recharge.uid='".mysql_real_escape_string($_GET['uid'])."' ";
}
if(isset($_GET['status']) && $_GET['status']!='') {
	$sWhere .= " AND recharge.status='".mysql_real_escape_string($_GET['status'])."' ";
}
if(isset($_GET['status_1']) && $_GET['status_1']!='') {
	$sWhere .= " AND recharge.status_1='".mysql_real_escape_string($_GET['status_1'])."' ";
}
if(isset($_GET['status_2']) && $_GET['status_2']!='') {
	$sWhere .= " AND recharge.status_2='".mysql_real_escape_string($_GET['status_2'])."' ";
}
if(isset($_GET['status_3']) && $_GET['status_3']!='') {
	$sWhere .= " AND recharge.status_3='".mysql_real_escape_string($_GET['status_3'])."' ";
}
if(isset($_GET['org_status']) && $_GET['org_status']!='') {
	$sWhere .= " AND recharge.org_status='".mysql_real_escape_string($_GET['org_status'])."' ";
}

if(isset($_GET['status_changed']) && $_GET['status_changed']!='') {
	$sWhere .= " AND recharge.is_status_changed='".mysql_real_escape_string($_GET['status_changed'])."'";
}

if(isset($_GET['mode']) && $_GET['mode']!='') {
	$sWhere .= " AND recharge.recharge_mode='".mysql_real_escape_string($_GET['mode'])."' ";
}

if(isset($_GET['api_complaint']) && $_GET['api_complaint']!='') {
	$sWhere .= " AND recharge.is_api_complaint='".mysql_real_escape_string($_GET['api_complaint'])."' ";
}

if(isset($_GET['op_ref']) && $_GET['op_ref']!='') {
	$sWhere .= " AND recharge.operator_ref_no='' AND recharge.status not in(2,3,4) ";
}

$statement = "apps_recharge recharge LEFT JOIN operators opr ON recharge.org_operator_id=opr.operator_id LEFT JOIN apps_user user ON recharge.uid=user.uid $sWhere ORDER BY recharge.request_date DESC";

//Pagination
$paged = (int) (!isset($_GET["paged"]) ? 1 : $_GET["paged"]);
$limit = (int) (!isset($_GET["show"]) ? 100 : $_GET["show"]);
$startpoint = ($paged * $limit) - $limit;
$scnt = ($paged * $limit) - $limit + 1;
$self = $tbl->remove_page_param('rpt-recharge.php');

$array['recharge_status'] = getRechargeStatusList();
$array['recharge_status_multi'] = getRechargeStatusListMulti();
$meta['title'] = "Recharge";
include('header.php');
?>
<script type="text/javascript" src="../js/fancybox2/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/fancybox2/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../js/fancybox2/jquery.fancybox.css?v=2.1.5" media="screen" />

<script>
jQuery(document).ready(function() {
    
    // alert($(".click_it").attr("val"));
});


jQuery(document).ready(function() {
    
 setTimeout(function() {
  location.reload();
}, 1800000);
    
	jQuery('#from').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery('#to').datepicker({
		format: 'yyyy-mm-dd'
	});
	jQuery(".fancyDetails").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	jQuery(".fancyAction").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
	jQuery(".fancyStatus").fancybox({
		closeClick	: false,
		helpers   : { 
   			overlay : {closeClick: false}
  		}
	});
});
function doExcel(){
	var from = jQuery('#from').val();
	var to = jQuery('#to').val();
	var opr = jQuery('#opr').val();
	var api = jQuery('#api').val();
	var status = jQuery('#status').val();
	var mode = jQuery('#mode').val();
	var uid = jQuery('#uid').val();
	var api_complaint = jQuery('#api_complaint').val();
	var op_ref = jQuery('#op_ref').val();
	window.location='excel/recharge.php?from='+from+'&to='+to+'&opr='+opr+'&api='+api+'&status='+status+'&mode='+mode+'&uid='+uid+'&api_complaint='+api_complaint+'&op_ref='+op_ref;
}
</script>
<link rel="stylesheet" href="../js/datepicker/datepicker.css">
<script src="../js/datepicker/bootstrap-datepicker.js"></script>
<div class="content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="page-title">Reports <small>/ Recharge</small></div>
		</div>
	
		<?php if($error == 4) { ?>
		<div class="alert alert-success">
			<i class="fa fa-warning"></i> API Complaint Closed Successfully.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php } else if($error == 3) { ?>
		<div class="alert alert-danger">
			<i class="fa fa-check"></i> Something Went Wrong!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php }?>
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-list"></i> List Recharge</h3>
			</div>	
			<div class="box-body no-padding min-height-480">
				<div class="box-filter padding-20">
					<form method="get" id="rptRecharge" class="">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>From</label>
										<input type="text" size="8" name="f" id="from" value="<?php if(isset($_GET['f'])) { echo $_GET['f']; }?>" placeholder="From Date" class="form-control">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>To</label>
										<input type="text" size="8" name="t" id="to" value="<?php if(isset($_GET['t'])) { echo $_GET['t']; }?>" placeholder="To Date" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Operator</label>
								<select name="opr" id="opr" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT operator_id,operator_name FROM operators ORDER BY service_type,operator_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->operator_id;?>" <?php if(isset($_GET['opr']) && $_GET['opr']==$result->operator_id) {?> selected="selected"<?php } ?>><?php echo $result->operator_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>API</label>
								<select name="api" id="api" class="form-control">
									<option value=""></option>
									<?php
									$query = $db->query("SELECT api_id,api_name FROM api_list WHERE status = '1' ORDER BY api_name ASC ");
									while($result = $db->fetchNextObject($query)) {	?>
									<option value="<?php echo $result->api_id;?>" <?php if(isset($_GET['api']) && $_GET['api']==$result->api_id) {?> selected="selected"<?php } ?>><?php echo $result->api_name;?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status</label>
								<select name="status" id="status" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge_status'] as $data) { ?>
										<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status']) && $_GET['status'] == $data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Mode</label>
								<select name="mode" id="mode" class="form-control">
									<option value=""></option>
									<option value="API" <?php if(isset($_GET['mode']) && $_GET['mode'] == "API"){?>selected="selected"<?php } ?>>API</option>
									<option value="WEB" <?php if(isset($_GET['mode']) && $_GET['mode'] == "WEB"){?>selected="selected"<?php } ?>>WEB</option>
									<option value="SMS" <?php if(isset($_GET['mode']) && $_GET['mode'] == "SMS"){?>selected="selected"<?php } ?>>SMS</option>
									<option value="GPRS" <?php if(isset($_GET['mode']) && $_GET['mode'] == "GPRS"){?>selected="selected"<?php } ?>>GPRS</option>
								</select>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="form-group">
								<label>User UID</label>
								<input type="text" size="8" name="uid" id="uid" value="<?php if(isset($_GET['uid'])) { echo $_GET['uid']; }?>" placeholder="UID" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Search</label>
								<input type="text" name="s" value="<?php if(isset($_GET['s'])) { echo $_GET['s']; }?>" placeholder="Search Txn/Mobile/Name" class="form-control">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>API Complaint</label>
								<select name="api_complaint" id="api_complaint" class="form-control">
									<option value=""></option>
									<option value="1" <?php if(isset($_GET['api_complaint']) && $_GET['api_complaint'] == 1){?>selected="selected"<?php } ?>>Complaints Registered</option>
									<option value="2" <?php if(isset($_GET['api_complaint']) && $_GET['api_complaint'] == 2){?>selected="selected"<?php } ?>>Complaints Closed</option>
									
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Ref NO</label>
								<select name="op_ref" id="op_ref" class="form-control">
									<option value=""></option>
									<option value="1" <?php if(isset($_GET['op_ref']) && $_GET['op_ref'] == 1){?>selected="selected"<?php } ?>>Color</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Original Status</label>
								<select name="org_status" id="org_status" class="form-control">
									<option value=""></option>
									<option value="2" <?php if(isset($_GET['org_status']) && $_GET['org_status'] == '2'){?>selected="selected"<?php } ?>><?php echo "Failure" ?></option>
								
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status Changed</label>
								<select name="status_changed" id="status_changed" class="form-control">
									<option value=""></option>
									<option value="1" <?php if(isset($_GET['status_changed']) && $_GET['status_changed'] == '1'){?>selected="selected"<?php } ?>><?php echo "Status Changed" ?></option>
								
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status 1</label>
								<select name="status_1" id="status_1" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge_status'] as $data) { ?>
										<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status_1']) && $_GET['status_1'] == $data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status 2</label>
								<select name="status_2" id="status_2" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge_status'] as $data) { ?>
										<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status_2']) && $_GET['status_2'] == $data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Status 3</label>
								<select name="status_3" id="status_3" class="form-control">
									<option value=""></option>
									<?php foreach($array['recharge_status'] as $data) { ?>
										<option value="<?php echo $data['id'];?>" <?php if(isset($_GET['status_3']) && $_GET['status_3'] == $data['id']){?>selected="selected"<?php } ?>><?php echo $data['status'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Show</label>
								<select name="show" class="form-control">
									<option value="10" <?php if($limit == '10') { ?> selected="selected"<?php } ?>>10</option>
									<option value="25" <?php if($limit == '25') { ?> selected="selected"<?php } ?>>25</option>
									<option value="50" <?php if($limit == '50') { ?> selected="selected"<?php } ?>>50</option>
									<option value="100" <?php if($limit == '100') { ?> selected="selected"<?php } ?>>100</option>
									<option value="250" <?php if($limit == '250') { ?> selected="selected"<?php } ?>>250</option>
									<option value="500" <?php if($limit == '500') { ?> selected="selected"<?php } ?>>500</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								<input type="submit" value="Filter" class="btn btn-warning">
								<button type="button" onclick="doExcel('rptRecharge')" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Excel</button>
							</div>
						</div>
					</form>
				</div>
				<table class="table table-striped table-condensed-sm">
					<thead>
						<tr>
							<th width="1%">S.</th>
							<th width="1%">Mode</th>
							<th width="8%">Date</th>
							<th>Txn No</th>
							<th>User</th>
							<th>Operator</th>
							<th>Mobile</th>
							<th>Amt</th>
							<th>Sc</th>
							<th>Ref No</th>
							<th width="1%">1st</th>
							<th width="1%">2nd</th>
							<th width="1%">3rd</th>
							<th>Status</th>
							<th>Orgnl Status</th>
							<th>A</th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
							<th width="1%"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						
						$query = $db->query("SELECT recharge.*, opr.operator_name, user.company_name,user.user_id FROM {$statement} LIMIT {$startpoint}, {$limit} ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%' align='center'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						<tr>
							<td align="center"><?php echo $scnt++;?></td>
							<td><?php echo $result->recharge_mode;?></td>
							<td><?php echo date("d/m/Y H:i:s", strtotime($result->request_date));?></td>
							<?php if($result->is_status_changed=='1')
							{ ?>
							<td style="background-color:#98ffaf"><?php echo $result->recharge_id;?></td>
							<?php } else{ ?>
							<td><?php echo $result->recharge_id;?></td>
							<?php }?>
							<td><?php echo $result->company_name;?> (<?php echo $result->uid;?>)</td>
							<?php
							$opr_info = $db->queryUniqueObject("SELECT operator_name FROM operators where operator_id='".$result->operator_id."' ");
							
							if($result->org_operator_id==0||$result->org_operator_id=="")
							{?>
							 <td><?php echo $opr_info->operator_name;?></td>
							<?php
							}
							else
							{?>
							 <td><?php echo $result->operator_name;?></td>
							<?php }
							?>
							
							<td><?php echo $result->account_no;?></td>	
							<td align="right"><?php echo round($result->amount,2);?></td>
							<td align="right"><?php echo round($result->surcharge,2);?></td>
							<?php 
							if($result->operator_ref_no!=""||null)
							{
				 			$qr= $db->queryUniqueValue("select count(operator_ref_no) from apps_recharge where operator_ref_no='". $result->operator_ref_no."' AND status not IN('2','3','4') And request_date between DATE_SUB('".$aFrom."', INTERVAL 7 DAY) and '".$aFrom."' ");
							}
							if($result->operator_ref_no=="" && ($result->status=='0'|| $result->status=='1' || $result->status=='5'||$result->status=='6'|| $result->status=='7' || $result->status=='8')){
								?>
						    <td style="background-color: #b7b7e0"><?php echo $result->operator_ref_no;?></td>
						    <?php }
						    else if($qr>1)
							{
							?>
							<td style="background-color: #fbb"><?php echo $result->operator_ref_no;?></td>
							<?php } else {?>
							<td><?php echo $result->operator_ref_no;?></td>
							<?php }?>
							<td>
								<?php $rch_status = getRechargeStatusArray($array['recharge_status_multi'],$result->status_1); ?>
								<a class="label <?php echo $rch_status[1];?>" ><?php echo $rch_status[0];?></a>
							</td>
							<td>
								<?php $rch_status = getRechargeStatusArray($array['recharge_status_multi'],$result->status_2); ?>
								<a class=" label <?php echo $rch_status[1];?>" href="rpt-api-callback.php?s=<?php echo $result->recharge_id;?>" target="_blank" ><?php echo $rch_status[0];?></a>
							</td>
							<td>
								<?php $rch_status = getRechargeStatusArray($array['recharge_status_multi'],$result->status_3); ?>
								<a class="label <?php echo $rch_status[1];?>" href="rpt-api-callback.php?s=<?php echo $result->recharge_id;?>" target="_blank" ><?php echo $rch_status[0];?></a>
							</td>
							<td>
								<?php $rch_status = getRechargeStatusArray($array['recharge_status'],$result->status); ?>
								<a class="fancyStatus fancybox.ajax label <?php echo $rch_status[1];?>" href="recharge-status-update.php?id=<?php echo $result->recharge_id;?>" title="Update Recharge Status" class=""><?php echo $rch_status[0];?></a>
							</td>
							<td><?php echo getRechargeStatusLabel($array['recharge_status'],$result->org_status);?></td>
								
							<td><?php echo $result->api_id;?></td>	
							<td><a class="fancyDetails fancybox.ajax" href="recharge-details.php?id=<?php echo $result->recharge_id;?>">
								<img src="../images/plus.png" /></a></td>
								<?php
								if($result->api_id==2 && $result->status==1)
								{
								    $content=$db->queryUniqueObject("select * from apps_reverse_response log LEFT JOIN api_list api ON log.api_id = api.api_id where  log.reverse_response_content LIKE '%".$result->recharge_id."%' and log.api_id =2 ");
								    if($content)
								    {
								        $data=$content->reverse_response_content;
								        $recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge where recharge_id = '".$result->recharge_id."' ");
                                        if (preg_match('/status=success/',$data))
                                        {
                                           $db->query("UPDATE apps_recharge SET status = '0' WHERE recharge_id = '".$result->recharge_id."' ");
                                        } 
                                        else if (preg_match('/status=failure/',$data))
                                        {
                                           $db->query("UPDATE apps_recharge SET status = '2' WHERE recharge_id = '".$result->recharge_id."' ");
                                        } 
								//         $data_url=str_replace("; ","&",$data);
								//             $final=rtrim($data_url,"&");
								        
								// $hitUrl = 'http://99-604-99-605.com/arroh-response.php'.'?'.$final;
                                // echo $hitUrl.'<br>';
        				//     $ch = curl_init();
        				// 	curl_setopt($ch, CURLOPT_URL, $hitUrl); 
        				// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        				// 	curl_exec ($ch);
        				// 	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        				// 	curl_close($ch);
								    }
								}
								
								?>
								
								
								
								<?php
// 								if($result->api_id==9)
// 								{
// 							 if($result!="")
// 								{
// 									$recharge_info = $db->queryUniqueObject("SELECT * FROM apps_recharge where recharge_id = '".$result->recharge_id."' ");
									
// 									$request_txn_no = $recharge_info->recharge_id;
									
// 									$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&csagentid=".$request_txn_no."&fmt=Json";
//                                 	$ch = curl_init();
//                                 	curl_setopt($ch, CURLOPT_URL, $url);
//                                 	curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
//                                 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                                 	$output = curl_exec($ch);
//                                 	curl_close($ch);
//                         		if($output !== false) {
                        		
//                         		if(isJson($output)) {	
//                         			$json = json_decode($output, true);			
//                         		} else {
//                         			libxml_use_internal_errors(true);
//                         			$json = json_decode(json_encode((array) simplexml_load_string($output)), 1);
//                         		}
                        					
//                         		$api_status = isset($json['STATUS']) && $json['STATUS'] != '' ? $json['STATUS'] : '';
//                         		$account = isset($json['MOBILE']) && $json['MOBILE'] != '' ? $json['MOBILE'] : '';
//                         		$amount = isset($json['AMOUNT']) && $json['AMOUNT'] != '' ? $json['AMOUNT'] : '';
//                         		$api_txn_no = isset($json['RPID']) && $json['RPID'] != '' ? $json['RPID'] : '';
//                         		$operator_ref_no = isset($json['OPID']) && $json['OPID'] != '' ? $json['OPID'] : '';
//                         		$api_status_details = isset($json['MSG']) && $json['MSG'] != '' ? $json['MSG'] : '';	
//                         		$status='1';
//                         		if($api_status=="SUCCESS")
//                         		{
//                         		    $status='0';
                        		    
//                         		}
//                         		if($api_status=="FAILED")
//                         		{
//                         		    $status='2';
                        		    
//                         		}
                        		
//                         			$db->query("UPDATE apps_recharge SET status = '".$status."' WHERE recharge_id = '".$result->recharge_id."' ");
//                         	}
//                     	if(!empty($operator_ref_no)) {
// 			if($operator_ref_no != '') {
// 				if(preg_match('/[0-9]/', $operator_ref_no) && strlen($operator_ref_no) > 4 && strlen($operator_ref_no) < 24 ) {
// 				     echo $operator_ref_no." ". $status. "<br>";
// 					$db->query("UPDATE apps_recharge SET operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$result->recharge_id."' ");
// 				}	
// 			}	
// 		}
//                     		}
//                     		if($result->recharge_mode=="API")
//                     		{ 
//                     		    $setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE user_id='".$result->user_id."' ");
//                     		    $reference_txn_no=$result->reference_txn_no;
//                     				if($setting_info->reverse_url!='') {
                    				
//                     					$url_txid = $result->recharge_id;
//                     				    $url_status = $api_status;
//                     				    if($api_status == 'SUCCESS') {
//                     						$url_opref = $operator_ref_no;
//                     						$url_msg = "Transaction Successful Amount Debited";
//                     					} else {
                    					    
//                                             	$url_opref = "NA";
                                						
//                                 						$url_msg = "Transaction Failed Amount Reversed";
                                					
//                     					        }
//                     		}
                    		
//                     		$explodUrl = explode('?',$setting_info->reverse_url);				
//         					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$url_opref, 'msg'=>$url_msg, 'usertxn'=>$reference_txn_no));
        					
//         					$ch = curl_init();
//         					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
//         					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         					curl_exec ($ch);
//         					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         					curl_close($ch);
        					
//         					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, `added_date`) VALUES ('', '".$result->uid."', '".$result->api_id."', '".$hitUrl."', '".$http_code."', NOW())");
					
								    
// 								}
// 								}
                    		
                    		
                    		?>
							
							<td><a class="fancyStatus fancybox.ajax" href="api-recharge-status.php?id=<?php echo $result->recharge_id;?>">
								<img src="../images/api.png" /></a></td>
							
							<td>
								<?php if($result->is_refunded == 'y') { ?>
									<?php if($result->is_complaint == 'n') { ?>
									<a class="fancyStatus fancybox.ajax" href="recharge-refund.php?id=<?php echo $result->recharge_id;?>" title="Revert Amount"><img src="../images/revert_1.png" /></a>
									<?php } else { ?>
									<img src="../images/comp_6.png" />
									<?php } ?>
								<?php } else { ?>
									<?php if($result->is_complaint == 'c') { ?>
									<img src="../images/comp_6.png" />
									<?php } else if($result->is_complaint == 'y') { ?>
									<a class="fancyStatus fancybox.ajax" href="recharge-refund.php?id=<?php echo $result->recharge_id;?>" title="Complaint Refund">
									<img src="../images/comp_1.png" /></a>
									<?php } else if($result->is_complaint == 'n') { ?>
									<a class="fancyStatus fancybox.ajax" href="recharge-refund.php?id=<?php echo $result->recharge_id;?>" title="Quick Refund">
									<img src="../images/comp_0.png" /></a>
									<?php } ?>
								<?php } ?>
							</td>
							<td>
								
									<?php if($result->is_api_complaint == 0) { ?>
									<a class="fancyStatus fancybox.ajax" href="register-api-complaint.php?id=<?php echo $result->recharge_id;?>" title="Register Api Complaint"><i class="fa fasize fa-question-circle"></i></a>
									<?php } else if($result->is_api_complaint == 1) { ?>
									<a  href="close-api-complaint.php?status=1&id=<?php echo $result->recharge_id;?>" title="Close Api Complaint"><i class="fa fasize-red fa-question-circle"></i></a>
									
									<?php } else if($result->is_api_complaint == 2) { ?>
										<a class="fancyStatus fancybox.ajax"  href="register-api-complaint.php?status=2&id=<?php echo $result->recharge_id;?>" title="Closed Api Complaint"><i class="fa fasize-yellow fa-question-circle"></i></a>
									<?php } ?>
							
									
							</td>
						</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<?php $qry = $db->query("SELECT SUM(recharge.amount) AS totalRecharge, SUM(recharge.surcharge) AS totalSurcharge FROM {$statement}");
						$row = $db->fetchNextObject($qry); ?>
						<tr>
							<td align="right" colspan="7"><b>Total</b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalRecharge,2);?></b></td>
							<td align="right"><b class="text-red"><?php echo round($row->totalSurcharge,2);?></b></td>
							<td colspan="5"></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="paginate">
			<?php echo $tbl->pagination($statement,$limit,$paged,$self);?>	
		</div>
	</div>
</div>
<?php include('footer.php'); ?>