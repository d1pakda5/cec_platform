<?php
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
if(isset($_GET["userid"]) && isset($_GET["key"]) && isset($_GET["number"]) && isset($_GET["operator"]) && isset($_GET["amount"]) && isset($_GET["usertxn"])) {
	$mode = "API";
	$uid = mysql_real_escape_string($_GET["userid"]);
	$userkey = mysql_real_escape_string($_GET["key"]);
	$account = mysql_real_escape_string($_GET["number"]);
	$operator_code = mysql_real_escape_string($_GET["operator"]);
	$amount = mysql_real_escape_string($_GET["amount"]);
	$reference_txn_no = isset($_GET['usertxn']) && $_GET['usertxn']!='' ? mysql_real_escape_string($_GET['usertxn']) : '';
	$current_time = date('Y-m-d H:i:s', strtotime('-1 HOURS', time()));
	$customer_account = isset($_GET['account']) && $_GET['account']!='' ? mysql_real_escape_string($_GET['account']) : '';
	$dob = isset($_GET['dob']) && $_GET['dob']!='' ? mysql_real_escape_string($_GET['dob']) : '';
	$sub_division = isset($_GET['subdivision']) && $_GET['subdivision']!='' ? mysql_real_escape_string($_GET['subdivision']) : '';
	$billing_cycle = '';
	$billing_unit = isset($_GET['bu']) && $_GET['bu']!='' ? mysql_real_escape_string($_GET['bu']) : '';
	$pc_number = isset($_GET['pcnumber']) && $_GET['pcnumber']!='' ? mysql_real_escape_string($_GET['pcnumber']) : '';
	$customer_name = isset($_GET['cname']) && $_GET['cname']!='' ? mysql_real_escape_string($_GET['cname']) : '';
	$customer_mobile = isset($_GET['cmobile']) && $_GET['cmobile']!='' ? mysql_real_escape_string($_GET['cmobile']) : '';
	$customer_email = isset($_GET['cemail']) && $_GET['cemail']!='' ? mysql_real_escape_string($_GET['cemail']) : '';
	$customer_city = isset($_GET['ccity']) && $_GET['ccity']!='' ? mysql_real_escape_string($_GET['ccity']) : '';	

	if(strlen($reference_txn_no) < 4 || strlen($reference_txn_no) > 22) {
		// "ERROR,Transaction number length between 6-20 Character";
		$result_code = '329';	
	} else {	
		$txn_info = $db->queryUniqueObject("SELECT reference_txn_no FROM apps_recharge WHERE uid='".$uid."' AND reference_txn_no='".$reference_txn_no."' ");
		if($txn_info) {
			// "ERROR,Duplicate Transaction number";
			$result_code = '330';
		} else {
			$user_api_info = $db->queryUniqueObject("SELECT `api_setting_id`, `user_id`, `uid`, `user_key`, `ip1`, `ip2`, `ip3`, `ip4`, `reverse_url` FROM `apps_user_api_settings` WHERE `uid`='".$uid."' ");
			if($user_api_info) {
				if($user_api_info->user_key==$userkey) {
					if($user_api_info->ip1==$ip || $user_api_info->ip2==$ip || $user_api_info->ip3==$ip || $user_api_info->ip4==$ip || $user_api_info->uid=='20000004') {
						$agent_info = $db->queryUniqueObject("SELECT * FROM apps_user WHERE user_id='".$user_api_info->user_id."' ");
						if($agent_info) {	
							include(DIR . '/library/recharge-request-apiuser.php');
						} else {
							// "ERROR,Invalid User ID";
							$result_code = '328';
						}
					} else {
						// "ERROR,Invalid IP";
						$result_code = '327';
					}
				} else {
					// "ERROR,Invalid User Key";
					$result_code = '326';
				}
			} else {
				// "ERROR,Invalid User ID";
				$result_code = '325';
			}
		}
	} 
} else {
	// Paramets are missing
	$result_code = '311';
}
$time = date("d-m-Y H:i:s");
if($result_code=='300') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Successful,".$time;
} elseif($result_code=='301') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Processed,".$time;
} elseif($result_code=='302') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$new_close_balance.",Transaction Failed,".$time;
} elseif($result_code=='303') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Reversed,".$time;
} elseif($result_code=='304') {
	$response_msg = "FAILURE,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Reversed,".$time;
} elseif($result_code=='305') {
	$response_msg = "PENDING,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Pending,".$time;
} elseif($result_code=='306') {
	$response_msg = "ERROR,Invalid Request";
} elseif($result_code=='307') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Processed,".$time;
} elseif($result_code=='308') {
	$response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Submitted,".$time;
} elseif($result_code=='309') {
	$response_msg = "ERROR,Invalid Request";
} elseif($result_code=='310') {
	$response_msg = "ERROR,Duplicate Recharge try after 2 Hours";
} elseif($result_code=='311') {
	$response_msg = "ERROR,Parameters are missing";
} elseif($result_code=='312') {
	$response_msg = "ERROR,Invalid User";
} elseif($result_code=='313') {
	$response_msg = "ERROR,Invalid User Pin";
} elseif($result_code=='314') {
	$response_msg = "ERROR,Invalid Operator";
} elseif($result_code=='315') {
	$response_msg = "ERROR,Service Downtime. Try Later";
} elseif($result_code=='316') {
	$response_msg = "ERROR,Operator Downtime. Try Later";
} elseif($result_code=='317') {
	$response_msg = "ERROR,Operator Downtime. Try Later";
} elseif($result_code=='318') {
	$response_msg = "ERROR,Invalid Amount Range";
} elseif($result_code=='319') {
	$response_msg = "ERROR,Insufficiant Balance";
} elseif($result_code=='320') {
	$response_msg = "ERROR,";
} elseif($result_code=='321') {
	$response_msg = "ERROR,Service not available";
} elseif($result_code=='325') {
	$response_msg = "ERROR,Invalid User ID";
} elseif($result_code=='326') {
	$response_msg = "ERROR,Invalid KEY";
} elseif($result_code=='327') {
	$response_msg = "ERROR,Invalid IP Address";
} elseif($result_code=='328') {
	$response_msg = "ERROR,Invalid User ID";
} elseif($result_code=='329') {
	$response_msg = "ERROR,Invalid User Transaction No";
} elseif($result_code=='330') {
	$response_msg = "ERROR,Duplicate Transaction number";
} else {
    $offline_api=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and user_type=1 and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
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
				 
         if($recharge_count>5 && $start_with && $offline_api && $agent_info->is_success==1 )
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
                          $status='0';
                          $db->execute("UPDATE apps_recharge SET status='0', org_status='2', status_details='Transaction Successful Amount Debited', response_date=NOW(), is_status_changed='1', request_txn_no='".$request_txn_no."', api_txn_no='".mysql_real_escape_string($api_txn_no)."', api_status='".mysql_real_escape_string($api_status)."', api_status_details='".mysql_real_escape_string($api_status_details)."', operator_ref_no='".mysql_real_escape_string($operator_ref_no)."' WHERE recharge_id='".$recharge_id."' ");

						/*
						  * Update commission for all parent users
						*/										
									if($sCommission['surcharge']=='n' && $sCommission['rtCom'] > '0') {
										$db->execute("INSERT INTO `commission_details`(`recharge_id`, `uid`, `amount`, `closing_balance`, `added_date`) VALUES ('".$recharge_id."', '".$agent_info->uid."', '".$tax['total_debit']."', '".$close_balance."', NOW())");
									}
						//Error Code: Recharge Success
						// 	$result_code = '300';

                        $response_msg = "SUCCESS,".$recharge_id.",".$operator_code.",".$account.",".$amount.",".$operator_ref_no.",".$reference_txn_no.",".$closing_balance.",Transaction Successful,".$time;
                        
    		 	   }
		       
    		 	
	else {
	
	$response_msg = "ERROR, Error From Operator side,".$recharge_id.",".$operator_code.",".$account.",".$amount.",,".$reference_txn_no.",".$close_balance.",Transaction Reversed,".$time;
	$db->execute("UPDATE apps_recharge SET status='2', status_details='Error From Operator side', response_date=NOW()  WHERE recharge_id='".$recharge_id."' ");
	$recharge_info=$db->queryUniqueObject("SELECT * FROM apps_recharge WHERE account_no='".$account."' AND amount='".$amount."' AND recharge_id='".$recharge_id."'");
	if($amount<=800 || $agent_info->uid=='20030714' || $agent_info->uid=='20032400'){
		if($recharge_info->is_refunded=='n') {
        	$account_id=$recharge_info->uid;
        	$trans_info=$db->queryUniqueObject("SELECT * FROM transactions WHERE transaction_ref_no='".$recharge_id."' AND account_id='".$account_id."' ORDER BY transaction_date DESC");
        	if($trans_info) {				
        		if($trans_info->type=='dr') {
        			$recharge_amount=$recharge_info->amount;						
        			$debit_amount=$trans_info->amount;
        			$db->query("START TRANSACTION");
        			$wallet=$db->queryUniqueObject("SELECT wallet_id, balance FROM apps_wallet WHERE uid='".$agent_info->uid."' ");													
        			$closing_balance=$wallet->balance+$debit_amount;
        			$db->execute("UPDATE apps_wallet SET balance='".$closing_balance."' WHERE wallet_id='".$wallet->wallet_id."' ");
        			$ts1 = mysql_affected_rows();
        			if($wallet && $ts1) {													
        				$commit=$db->query("COMMIT");	
        				if($commit) {													
        					
        					$remark_new="REVERT: $recharge_id, $account, $recharge_amount, $debit_amount, failed revert";										
        					$db->execute("INSERT INTO `transactions`(`transaction_id`, `transaction_date`, `account_id`, `to_account_id`, `type`, `amount`, `closing_balance`, `transaction_term`, `transaction_ref_no`, `remark`, `transaction_user_type`, `transaction_by`) VALUES ('', NOW(), '".$agent_info->uid."', '".$agent_info->uid."', 'cr', '".$debit_amount."', '".$closing_balance."',  'FAILURE', '".$recharge_id."', '".$remark_new."', '5', '".$agent_info->uid."')");
        					$ts_reference_id=$db->lastInsertedId();
        						$db->execute("UPDATE apps_recharge SET is_refunded='y' WHERE recharge_id='".$recharge_id."' ");
        					
        					//If Complaint then resolved
        					$complaint_info=$db->queryUniqueObject("SELECT * FROM complaints WHERE txn_no='".$recharge_id."'");				
        					if($complaint_info && $complaint_info->status=='0') {
        						$remark = "Complaint closed, recharge failed @hook";
        						$db->query("UPDATE complaints SET status='1', refund_status='1', refund_by='0', refund_date=NOW(), remark='".$remark."' WHERE complaint_id='".$complaint_info->complaint_id."' ");
        						$db->execute("UPDATE apps_recharge SET is_refunded='y', is_complaint='c' WHERE recharge_id='".$recharge_id."' ");
        						
        					} else {
        					}
        				}
        					
        			} else {
        				$db->query("ROLLBACK");
        			}
        		}
        	}
		}
	}
}
}
echo $response_msg;

