<?php
$request_txn_no = $recharge_id;
if($operator_info->service_type=='10') {
	$status = '0';
} else {
	$status = '8';
}
$status_details = 'Transaction Submitted';
$api_txn_no = '';
$api_status = '';
$api_status_details = '';
$operator_ref_no = '';