<?php
session_start();
if(!isset($_SESSION['staff'])) {
	header("location:index.php");
	exit();
}
include("../config.php");
include("common.php");
$error = 0 ;
$admin_info = $db->queryUniqueObject("SELECT * FROM apps_admin WHERE admin_id='".$_SESSION['staff']."' ");
$admin = $db->queryUniqueObject("SELECT * FROM apps_admin_wallet");
if($admin) {
	$current_balance = $admin->balance;
} else {
	$current_balance = "0";
}
$countOffline = $db->countOf("apps_recharge", "api_id = '11' AND ( status = '1' OR status = '7' OR status = '8')");
$countFund = $db->countOf("fund_requests", "(user_type = '3' or user_type = '6') AND status = '0' ");
$countKyc = $db->countOf("userskyc", "status='0' ");







$meta['title'] = "Dashboard";
include("header.php");
?>
<div class="content">
	<div class="container-fluid">
	    <div class="alert alert-info" style="padding:8px">
			<marquee style="font-size: 15px;" scrollamount="4" behavior="scroll" direction="left">
			    <?php 
			    	$query = $db->query("SELECT * FROM api_list where status=1 and api_id not in (11,13) ORDER BY api_id ASC ");
						if($db->numRows($query) < 1) echo "<tr><td colspan='100%'>No Result Found</td></tr>";
						while($result = $db->fetchNextObject($query)) {
						?>
						
			<?php echo $result->api_name;?> : <span style="font-size: 15px;" class="badge">
			<?php 
			if($result->api_id == '1') {
									$url = "http://api.egpay.in/GetAPIBalance.aspx?UID=".$eg_pay['uid']."&UPASS=".$eg_pay['pass']."&SUBUID=".$eg_pay['subuid']."&SUBUPASS=".$eg_pay['subupass'];
									$output = file_get_contents($url);
									$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);
									$balance = isset($xml['API_Balance']) ? $xml['API_Balance'] : '';
									echo $balance;
								} else if($result->api_id == '2') {
								 	$url ="http://arrohservices.in/web-services/httpapi/check-balance?acc_no=".$arroh['acc_no']."&api_key=".$arroh['key'];
							        $output = file_get_contents($url);
									$balance_split = explode(" ", $output);
									echo $balance_split[1];
								} else if($result->api_id == '3') {
									$url = "http://smsalertbox.com/api/balance.php?uid=".$achariya['uid']."&pin=".$achariya['pin']."&route=recharge&format=json&version=4";
									$output = file_get_contents($url);
									$json = json_decode($output, true);
									$balance = isset($json['balance']) && $json['balance'] != '' ? $json['balance'] : '';
									echo $balance;
								} else if($result->api_id == '5') {
									//$url = "http://erechargeasia.in/API/APIService.aspx?userid=".$modem_rp['userid']."&pass=".$modem_rp['pass']."&Get=CB&fmt=Json";
									//$output = file_get_contents($url);
									//$xml = json_decode($output, true);
									//$balance = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									//echo $balance;
								} else if($result->api_id == '6') {
									$url = "http://roundpayapi.in/API/APIService.aspx?userid=".$roundpay['userid']."&pass=".$roundpay['pass']."&Get=CB";
									$output = file_get_contents($url);
									$xml = json_decode(json_encode((array) simplexml_load_string($output)), 1);
									$balance = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									echo $balance;
								} else if($result->api_id == '7') {
									//$url = "http://appone.exioms.com/api/v3_1/getWalletAmountv3.php/getWalletAmount?strUsername=".$exioms['uname']."&strAuthKey=".$exioms['key']."&format=1";									
									//$json = json_decode($output, 1);
									//$balance = isset($json['message']) && $json['message'] != '' ? $json['message'] : '';									
									$url = "http://rechargea2z.com/API/APIService.aspx?userid=".$rechargea2z['userid']."&pass=".$rechargea2z['pass']."&Get=CB&fmt=Json";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									echo $balance;
								} else if($result->api_id == '9') {
									$url = "http://ambikamultiservices.com/API/NewAPIService.aspx?userid=".$ambika['userid']."&pass=".$ambika['pass']."&Get=CB&fmt=Json";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									echo $balance;
								} else if($result->api_id == '10') {
									include(DIR."/library/cyberplat-balance.php");
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
								 	
									$result=explode("#",$output);
								
									if($result[0]=='Success')
									{
									$balance_paymentall = isset($result[1]) && $result[1] != '' ? $result[1] : '';
									}
									echo $balance_paymentall;
								} else if($result->api_id == '16') {
									$url = "http://esuresolution.com/API/APIService.aspx?userid=".$esure['userid']."&pass=".$esure['pass']."&Get=CB&fmt=Json";
									$output = file_get_contents($url);
									$xml = json_decode(($output), true);
									$balance = isset($xml['STATUS']) && $xml['STATUS'] != '' ? $xml['STATUS'] : '';
									echo $balance;
								}
								
								
								?>
								
								
			
			</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }?>
			</marquee>
			
		</div>
	    
		<?php if($countKyc > 0) { ?>
		<div class="alert alert-green">
			You have <span class="badge"><?php echo $countKyc;?></span> KYC verification request pending, to view click to button <a href="rpt-kyc.php" class="btn btn-sm btn-default">List KYC Request</a>
		</div>
		<?php } ?>	
		<?php if($countFund > 0) { ?>
		<div class="alert alert-red">
			You have <span class="badge"><?php echo $countFund;?></span> fund request is pending, to view click to button <a href="fund-request.php" class="btn btn-sm btn-info">List Fund Request</a>
		</div>
		<?php } ?>
		<?php if($countOffline > 0) { ?>
		<div class="alert alert-danger">
			You have <b><?php echo $countOffline;?></b> offline transactions are pending, to view <a href="offline-payment.php">click here</a>
		</div>
		<?php } ?>
		<div class="row">
			<div class="col-sm-8">
				<div class="row">
					<div class="col-sm-3">
						<div class="small-box">
							<a href="live-recharge.php" class="small-block">
								<i class="fa fa-refresh"></i>								
								<p>Live Recharge</p>
							</a>
						</div>
					</div>			
					<div class="col-sm-3">
						<div class="small-box">
							<a href="fund-add.php" class="small-block">
								<i class="fa fa-inr"></i>
								<p>Fund (Add)</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="fund-request.php" class="small-block">
								<i class="fa fa-inbox"></i>
								<p>Fund Request</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="complaints.php" class="small-block">
								<i class="fa fa-mobile"></i>
								<p>Complaints</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="tickets.php" class="small-block">
								<i class="fa fa-support"></i>
								<p>Support</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="operator.php" class="small-block">
								<i class="fa fa-signal"></i>
								<p>Operator</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="notification.php" class="small-block">
								<i class="fa fa-signal"></i>
								<p>Notifications</p>
							</a>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="small-box">
							<a href="rpt-kyc.php" class="small-block">
								<i class="fa fa-user"></i>								
								<p>KYC</p>
							</a>
						</div>
					</div>	
					<div class="col-sm-3">
						<div class="small-box">
							<a href="sale_rpt_form.php" class="small-block">
								<i class="fa fa-book"></i>
								<p>Monthly Sale Report</p>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="small-box">
					<a class="small-block">
						<span style="font-size:48px; line-height:72px; color:#819bd5;"><?php echo round($current_balance, 2);?> Rs</span>
						<p>Current Balance</p>
					</a>
				</div>
			</div>
			
		</div>
	</div>
</div>
<div class="container-fluid">
	<div class="panel padding-left-10 padding-top-10 padding-bottom-10">
		Last Login IP: <?php echo $admin_info->last_login_ip;?> at  <?php echo date('r', strtotime($admin_info->last_login_time));?>
	</div>
</div>
<?php include("footer.php");?>