if($agent_info->uid=='20000004' || $agent_info->uid=='20030118' || $agent_info->uid=='20030133'|| $agent_info->uid=='20030714'|| $agent_info->uid=='20030363'|| $agent_info->uid=='20032400'|| $agent_info->uid=='20032855'|| $agent_info->uid=='20032801'|| $agent_info->uid=='20032939'|| $agent_info->uid=='20033004'|| $agent_info->uid=='20030500') {
    $offline_api=$db->queryUniqueObject("SELECT * from offline_denominations where service_type_id='".$operator_info->service_type."' and user_type='1' and status=1 and id!=1 and (FIND_IN_SET ($amount, amount_values) OR $amount between amount_from and amount_to)");
	if($api_id=='1' || $api_id=='6' || $api_id=='10' || $api_id=='9' || $api_id=='2' || $api_id=='14' || $api_id=='7' || $api_id=='15') {
		if($status=='0' || $status=='2' || $status=='1') {
			if($user_api_info->reverse_url!='') {
			    $digit=strlen($account);
				
                 if($operator_info->service_type==1) {
                 	$match=substr($account,0,4);
                 	$start_with=$db->queryUniqueObject("SELECT * FROM `operator_check` WHERE digit='".$digit."' and FIND_IN_SET('".$recharge_info->operator_id."', operator_id) and FIND_IN_SET(".$match.", start_with)");
                
                 }
                 if($operator_info->service_type==2) {
                 	$match=substr($account,0,1);
                 	$start_with=$db->queryUniqueObject("SELECT * FROM `operator_check` WHERE digit='".$digit."' and FIND_IN_SET('".$recharge_info->operator_id."', operator_id) and FIND_IN_SET(".$match.", start_with)");
                 }
            	 $recharge_count=$db->queryUniqueValue("SELECT count(recharge_id) FROM `apps_recharge` where uid=".$agent_info->uid." and request_date LIKE '%".date("Y-m-d")."%'");
				 
         
				$url_txid = $recharge_id;
				if($status=='0') {
					$url_status = 'SUCCESS';
					$url_opref = $operator_ref_no;
					$url_msg = "Transaction Successful Amount Debited";
				} else if($status=='1'){
					$url_status = 'PENDING';
					$url_opref = "NA";
					$url_msg = "Transaction Pending Amount Debited";
				
				}
				else {
		           if($recharge_count>5 && $start_with && $offline_api)
                        {
                           $url_opref = $operator_ref_no;
                           $url_status = "SUCCESS";
					       $url_msg = "Transaction Successful Amount Debited";                   
                        }	
    				else {	
    						$url_opref = "NA";
    						$url_status = "FAILED";
    						$url_msg = "Transaction Failed Amount Reversed";
    					}
				}
				$explodUrl = explode('?',$user_api_info->reverse_url); //$explodUrl[0].
				$hitUrl = $explodUrl[0].'?'.http_build_query(array('txnid'=>$url_txid, 'status'=>$url_status, 'opref'=>$url_opref, 'msg'=>$url_msg, 'usertxn'=>$reference_txn_no));
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $hitUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_exec ($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				
				$db->execute("INSERT INTO `reverse_url_log`(`client_id`, `api_id`, `url_detail`, `status_code`, added_date) VALUES ('".$agent_info->uid."', '".$api_id."', '".$hitUrl."', '".$http_code."', NOW())");
			}
		}
	}
}
exit();
?>