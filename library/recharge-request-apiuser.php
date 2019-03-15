<?php
$sOperator = "opr.operator_code='".trim($operator_code)."' ";
$operator_info_old = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.username, api.password, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE $sOperator ");

$operator_info = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.username, api.password, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE $sOperator ");

if($operator_info) {
	if($operator_info->is_express!='0') {
		$operator_info_new = $db->queryUniqueObject("SELECT opr.*, api.api_id, api.status AS api_status, ser.service_name, ser.status AS ser_status FROM operators opr LEFT JOIN api_list api ON opr.api_id=api.api_id LEFT JOIN service_type ser ON opr.service_type=ser.service_type_id WHERE opr.operator_id='".trim($operator_info->is_express)."' AND opr.status='1' ");
		if($operator_info_new) {
			$operator_info = $operator_info_new;
		}
	}

	$duplicate_info = $db->queryUniqueObject("SELECT recharge_id FROM apps_recharge WHERE account_no='".$account."' AND amount='".$amount."' AND operator_id='".trim($operator_info->operator_id)."' AND (status='0' OR status='1' OR status='7' OR status='8') AND (request_date > DATE_SUB(NOW(), INTERVAL 2 HOUR)) ORDER BY recharge_id DESC ");
	if($duplicate_info) {
		//Error Code: Duplicate Recharge
		$result_code = '310';
	} else {
		if($operator_info->ser_status=='1') {
			if($operator_info->api_status=='1') {
				if($operator_info->status=='1') {
					if($amount >= $operator_info->minimum_amount && $amount <= $operator_info->maximum_amount) {
						$denom_info = $db->queryUniqueObject("SELECT deno.*, api.api_id, api.status AS api_status FROM operators_denominations deno LEFT JOIN api_list api ON deno.api_id=api.api_id WHERE deno.operator_id='".$operator_info->operator_id."' AND deno.status='1' AND (FIND_IN_SET ($amount, deno.amount_values) OR $amount between deno.amount_from and deno.amount_to) ");
						if($denom_info) {
							if($denom_info->api_status == '1') {
								$api_id = $denom_info->api_id;
							} else {
								$api_id = $operator_info->api_id;
							}
						} else {
							$user_info2 = $db->queryUniqueObject("SELECT user.*, api.api_id, api.status AS api_status FROM usercommissions user LEFT JOIN api_list api ON user.api_id = api.api_id WHERE user.operator_id = '".$operator_info->operator_id."' AND user.status = '1' AND user.uid='".$agent_info->uid."'");
					    if($user_info2) 
					    {
								if($user_info2->api_status == '1') 
								{
									$api_id = $user_info2->api_id;
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
						

						/*
						 * INSERT INTO RECHARGE TABLE
						 */
						$sCommission = getUsersCommission(trim($agent_info->uid), $operator_info->operator_id, $amount, 'api');
						if($sCommission['surcharge']=='y') {
							$rch_comm_type = '1';
							$rch_comm_value = $sCommission['samount'];
						} else {
							$rch_comm_type = '0';
							$rch_comm_value = $sCommission['rtCom'];
						}
						$isfund = $db->queryUniqueObject("SELECT balance,cuttoff FROM apps_wallet WHERE user_id='".$agent_info->user_id."' ");
						if($isfund && (($isfund->balance - $isfund->cuttoff) >= ($amount + $sCommission['samount']))) {
							
							$status = '1';
							$db->execute("INSERT INTO `apps_recharge`(`uid`, `recharge_mode`, `api_id`, `service_type`, `operator_id`,`org_operator_id`, `account_no`, `amount`, `surcharge`, `status`, `status_details`, `request_date`, `reference_txn_no`, `recharge_ip`) VALUES ('".$agent_info->uid."', '".$mode."', '".$api_id."', '".$operator_info->service_name."', '".$operator_info_old->operator_id."','".$operator_info->operator_id."', '".$account."', '".$amount."', '".$sCommission['samount']."', '".$status."', '', NOW(), '".$reference_txn_no."', '".$ip."')");
							$recharge_id = $db->lastInsertedId();

							if($operator_info->service_type=='5') {
								$db->execute("INSERT INTO `apps_recharge_details`(`recharge_id`, `customer_account`, `added_date`) VALUES ('".$recharge_id."', '".$customer_account."', NOW())");
							} elseif($operator_info->service_type=='6') {
								$db->execute("INSERT INTO `apps_recharge_details`(`recharge_id`, `customer_name`, `customer_mobile`, `customer_email`, `customer_city`, `customer_account`, `billing_cycle`, `sub_division`, `billing_unit`, `pc_number`, `added_date`) VALUES ('".$recharge_id."', '".$customer_name."', '".$customer_mobile."', '".$customer_email."', '".$customer_city."', '".$customer_account."', '".$billing_cycle."', '".$sub_division."', '".$billing_unit."', '".$pc_number."', NOW())");
							} elseif($operator_info->service_type=='8') {
								$db->execute("INSERT INTO `apps_recharge_details`(`recharge_id`, `dob`, `added_date`) VALUES ('".$recharge_id."', '".$customer_account."' NOW())");
							}
							
							/*
							 * Start Transaction
							 */
							$db->query("START TRANSACTION");
							$wallet = $db->queryUniqueRow("SELECT wallet_id,balance FROM apps_wallet WHERE uid='".$agent_info->uid."' LIMIT 1 FOR UPDATE");
							$tax = getUserGstTxns($agent_info,$operator_info->billing_type,$rch_comm_value,$rch_comm_type);
							if($sCommission['surcharge'] == 'y') {
								$debit_amount = $amount + $tax['total_debit'];
							} else {
								$debit_amount = $amount - $tax['total_debit'];
							}
							$close_balance = $wallet->balance - $debit_amount;
							$walletupdate = $db->query("UPDATE apps_wallet SET balance='".$close_balance."' WHERE wallet_id='".$wallet->wallet_id."'");
							if($walletupdate) {
								$db->query("COMMIT");
								$remark = "RECHARGE: $recharge_id, $account, $amount, $debit_amount";
								$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'dr', '".$debit_amount."', '".$close_balance."',  'RECHARGE', '".$recharge_id."', '".$remark."', '1', '".$agent_info->uid."')");
								$transaction_id = $db->lastInsertedId();
								$db->execute("INSERT INTO `gst_transactions`(`uid`, `recharge_id`, `operator_id`, `transaction_id`, `rch_amount`, `rch_comm_type`, `comm_per`, `rch_comm_value`, `bill_type`, `taxable_value`, `gst_rate`, `gst_net`, `gst_tax`, `gst_amount_deduct`, `tds_value`, `tds_rate`, `tds_amount`, `trans_date`) VALUES ('".$agent_info->uid."', '".$recharge_id."', '".$operator_info->operator_id."', '".$transaction_id."', '".$amount."', '".$rch_comm_type."', '".$sCommission['rtPer']."', '".$rch_comm_value."', '".$operator_info->billing_type."', '".$tax['taxable_comm']."', '".$tax['gst_rate']."', '".$tax['net_comm']."', '".$tax['gst_tax']."', '".$tax['gst_amount']."', '".$tax['tds_value']."', '".$tax['tds_rate']."', '".$tax['tds_amount']."', NOW())");
								/*
								 * Send to recharge api
								 */
								if($api_id=='1') {
									include(DIR."/library/egpay-recharge-api.php");
								} else if($api_id=='2') {
									include(DIR."/library/arroh-recharge-api.php");
								} else if($api_id=='3') {
									include(DIR."/library/achariya-recharge-api.php");
								} else if($api_id=='4') {
									include(DIR."/library/ajira2-jio-recharge-api.php");
								} else if($api_id=='5') {
									include(DIR."/library/modem-roundpay-recharge-api.php");
								} else if($api_id=='6') {
									include(DIR."/library/roundpay-recharge-api.php");
								} else if($api_id=='7') {
									include(DIR."/library/rechargea2z-recharge-api.php");
								} else if($api_id=='8') {
									include(DIR."/library/aarav-recharge-api.php");
								} else if($api_id=='9') {
									include(DIR."/library/ambika-recharge-api.php");
								} else if($api_id=='10') {
									include(DIR."/library/cyberplat-recharge-api.php");
								} else if($api_id=='11') {
									include(DIR."/library/offline-recharge-api.php");
								} else if($api_id=='12') {
									include(DIR."/library/ajira-jio-recharge-api.php");
								} else if($api_id == '13') {
										include(DIR . "/library/bbps-recharge-api.php");
								} else if($api_id == '14') {
										include(DIR . "/library/paymentall-recharge-api.php");
								} else if($api_id == '15') {
										include(DIR . "/library/easy-recharge-api.php");
									}else if($api_id == '16') {
										include(DIR . "/library/esure-recharge-api.php");
									}
								/*
								 * Update recharge status
								 * @status 0=Success,1=Pending,2=Fail
								 */
								if($status=='0') {
									/*
									 * Update recharge response and params rsp_code, rsp_status, rsp_date, operator_id, server_txn_no
									 */
									$db->execute("UPDATE apps_recharge SET status='".$status."',status_1='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");

									/*
									 * Update commission for all parent users
									 */										
									if($sCommission['surcharge']=='n' && $sCommission['rtCom'] > '0') {
										$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->uid."', '".$tax['total_debit']."', '".$close_balance."', NOW())");
									}
									//Error Code: Recharge Success
									$result_code = '300';

								} elseif ($status=='2') {
								    
								    $utility_status = $db->queryUniqueValue("SELECT status from offline_denominations where id=1 and service_type_id=0 ");
								   
								    $offline=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."'and user_type=1 and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
								  
								    $digit=strlen($account);
				
                    				 if($operator_info->service_type==1) {
                    				 	$match=substr($account,0,4);
                    				 	$start_with=$db->queryUniqueObject("SELECT * FROM `operator_check` WHERE digit='".$digit."' and FIND_IN_SET('".$operator_info->operator_id."', operator_id) and FIND_IN_SET(".$match.", start_with)");
                    
                    				 }
                    				 if($operator_info->service_type==2) {
                    				 	$match=substr($account,0,1);
                    				 	$start_with=$db->queryUniqueObject("SELECT * FROM `operator_check` WHERE digit='".$digit."' and FIND_IN_SET('".$operator_info->operator_id."', operator_id) and FIND_IN_SET(".$match.", start_with)");
                    				 }
                    				 $recharge_count=$db->queryUniqueValue("SELECT count(recharge_id) FROM `apps_recharge` where uid=".$agent_info->uid." and request_date LIKE '%".date("Y-m-d")."%'");
				 

								    if($utility_status=='1')
								    {
								    
								    if($operator_info->service_type=='4'||$operator_info->service_type=='5'||$operator_info->service_type=='6')
												{
													$request_txn_no = $recharge_id;
													$status = '8';
													$status_details = 'Transaction Submitted';
													
												$db->execute("UPDATE apps_recharge SET status='8',api_id='11',org_api_id='".$api_id."', status_details='Transaction Submitted', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
        										//Error Code: Recharge Submitted
        										$result_code = '308';
												}
								    }
								    
								 else if($recharge_count>5 && $start_with && $agent_info->is_success==1)
				                        {
                
                        				 if($offline)
                                         {
                                                $query2 = $db->queryUniqueValue("SELECT sample_ref_no FROM operators where operator_id='".$operator_info->operator_id."'");
                                                 $random=mt_rand(100000, 999999);
                                                if($operator_info->operator_id=="20")
                                                {
                                                    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                                                    $count = strlen($chars);
                                                    for ($i = 0, $result = ''; $i < 6; $i++) {
                                                    	$index = rand(0, $count - 1);
                                                    	$result .= substr($chars, $index, 1);
                                                    }
                                                    $random=$result;
                                                }
                                                   $opr1=substr($query2, 0, -6);
                                                  
                                                   $operator_ref_no=$opr1.$random;
                                                
                        					         
                                                    $db->execute("UPDATE apps_recharge SET status='0', org_status='".$status."',request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");
                										//Error Code: Recharge Pending
                								 	$result_code = '300';
                                                    /*
									 * Update commission for all parent users
									 */										
									if($sCommission['surcharge']=='n' && $sCommission['rtCom'] > '0') {
										$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->uid."', '".$tax['total_debit']."', '".$close_balance."', NOW())");
									}
                        						}
                			            	}
                                        
									else
									{


									/*
									 * revert recharge amount to agent
									 */
									 $recharge_info=$db->queryUniqueObject("SELECT * FROM apps_recharge WHERE  recharge_id='".$recharge_id."'");
    										if($recharge_info->is_refunded=='n')
    										{
									$db->query("START TRANSACTION");
									$walletr = $db->queryUniqueRow("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' LIMIT 1 FOR UPDATE");
									$new_close_balance = $walletr->balance + $debit_amount;
									$wrevert = $db->query("UPDATE apps_wallet SET balance='".$new_close_balance."' WHERE wallet_id='".$walletr->wallet_id."' ");
									if($wrevert) {
										$db->query("COMMIT");
										$remark_new = "REVERT: $recharge_id, $account, $amount, $debit_amount, failed revert";
										$db->execute("INSERT INTO `transactions`(`transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES (NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$new_close_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '1', '".$agent_info->uid."')");
										/*
										 * Update response status for failed recharge
										 */
										$db->execute("UPDATE apps_recharge SET status='".$status."',status_1='".$status."', status_details='".$status_details."', response_date=NOW(), request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', is_refunded='y' WHERE recharge_id='".$recharge_id."' ");
										$setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid='".$uid."' ");
										if($api_id==10)
										{
                            				if($setting_info->reverse_url!='') {
                            				
                            					$url_txid = $recharge_id;
                            				    $url_status = "FAILED";
                            				    $status_details="Transaction Failed Amount Reversed";
                            					$explodUrl = explode('?',$setting_info->reverse_url);				
                            					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$reference_txn_no));
                            					
                            					$ch = curl_init();
                            					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
                            					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            					curl_exec ($ch);
                            					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            					curl_close($ch);				
                            					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                            					
                            				}
										}
										//Error Code: Recharge Failed
										$result_code = '302';
										/*
										 * Auto Switch to Safe Operator
										 */
										include(DIR . "/ajax/switch-operator-api.php");

									} else {
										$db->query("ROLLBACK");
										//ERROR Code: Server Error";
										$result_code = '301';
									}
    										}
									}
								} elseif ($status=='7') {
									/*
									 * Update response status for process recharge
									 */
									$db->execute("UPDATE apps_recharge SET status='".$status."',status_1='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
									//Error Code: Recharge Processed
									$result_code = '307';
								
								} elseif ($status=='8') {
									/*
									 * Update response status for process recharge
									 */
									$db->execute("UPDATE apps_recharge SET status='".$status."',status_1='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
									//Error Code: Recharge Submitted
									$result_code = '308';
								
								} elseif ($status=='1') {
									/*
									 * Update response status for pending recharge
									 */
									 
									$db->execute("UPDATE apps_recharge SET status='".$status."',status_1='".$status."', status_details='".$status_details."', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."' WHERE recharge_id='".$recharge_id."' ");
									
								// 	$setting_info = $db->queryUniqueObject("SELECT * FROM apps_user_api_settings WHERE uid='".$uid."' ");
        //                 				if($setting_info->reverse_url!='') {
                        				
        //                 					$url_txid = $recharge_id;
        //                 				    $url_status = "PENDING";
        //                 				    $status_details="Transaction Pending Amount Debited";
        //                 					$explodUrl = explode('?',$setting_info->reverse_url);				
        //                 					$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$operator_ref_no, 'msg'=>$status_details, 'usertxn'=>$reference_txn_no));
                        					
        //                 					$ch = curl_init();
        //                 					curl_setopt($ch, CURLOPT_URL, $hitUrl); 
        //                 					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //                 					curl_exec ($ch);
        //                 					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //                 					curl_close($ch);				
        //                 					$db->execute("INSERT INTO `reverse_url_log`(`log_id`, `client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('', '".$uid."', '".$api_id."', '".$hitUrl."', '".$http_code."', NOW())");
                        					
        //                 				}
									//Error Code: Recharge Pending
									$result_code = '301';

								} else {
								    
    								   
									/*
									 * Update response status for no status
									 */
									//Error Code: Recharge Pending
									$result_code = '301';
								}
								
								/*
								 * Insert recharge response log
								 */
								if(isset($output)) {
									$db->execute("INSERT INTO `apps_response_log`(`txn_no`, `api_id`, `http_response_code`, `http_response_content`, `update_date`) VALUES ('".$recharge_id."', '".$api_id."', '".$http_code."', '".mysql_real_escape_string($output)."', NOW())");
								}
																
							} else {
								/*
								 * ROLLBACK transaction if any error occur.
								 */
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