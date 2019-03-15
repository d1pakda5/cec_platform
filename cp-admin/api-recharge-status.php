<?php
include("../config.php");
$request_id = isset($_GET['id']) ? mysql_real_escape_string($_GET['id']) : 0;
if(isset($_GET['id']) && $_GET['id'] != '') {
	$recharge_info = $db->queryUniqueObject("SELECT recharge.*, opr.operator_name, api.api_name, user.user_id, user.company_name FROM apps_recharge recharge LEFT JOIN operators opr ON recharge.operator_id = opr.operator_id LEFT JOIN api_list api ON recharge.api_id = api.api_id LEFT JOIN apps_user user ON recharge.uid = user.uid WHERE recharge.recharge_id = '".$request_id."' ");
	if($recharge_info) {
		$recharge_id = $recharge_info->recharge_id;		
		$txn_no = $recharge_info->recharge_id;
		$uid = $recharge_info->uid;
		$api_id = $recharge_info->api_id;
		$api_name = $recharge_info->api_name;
		$service_type = $recharge_info->service_type;
		$operator_id = $recharge_info->operator_id;
		$operator_name = $recharge_info->operator_name;
		$account_no = $recharge_info->account_no;
		$amount = $recharge_info->amount;
		$surcharge = $recharge_info->surcharge;
		$status = $recharge_info->status;
		$status_details = $recharge_info->status_details;
		$request_date = $recharge_info->request_date;
		$request_txn_no = $recharge_info->request_txn_no;
		$reference_txn_no = $recharge_info->reference_txn_no;
		$api_txn_no = $recharge_info->api_txn_no;
		$response_date = $recharge_info->response_date;
		$api_status = $recharge_info->api_status;
		$api_status_details = $recharge_info->api_status_details;
		$old_operator_ref_no = $recharge_info->operator_ref_no;
		$recharge_ip = $recharge_info->recharge_ip;
		$is_refunded = $recharge_info->is_refunded;
		if($api_id == '1') {
			include(DIR . "/library/status-egpay-api.php");
		} else if($api_id == '2') {
			include(DIR . "/library/status-arroh-api.php");
		} else if($api_id == '3') {
			include(DIR . "/library/status-achariya-api.php");
		} else if($api_id == '4') {
			include(DIR . "/library/status-modem-api.php");
		} else if($api_id == '5') {
			include(DIR . "/library/status-modem-roundpay-api.php");
		} else if($api_id == '6') {
			include(DIR . "/library/status-roundpay-api.php");
		} else if($api_id == '7') {
			include(DIR . "/library/status-rechargea2z-api.php");
		} else if($api_id == '8') {
			include(DIR . "/library/status-aarav-api.php");
		} else if($api_id == '9') {
			include(DIR . "/library/status-ambika-api.php");
		} else if($api_id == '10') {
			include(DIR . "/library/status-cyberplat-api.php");
		} else if($api_id == '11') {
			include(DIR . "/library/status-offline-api.php");
		} else if($api_id == '12') {
			include(DIR . "/library/status-ajira-jio-api.php");
		} else if($api_id == '14') {
			include(DIR . "/library/status-paymentall-api.php");
		} else if($api_id == '16') {
			include(DIR . "/library/status-esure-api.php");
		}
		
		if(!empty($operator_ref_no)) {
			if($operator_ref_no != '') {
				if(preg_match('/[0-9]/', $operator_ref_no) && strlen($operator_ref_no) > 4 && strlen($operator_ref_no) < 24 ) {
					$db->query("UPDATE apps_recharge SET operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_id."' ");
				}	
			}	
		}
			
	} else {
		echo "ERROR,Invalid Recharge ID";
		exit();		
	}
	
} else {
	echo "ERROR,Invalid Transaction ID";
	exit();
}
$array['complaint_status'] = getComplaintStatusList();
$array['recharge_status'] = getRechargeStatusList();
?>
<style>
.bg-status {
	background:#36a2cf!important;
	border-color:#36a2cf!important;
	color:#fff!important;
}
.bg-status h3.box-title {
	font-size:18px!important;
}
.fancy-body-inner {
	padding:10px;
	width:100%;
	float:left;
}
.fancy-body-inner .form-group {
	margin-bottom:10px;
	height:auto;
}
.fancy-body-inner .form-control-static {
	min-height: 14px!important;
	padding-top:3px;
	padding-bottom:3px;
}
.fancy-body-inner .control-label {
	padding-top:3px;
	padding-bottom:3px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#frmComplaint").bind('submit', function(e) {
		e.preventDefault();		
		jQuery.ajax({
			type : "POST",
			cache : false,
			url : 'ajax/update-recharge-status.php',
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
<div class="box" style="width:480px;">
	<div class="box-header bg-status">
		<h3 class="box-title">API Status : Txn No <?php echo $txn_no;?></h3>
	</div>
	<div class="box-body no-padding">
		<table class="table table-condensed table-bordered">			
			<tr>
				<td>API Status</td>
				<td><?php echo $api_status;?></td>
			</tr>
			<tr>
				<td>API Status Details</td>
				<td><?php echo $api_status_details;?></td>
			</tr>	
			<tr>
				<td>API</td>
				<td><?php echo $api_name;?></td>
			</tr>
			<tr>
				<td>API Txn No</td>
				<td><?php echo $api_txn_no;?></td>
			</tr>
			<tr>
				<td>Operator Name</td>
				<td><?php echo $operator_name;?> </td>
			</tr>
			<tr>
				<td>Mobile/Account</td>
				<td><?php echo $account_no;?></td>
			</tr>
			<tr>
				<td>Amount</td>
				<td><?php echo round($amount, 2);?></td>
			</tr>			
			<tr>
				<td>Operator Ref No.</td>
				<td><?php echo $operator_ref_no;?></td>
			</tr>
			<tr>
				<td>Request Time</td>
				<td><?php echo $request_date;?></td>
			</tr>
			<tr>
				<td>Our Txn No</td>
				<td><b><?php echo $request_txn_no;?></b></td>
			</tr>
			<tr>
				<td>Response Time</td>
				<td><?php echo $response_date;?></td>
			</tr>
		</table>
	</div>
</div>