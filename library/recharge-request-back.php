<?php
if($mode == "WEB") {
	$sOperator = " opr.operator_id = '".trim($operator_code)."' ";
} else if($mode == "SMS" || $mode == "GPRS") {			
	$sOperator = " opr.operator_longcode = '".trim($operator_code)."' ";
} else if($mode == "API") {
	$sOperator = " opr.operator_code = '".trim($operator_code)."' ";
} else {
	$sOperator = " opr.operator_id = '".trim($operator_code)."' ";
}
$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id = api.api_id LEFT JOIN service_type ser ON opr.service_type = ser.service_type_id WHERE $sOperator ");
if($operator_info) {
	if($operator_info->ser_status == '1') {
		if($operator_info->api_status == '1') {
			if($operator_info->status == '1') {
				if($amount >= $operator_info->minimum_amount && $amount <= $operator_info->maximum_amount) {
					
					$denom_info = $db->queryUniqueObject("SELECT deno.*, api.api_id, api.status AS api_status FROM operators_denominations deno LEFT JOIN api_list api ON deno.api_id = api.api_id WHERE deno.operator_id = '".$operator_info->operator_id."' AND deno.status = '1' AND FIND_IN_SET ($amount, deno.amount_values) ");
					if($denom_info) {
						if($denom_info->api_status == '1') {
							$api_id = $denom_info->api_id;
						} else {
							$api_id = $operator_info->api_id;
						}
					} else {
						$api_id = $operator_info->api_id;
					}					
					
					/*
					* INSERT INTO RECHARGE TABLE
					*/					
					$sCommission = getUserCommission(trim($agent_info->mdist_id), $operator_info->operator_id, $amount);
					$isfund = $db->queryUniqueObject("SELECT wallet_id, balance, cuttoff FROM apps_wallet WHERE user_id = '".$agent_info->user_id."' ");
					if($isfund && (($isfund->balance - $isfund->cuttoff) >= ($amount + $sCommission['samount']))) {
						$status = '1';
						$db->execute("INSERT INTO `apps_recharge`(`recharge_id`, `uid`, `recharge_mode`, `api_id`, `service_type`, `operator_id`, `account_no`, `amount`, `surcharge`, `status`, `status_details`, `request_date`, `reference_txn_no`, `recharge_ip`) VALUES ('', '".$agent_info->uid."', '".$mode."', '".$api_id."', '".$operator_info->service_name."', '".$operator_info->operator_id."', '".$account."', '".$amount."', '".$sCommission['samount']."', '".$status."', '', NOW(), '".$reference_txn_no."', '".$ip."')");
						$recharge_id = $db->lastInsertedId();
						if($operator_info->service_type == '5') {
							$db->execute("INSERT INTO `apps_recharge_details`(`detail_id`, `recharge_id`, `customer_account`, `added_date`) VALUES ('', '".$recharge_id."', '".$customer_account."', NOW())");
						} else if($operator_info->service_type == '6') {							
							$db->execute("INSERT INTO `apps_recharge_details`(`detail_id`, `recharge_id`, `customer_name`, `customer_mobile`, `customer_email`, `customer_city`, `customer_account`, `billing_cycle`, `sub_division`, `billing_unit`, `pc_number`, `added_date`) VALUES ('', '".$recharge_id."', '".$customer_name."', '".$customer_mobile."', '".$customer_email."', '".$customer_city."', '".$customer_account."', '".$billing_cycle."', '".$sub_division."', '".$billing_unit."', '".$pc_number."', NOW())");
						} else if($operator_info->service_type == '8') {
							$db->execute("INSERT INTO `apps_recharge_details`(`detail_id`, `recharge_id`, `dob`, `added_date`) VALUES ('', '".$recharge_id."', '".$customer_account."' NOW())");
						}
						
						/*
						* Start Transaction
						*/
						$db->query("START TRANSACTION");
						$wallet = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->uid."' ");
						if($sCommission['surcharge'] == 'y') {
							$debit_amount = $amount + $sCommission['samount'];
						} else {
							$debit_amount = $amount - $sCommission['rtCom'];
						}
						$close_balance = $wallet->balance - $debit_amount;
						$db->query("UPDATE apps_wallet SET balance = '".$close_balance."' WHERE wallet_id = '".$wallet->wallet_id."' ");
						$ts1 = mysql_affected_rows();
						if($wallet && $ts1) {
							$commit = $db->query("COMMIT");
							if($commit) {
								$remark = "RECHARGE: $recharge_id, $account, $amount, $debit_amount";
								$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'dr', '".$debit_amount."', '".$close_balance."',  'RECHARGE', '".$recharge_id."', '".$remark."', '5', '".$agent_info->uid."')");						
								/*
								* Send to recharge
								*/
								if($api_id == '1') {
									include(DIR . "/library/egpay-recharge-api.php");
								} else if($api_id == '2') {
									include(DIR . "/library/royal-capital-recharge-api.php");
								} else if($api_id == '3') {
									include(DIR . "/library/achariya-recharge-api.php");
								} else if($api_id == '4') {
									include(DIR . "/library/modem-recharge-api.php");
								} else if($api_id == '5') {
									include(DIR . "/library/modem-roundpay-recharge-api.php");
								} else if($api_id == '6') {
									include(DIR . "/library/roundpay-recharge-api.php");
								} else if($api_id == '7') {
									include(DIR . "/library/exioms-recharge-api.php");
								} else if($api_id == '8') {
									include(DIR . "/library/pay-manthra-recharge-api.php");
								} else if($api_id == '9') {
									include(DIR . "/library/ambika-recharge-api.php");
								} else if($api_id == '10') {
									include(DIR . "/library/cyberplat-recharge-api.php");
								} else if($api_id == '11') {
									include(DIR . "/library/offline-recharge-api.php");
								}
								
								if($status == '0') {
									/*
									* Update recharge response and params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
									*/
									$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', response_date = NOW(), request_txn_no = '".$request_txn_no."', api_txn_no = '".$api_txn_no."', api_status = '".$api_status."', api_status_details = '".$api_status_details."', operator_ref_no = '".$operator_ref_no."' WHERE recharge_id = '".$recharge_id."' ");
									
									/*
									* Update commission for all parent users
									*/
									if($sCommission['rtCom'] > '0') {
										$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->uid."', '".$sCommission['rtCom']."', '".$close_balance."', NOW())");
									}
																
									//Commission Distributor
									if($sCommission['dsCom'] > '0') {
										$db->query("START TRANSACTION");
										$rDs = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->dist_id."' ");
										if($rDs) {										
											$ds_close_balance = $rDs->balance + $sCommission['dsCom'];
											$db->execute("UPDATE apps_wallet SET balance='".$ds_close_balance."' WHERE wallet_id='".$rDs->wallet_id."'");
											$ts2 = mysql_affected_rows();
											$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->dist_id."', '".$sCommission['dsCom']."', '".$ds_close_balance."', NOW())");
											if($ts2) {
												$commit = $db->query("COMMIT");
											} else {
												$db->query("ROLLBACK");
											}
										} else {
											$db->query("ROLLBACK");
										}
									}
									
									//Commission Master Distributor
									if($sCommission['mdCom'] > '0') {
										$db->query("START TRANSACTION");
										$rMd = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->mdist_id."' ");
										if($rMd) {										
											$md_close_balance = $rMd->balance + $sCommission['mdCom'];
											$db->execute("UPDATE apps_wallet SET balance='".$md_close_balance."' WHERE wallet_id='".$rMd->wallet_id."'");
											$ts3 = mysql_affected_rows();
											$db->execute("INSERT INTO `commission_details`(`commission_id`, `recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('', '".$recharge_id."', '".$agent_info->mdist_id."', '".$sCommission['mdCom']."', '".$md_close_balance."', NOW())");
											if($ts3) {
												$commit = $db->query("COMMIT");
											} else {
												$db->query("ROLLBACK");
											}										
										} else {
											$db->query("ROLLBACK");
										}
									}
									//Error Code: Recharge Success
									$result_code = '300';
								
								} else if ($status == '2') {								
									/*
									* revert recharge amount to agent
									*/
									$db->query("START TRANSACTION");													
									$wallet1 = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid = '".$agent_info->uid."' ");													
									$new_close_balance = $wallet1->balance + $debit_amount;
									$db->execute("UPDATE apps_wallet SET balance = '".$new_close_balance."' WHERE wallet_id = '".$wallet1->wallet_id."' ");
									$ts4 = mysql_affected_rows();
									if($wallet1 && $ts4) {													
										$commit = $db->query("COMMIT");	
										if($commit) {													
											$remark_new = "REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
											$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$new_close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");												
											/*
											* Update response status for failed recharge
											*/
											$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', response_date = NOW(), request_txn_no = '".$request_txn_no."', api_txn_no = '".$api_txn_no."', api_status = '".$api_status."', api_status_details = '".$api_status_details."', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
											//Error Code: Recharge Failed											
											$result_code = '302';
										}
											
									} else {
										$db->query("ROLLBACK");
										//ERROR Code: Server Error";
										$result_code = '301';
									}
								
								} else if ($status == '7') {						
									$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', request_txn_no = '".$request_txn_no."', api_txn_no = '".$api_txn_no."', api_status = '".$api_status."', api_status_details = '".$api_status_details."' WHERE recharge_id = '".$recharge_id."' ");
									//Error Code: Recharge Processed
									$result_code = '307';
											
								} else if ($status == '8') {						
									$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', request_txn_no = '".$request_txn_no."', api_txn_no = '".$api_txn_no."', api_status = '".$api_status."', api_status_details = '".$api_status_details."' WHERE recharge_id = '".$recharge_id."' ");
									//Error Code: Recharge Submitted
									$result_code = '308';
											
								} else if ($status == '1') {						
									$db->execute("UPDATE apps_recharge SET status = '".$status."', status_details = '".$status_details."', request_txn_no = '".$request_txn_no."', api_txn_no = '".$api_txn_no."', api_status = '".$api_status."', api_status_details = '".$api_status_details."' WHERE recharge_id = '".$recharge_id."' ");
									//Error Code: Recharge Pending
									$result_code = '301';
											
								} else {
									/*
									* Update response status for other status or having no status
									*/
									//Error Code: Recharge Pending
									$result_code = '301';
								}
								
								/*
								* Insert recharge response log
								*/
								if(isset($output)) {
									$db->execute("INSERT INTO `apps_response_log`(`log_id`, `txn_no`, `api_id`, `http_response_code`, `http_response_content`, `update_date`) VALUES ('', '".$recharge_id."', '".$api_id."', '".$http_code."', '".$output."', NOW())");
								}
							}
														
						} else {
							$db->query("ROLLBACK");
							$db->execute("UPDATE apps_recharge SET status = '6', status_details = 'Transaction cancelled', response_date = NOW() WHERE recharge_id = '".$recharge_id."' ");
							//Error Code: Request Failed";
							$result_code = '306';
						}
						
					} else {
						//Error Code: Insufficiant Amount
						$result_code = '319';
					}
					
				} else {
					//Error Code: Invalid Amount
					$result_code = '318';
				}
			} else {
				//Error Code: Operator Downtime. Try Later
				$result_code = '317';
			}
		} else {
			//Error Code: API Downtime. Try Later
			$result_code = '316';
		}
	} else {
		//Error Code: Service Downtime. Try Later
		$result_code = '315';
	}
} else {
	//Error Code: Invalid Operator
	$result_code = '314';
}