<?php
include("/home/recharge/public_html/config.php");
	$query = $db->query("SELECT * FROM api_list where status=1 ORDER BY api_id ASC ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						    	if($result->api_id == '1') {
								
								
								} else if($result->api_id == '2') {
								    $balance_api2='';
								 	$url ="http://arrohservices.in/web-services/httpapi/check-balance?acc_no=".$arroh['acc_no']."&api_key=".$arroh['key'];
							        $output = file_get_contents($url);
									$balance_split = explode(" ", $output);
									$balance_api2=$balance_split[1];
								} else if($result->api_id == '3') {
								
									
								} else if($result->api_id == '5') {
								
								} else if($result->api_id == '6') {
								    $balance_api6=''; 
									$url = "http://roundpayapi.in/API/APIService.aspx?userid=".$roundpay['userid']."&pass=".$roundpay['pass']."&Get=CB";
									$output = file_get_contents($url);
									$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);
									$balance_api6 = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									
								} else if($result->api_id == '7') {
								    $balance_api7=''; 
									$url = "http://rechargea2z.com/API/APIService.aspx?userid=".$rechargea2z['userid']."&pass=".$rechargea2z['pass']."&Get=CB&fmt=Json";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance_api7 = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									
								} else if($result->api_id == '9') {
								    $balance_api9='';
									$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&Get=CB&fmt=Json";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance_api9 = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									
								} else if($result->api_id == '10') {
								        $request_txn_no = time();
	
                                    	include("/home/recharge/public_html/system/class.cyberplat.php");
                                    	$cp = new CyberPlat();	
                                    	
                                    	define('CP_SD','245840');
                                    	define('CP_AP','256750');
                                    	define('CP_OP','256751');
                                    	define('CP_PASSWORD','Vinod@123');	
                                    	
                                    	$secret_key = file_get_contents("/home/recharge/public_html/library/secret.key");
                                    	$public_key = file_get_contents("/home/recharge/public_html/library/pubkeys.key");
                                    	$passwd = CP_PASSWORD;
                                    	
                                    	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no;
                                    	
                                    	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
                                    	
                                    	$verify_result = ipriv_verify($signin_result[1], $public_key);
                                    	
                                    	//Mobile Verification	
                                    	$cpurl = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
                                    	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl);
                                    	$output = $check_output;
                                    	
                                    	$check_rsp = explode("\n",$check_output);
                                    	
                                    	$cp_val_error = '';
                                    	$cp_val_result = '';
                                    	$cp_val_transid = '';
                                    	$cp_val_msg = '';
                                    	$balance_api10 ='';
                                    	foreach($check_rsp as $data) {
                                    		if(strpos($data, 'REST=') !== false) {
                                    			$_data = explode("=",$data);
                                    			$balance_api10 = trim($_data[1]);
                                    		}
                                    	}
								} else if($result->api_id == '14') {
								    $url = "http://payment2all.com/multirecharge/balanceapi/run";
								    $fields = array(
                                    'username' =>$paymentall['username'] ,
                                    'password' => $paymentall['password']
                                    
                                    );
                                    
                                    //url-ify the data for the POST
                                    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                                    rtrim($fields_string, '&');
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    
                                    
                                    curl_setopt($ch,CURLOPT_POST, count($fields));
                                    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                                	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                                    
                                    
                                    $output = curl_exec($ch);
								 	$balance_api14 ='';
									$result=explode("#",$output);
								
									if($result[0]=='Success')
									{
									$balance_api14 = isset($result[1]) && $result[1] != '' ? $result[1] : '';
									}
								}  else if($result->api_id == '16') {
								   $url = "http://esuresolution.com/API/APIService.aspx?userid=".$esure['userid']."&pass=".$esure['pass']."&Get=CB&fmt=Json";
								   $balance_api16="";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance_api16 = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
								}
							}
							$date=date("Y-m-d");
							
								$virtual = $db->queryUniqueValue("SELECT balance FROM `apps_admin_wallet` where admin_wallet_id='1' ");
								$db->execute("INSERT INTO `api_balance`(`id`, `api2`, `api6`, `api7`, `api9`, `api10`, `api14`,`api16`, `date`, `virtual`) VALUES ('','".$balance_api2."','".$balance_api6."','".$balance_api7."','".$balance_api9."','".$balance_api10."','".$balance_api14."','".$balance_api16."','".$date."','".$virtual."')");
							
							
							
							
							
							
							