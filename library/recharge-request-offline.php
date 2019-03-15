<?php
if($mode=="WEB") {
	$sOperator = "opr.operator_id='".trim($operator_code)."' ";
} else if($mode=="SMS" || $mode=="GPRS") {			
	$sOperator = "opr.operator_longcode='".trim($operator_code)."' ";
} else if($mode=="API") {
	$sOperator = "opr.operator_code='".trim($operator_code)."' ";
} else {
	$sOperator = "opr.operator_id='".trim($operator_code)."' ";
}
//

$operator_info_old = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.username, api.password, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE $sOperator ");

$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.username, api.password, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE $sOperator ");

if($operator_info) {
	
	if($operator_info->is_express!='0') {
		$operator_info_new = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_id='".trim($operator_info->is_express)."' AND opr.status='1' ");
		if($operator_info_new) {
			$operator_info = $operator_info_new;
		}
	}
	
	$duplicate_info = $db->queryUniqueObject("SELECT recharge_id FROM apps_recharge WHERE account_no='".$account."' AND amount='".$amount."' AND operator_id='".trim($operator_info->operator_id)."' AND (status='0' OR status='1' OR status='7' OR status='8') AND (request_date > DATE_SUB(NOW(), INTERVAL 2 MINUTE)) ORDER BY recharge_id DESC ");
	if($duplicate_info) {
		//Error Code: Duplicate Recharge											
		$result_code = '310';
	} else {
		if($operator_info->ser_status == '1') {
			if($operator_info->api_status == '1') {
				if($operator_info->status == '1') {
					if($amount >= $operator_info->minimum_amount && $amount <= $operator_info->maximum_amount) {			
						
						$denom_info = $db->queryUniqueObject("SELECT deno.*, api.api_id, api.status AS api_status FROM operators_denominations deno LEFT JOIN api_list api ON deno.api_id = api.api_id WHERE deno.operator_id = '".$operator_info->operator_id."' AND deno.status = '1' AND (FIND_IN_SET ($amount, deno.amount_values) OR $amount between deno.amount_from and deno.amount_to) ");
						if($denom_info) {
							if($denom_info->api_status == '1') {
								$api_id = $denom_info->api_id;
							} else {
								$api_id = $operator_info->api_id;
							}
						} else {
						    	if($agent_info->user_type=='1')
        						{
        					    	$user_info = $db->queryUniqueObject("SELECT user.*, api.api_id, api.status AS api_status FROM usercommissions user LEFT JOIN api_list api ON user.api_id = api.api_id WHERE user.operator_id = '".$operator_info->operator_id."' AND user.status = '1' AND user.uid='".$agent_info->uid."'");
        						}
        						else if($agent_info->user_type=='6') 
        						{	
        						    $user_info = $db->queryUniqueObject("SELECT user.*, api.api_id, api.status AS api_status FROM usercommissions user LEFT JOIN api_list api ON user.api_id = api.api_id WHERE user.operator_id = '".$operator_info->operator_id."' AND user.status = '1' AND user.uid='".$agent_info->uid."'");
        						}
        						else 
        						{
        					    	$user_info = $db->queryUniqueObject("SELECT user.*, api.api_id, api.status AS api_status FROM usercommissions user LEFT JOIN api_list api ON user.api_id = api.api_id WHERE user.operator_id = '".$operator_info->operator_id."' AND user.status = '1' AND user.uid='".$agent_info->dist_id."'");
        						}
        					    if($user_info) 
        					    {
        								if($user_info->api_status == '1') 
        								{
        									$api_id = $user_info->api_id;
        								}
        								else 
        								{
        									$api_id = $operator_info->api_id;
        								}
        						}
        						else
        						{
        							$api_id = $operator_info->api_id;
        						}
						  
						    }
						
					if($api_selected)
									{
									   $api_id=$api_selected;
									}
								// 	echo $api_selected;
								// 	echo $api_id;
								// 	 die;
						
						
						/*
						 * INSERT INTO RECHARGE TABLE
						 */
						if($agent_info->user_type=='1') {				
							$sCommission = getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
						}
						else if($agent_info->user_type=='6') {				
							$sCommission = getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'r');
						}
						else {
							$sCommission = getUsersCommission(trim($agent_info->dist_id), $operator_info->operator_id, $amount, 'r');
						}
						
						if($sCommission['surcharge']=='y') {
							$rch_comm_type = '1';
							$rch_comm_value = $sCommission['samount'];
						} else {
							$rch_comm_type = '0';
							$rch_comm_value = $sCommission['rtCom'];
						}
						
						$isfund = $db->queryUniqueObject("SELECT wallet_id, balance, cuttoff FROM apps_wallet WHERE user_id='".$agent_info->user_id."' ");
						if($isfund && (($isfund->balance - $isfund->cuttoff) >= ($amount + $sCommission['samount']))) {
							$status = '1';
						$db->execute("UPDATE `apps_recharge` SET `offline_uid`= '20018105', `recharge_mode`= '".$mode."',`api_id`='".$api_id."',`service_type`='".$operator_info->service_name."',`operator_id`='".$operator_info_old->operator_id."',`org_operator_id`='".$operator_info->operator_id."',`account_no`='".$account."',`surcharge`='".$sCommission['samount']."',`status`='".$status."',`status_details`='',`request_date`=NOW(),`reference_txn_no`='".$reference_txn_no."',`recharge_ip`='".$ip."' WHERE recharge_id='".$recharge_id."'");
					
					
					
				// 			$recharge_id = $db->lastInsertedId();
							
							/* Recharge Additional Details*/
							if($operator_info->service_type == '5') {
							    $db->execute("UPDATE `apps_recharge_details` SET `customer_account`='".$customer_account."',`added_date`=NOW() WHERE `recharge_id`='".$recharge_id."'");
								
							} else if($operator_info->service_type == '6') {	$db->execute("UPDATE `apps_recharge_details` SET `customer_name`='".$customer_name."',`customer_mobile`='".$customer_mobile."',`customer_email`='".$customer_email."',`customer_city`='".$customer_city."',`customer_account`='".$customer_account."',`billing_cycle`='".$billing_cycle."',`sub_division`='".$sub_division."',`billing_unit`='".$billing_unit."',`pc_number`='".$pc_number."',`added_date`=NOW() WHERE `recharge_id`='".$recharge_id."'");						
							
							} else if($operator_info->service_type == '8') {
							    $db->execute("UPDATE `apps_recharge_details` SET `dob`='".$customer_account."',`added_date`=NOW() WHERE `recharge_id`='".$recharge_id."'");
							
							} else if($operator_info->service_type=='10') {
							    $db->execute("UPDATE `orders` SET `order_date`=NOW(),`operator_id`='".$operator_info->operator_id."',`product_id`='".$product_id."',`order_amount`='".$amount."',`customer_name`='".$customer_name."',`customer_mobile`='".$customer_mobile."',`customer_email`='".$customer_email."',`order_status`='pending' WHERE `recharge_id`='".$recharge_id."'");
							
							}
							
							/*
							 * Start Transaction
							 */
							$db->query("START TRANSACTION");
							$wallet = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");
							$tax = getUserGstTxns($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
							if($sCommission['surcharge'] == 'y') {
								$debit_amount = $amount + $tax['total_debit'];
							} else {
								$debit_amount = $amount - $tax['total_debit'];
							}
							$close_balance = $wallet->balance - $debit_amount;
							$db->query("UPDATE apps_wallet SET balance='".$close_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
							$ts1 = mysql_affected_rows();
							if($wallet && $ts1) {
								$commit = $db->query("COMMIT");
								if($commit) {
									$remark = "RECHARGE: $recharge_id, $account, $amount, $debit_amount";
									$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'dr', '".$debit_amount."', '".$close_balance."',  'RECHARGE', '".$recharge_id."', '".$remark."', '5', '".$agent_info->uid."')");
								 	$transaction_id = $db->lastInsertedId();
									//
								 	$db->execute("INSERT INTO `gst_transactions`(`uid`, `recharge_id`, `operator_id`, `transaction_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`, `tds_value`, `tds_rate`, `tds_amount`, `trans_date`) VALUES ('".$agent_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$transaction_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."', '".$tax['tds_value']."', '".$tax['tds_rate']."', '".$tax['tds_amount']."', NOW())");						
									/*
									* Send to recharge
									*/
									
									if($api_id == '1') {
										include(DIR . "/library/egpay-recharge-api.php");
									} else if($api_id == '2') {
										include(DIR . "/library/arroh-recharge-api.php");
									} else if($api_id == '3') {
										include(DIR . "/library/achariya-recharge-api.php");
									} else if($api_id == '4') {
										include(DIR . "/library/ajira2-jio-recharge-api.php");
									} else if($api_id == '5') {
										include(DIR . "/library/modem-roundpay-recharge-api.php");
									} else if($api_id == '6') {
										include(DIR . "/library/roundpay-recharge-api.php");
									} else if($api_id == '7') {
										include(DIR . "/library/rechargea2z-recharge-api.php");
									} else if($api_id == '8') {
										include(DIR . "/library/aarav-recharge-api.php");
									} else if($api_id == '9') {
										include(DIR . "/library/ambika-recharge-api.php");
									} else if($api_id == '10') {
										include(DIR . "/library/cyberplat-recharge-api.php");
									} else if($api_id == '11') {
										include(DIR . "/library/offline-recharge-api.php");
									} else if($api_id == '12') {
										include(DIR . "/library/ajira-jio-recharge-api.php");
									} else if($api_id == '14') {
										include(DIR . "/library/paymentall-recharge-api.php");
									}
									
									if($status == '0') {
										/*
										* Update recharge response and params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
										*/
										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
										
										/*
										* Update commission for all parent users
										*/
										if($sCommission['surcharge']=='n' && $sCommission['rtCom'] > '0') {
											$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->uid."', '".$tax['total_debit']."', '".$close_balance."', NOW())");
										}
										
										if($agent_info->user_type!='1') {								
											//Commission Distributor
											if($sCommission['surcharge']=='n' && $sCommission['dsCom'] > '0') {
												$db->query("START TRANSACTION");
												$rDs = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->dist_id."' ");
												if($rDs) {
													$ds_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->dist_id."' ");
													if($sCommission['surcharge']=='y') {
														$rch_ds_comm_value = '0';
													} else {
														$rch_ds_comm_value = $sCommission['dsCom'];
													}
													$taxDs = getUserGstTxns($ds_info,$operator_info->billing_type,$rch_ds_comm_value,$rch_comm_type);									
													$ds_close_balance = $rDs->balance + $taxDs['total_debit'];
													$db->execute("UPDATE apps_wallet SET balance='".$ds_close_balance."' WHERE wallet_id='".$rDs->wallet_id."'");
													$ts2 = mysql_affected_rows();
													$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->dist_id."', '".$taxDs['total_debit']."', '".$ds_close_balance."', NOW())");
													$commission_id = $db->lastInsertedId();
													$db->execute("INSERT INTO `gst_transactions`(`uid`, `recharge_id`, `operator_id`, `transaction_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`, `tds_value`, `tds_rate`, `tds_amount`, `trans_date`) VALUES ('".$ds_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$commission_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['dsPer']."', '".$rch_ds_comm_value."', '".$operator_info->billing_type."', '".$taxDs['taxable_comm']."', '".$taxDs['gst_rate']."', '".$taxDs['net_comm']."', '".$taxDs['gst_tax']."', '".$taxDs['gst_amount']."', '".$taxDs['tds_value']."', '".$taxDs['tds_rate']."', '".$taxDs['tds_amount']."', NOW())");
													if($ts2) {
														$commit = $db->query("COMMIT");
													} else {
														$db->query("ROLLBACK");
													}
												} else {
													$db->query("ROLLBACK");
												}
											}
											//Commission MasterDistributor
											if($sCommission['surcharge']=='n' && $sCommission['mdCom'] > '0') {
												$db->query("START TRANSACTION");
												$rMd = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->mdist_id."' ");
												if($rMd) {
													$md_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE uid='".$agent_info->mdist_id."' ");
													if($sCommission['surcharge']=='y') {
														$rch_md_comm_value = '0';
													} else {
														$rch_md_comm_value = $sCommission['mdCom'];
													}
													$taxMd = getUserGstTxns($md_info,$operator_info->billing_type,$rch_md_comm_value,$rch_comm_type);									
													$md_close_balance = $rMd->balance + $taxMd['total_debit'];
													$db->execute("UPDATE apps_wallet SET balance='".$md_close_balance."' WHERE wallet_id='".$rMd->wallet_id."'");
													$ts2 = mysql_affected_rows();
													$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->mdist_id."', '".$taxMd['total_debit']."', '".$md_close_balance."', NOW())");
													$commission_id = $db->lastInsertedId();
													$db->execute("INSERT INTO `gst_transactions`(`uid`, `recharge_id`, `operator_id`, `transaction_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`, `tds_value`, `tds_rate`, `tds_amount`, `trans_date`) VALUES ('".$md_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$commission_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['mdPer']."', '".$rch_md_comm_value."', '".$operator_info->billing_type."', '".$taxMd['taxable_comm']."', '".$taxMd['gst_rate']."', '".$taxMd['net_comm']."', '".$taxMd['gst_tax']."', '".$taxMd['gst_amount']."', '".$taxMd['tds_value']."', '".$taxMd['tds_rate']."', '".$taxMd['tds_amount']."', NOW())");
													if($ts2) {
														$commit = $db->query("COMMIT");
													} else {
														$db->query("ROLLBACK");
													}
												} else {
													$db->query("ROLLBACK");
												}
											}
										}
										//Error Code: Recharge Success
										$result_code = '300';
									
									} else if ($status == '2') {								
										/*
										* revert recharge amount to agent
										*/
										if($agent_info->user_type!='1') {
										    
										    $wallet2 = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='20018105' ");													
											$new_close_balance2 = $wallet2->balance + $debit_amount;
											$db->execute("UPDATE apps_wallet SET balance='".$new_close_balance2."' WHERE wallet_id='".$wallet2->wallet_id."' ");
											
	                                                $remark_new2 = "REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
													$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '20018105', '20018105', 'cr', '".$debit_amount."', '".$new_close_balance2."',  'FAILURE', '".$recharge_id."', '".$remark_new2."', '5', '20018105')");								
                                            $utility_status = $db->queryUniqueValue("SELECT status from offline_denominations where id=1 and service_type_id=0 ");
            								    $jio = $db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and (user_type=5 or user_type=1) and status=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to) ");
            								    $offline=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and (user_type=5 or user_type=1) and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
            								    $jio_from=$jio->amount_from;
            								    $jio_to=$jio->amount_to;
            								    if($utility_status=='1' && ($amount>=$jio_from && $amount>=$jio_to))
								                  {
										    if($operator_info->service_type=='4'||$operator_info->service_type=='5'||$operator_info->service_type=='6'||$operator_info->operator_id=='20')
												{
													$request_txn_no = $recharge_id;
													$status = '8';
													$status_details = 'Transaction Submitted';
													
												$db->execute("UPDATE apps_recharge SET status='8',api_id='11',org_api_id='".$api_id."', status_details='Transaction Submitted', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
        										//Error Code: Recharge Submitted
        										$result_code = '308';
												}
								                  }
												
											elseif($offline)
                                                {
                                                   
            										$db->execute("UPDATE apps_recharge SET status='1', org_status='".$status."',request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
            										//Error Code: Recharge Pending
            									$result_code = '301';
                                                    
            									
                                                }	
            								else {					
											$db->query("START TRANSACTION");													
											$wallet1 = $db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");													
											$new_close_balance = $wallet1->balance + $debit_amount;
											$db->execute("UPDATE apps_wallet SET balance='".$new_close_balance."' WHERE wallet_id='".$wallet1->wallet_id."' ");
											$ts4 = mysql_affected_rows();
											if($wallet1 && $ts4) {													
												$commit = $db->query("COMMIT");	
												if($commit) {													
																							
													$remark_new = "REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
													$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$new_close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");								

													/*
													* Update response status for failed recharge
													*/
													$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', is_refunded = 'y' WHERE recharge_id = '".$recharge_id."' ");
													//Error Code: Recharge Failed											
													$result_code = '302';
												}
											
													
											} else {
												$db->query("ROLLBACK");
												//ERROR Code: Server Error";
												$result_code = '301';
											}
										} 
										}else {
											/*
											* Update response status for failed recharge
											*/
											$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
											//Error Code: Recharge Failed
											 $utility_status = $db->queryUniqueValue("SELECT status from offline_denominations where id=1 and service_type_id=0 ");
            								    $jio = $db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and (user_type=5 or user_type=1) and id!=1 and status=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to) ");
            								    $offline=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and (user_type=5 or user_type=1) and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
            								    $jio_from=$jio->amount_from;
            								    $jio_to=$jio->amount_to;
            								    if($utility_status=='1')
								                  {
        											if($operator_info->service_type=='4'||$operator_info->service_type=='5'||$operator_info->service_type=='6')
        											{
        												$db->execute("UPDATE apps_recharge SET status='8',api_id='11',org_api_id='".$api_id."', status_details='Transaction Submitted', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
        												//Error Code: Recharge Submitted
        												$result_code = '308';
        											}
								                  }
								                   else if($jio)
                    								    {
                    								        if($operator_info->operator_id=='20' && $amount > '90')
                    								        {   
                    							                    $request_txn_no = $recharge_id;
                    												$status = '8';
                    												$status_details = 'Transaction Submitted';
                    													
                    												$db->execute("UPDATE apps_recharge SET status='8',api_id='11',org_api_id='".$api_id."', status_details='Transaction Submitted', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
                            										//Error Code: Recharge Submitted
                            										$result_code = '308';
                    								        }        
                    								    }
											elseif($offline)
                                                {
                                                    
            									$db->execute("UPDATE apps_recharge SET status='1', org_status='".$status."',request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
            										//Error Code: Recharge Pending
            									$result_code = '301';
                                                
            									
                                                }	
													
											$result_code = '302';
										}
										
										/*
										* Auto Switch to Safe Operator
										*/
										include(DIR . "/ajax/switch-operator-api.php");
										
									
									} else if ($status == '7') {						
										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
										//Error Code: Recharge Processed
										$result_code = '307';
												
									} else if ($status == '8') {						
										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
										//Error Code: Recharge Submitted
										$result_code = '308';
												
									} else if ($status == '1') {						
										$db->execute("UPDATE apps_recharge SET status='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
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
										$db->execute("INSERT INTO `apps_response_log`(`log_id`, `txn_no`, `api_id`, `http_response_code`, `http_response_content`, `update_date`) VALUES ('', '".$recharge_id."', '".$api_id."', '".$http_code."', '".mysql_real_escape_string($output)."', NOW())");
									}
								}
															
							} else {
								$db->query("ROLLBACK");
								$db->execute("UPDATE apps_recharge SET status='6', status_details='Transaction cancelled', response_date=NOW() WHERE recharge_id='".$recharge_id."' ");
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
	}
} else {
	//Error Code: Invalid Operator
	$result_code = '314';
}