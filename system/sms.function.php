<?php
function smsSendSingle($mobile, $message, $module = '') {
	global $db;	
	$module = $db->queryUniqueObject("SELECT * FROM sms_module WHERE sms_module_name = '".$module."' ");
	if($module) {
		if($module->sms_api_id == '1') {
			$api_id = '1';
			//$url = "http://sms.shubhsandesh.com/sendsms.asp?user=erechargeasia&password=ANKIT&sender=RECHRG&text=".urlencode($message)."&PhoneNumber=".$mobile."&track=1";
			$url = "http://sms.clickecharge.com/api/sendmsg.php?user=clickecharge&pass=123456&sender=CLICKR&phone=".$mobile."&text=".urlencode($message)."&priority=ndnd&stype=normal";
		} else if ($module->sms_api_id == '2') {
			$api_id = '2';
			$url = "http://49.50.67.32/smsapi/httpapi.jsp?username=clickecharge&password=vinod-560&from=CLICKR&to=".$mobile."&text=".urlencode($message);
		} else if ($module->sms_api_id == '3') {
			$api_id = '3';
			$url = "http://203.212.70.200/smpp/sendsms?username=ankitsenderid&password=hotmail02&to=".$mobile."&from=RECHRG&udh=&text=".urlencode($message)."&dlr-mask=19&dlr-url";
		} else if ($module->sms_api_id == '4') {
			$api_id = '4';
			$url="http://bulksms.clickecharge.com/api/sendhttp.php?authkey=218607Aq0elvjbB4ol5b13f181&mobiles=".$mobile."&message=".urlencode($message)."&sender=CLICKR&route=4&country=91";
		} 
		else {
			$api_id = '3';
			$url = "http://203.212.70.200/smpp/sendsms?username=ankitsenderid&password=hotmail02&to=".$mobile."&from=RECHRG&udh=&text=".urlencode($message)."&dlr-mask=19&dlr-url";
		}
	} else {
		$api_id = '4';		
	    	$url="http://bulksms.clickecharge.com/api/sendhttp.php?authkey=218607Aq0elvjbB4ol5b13f181&mobiles=".$mobile."&message=".urlencode($message)."&sender=CLICKR&route=4&country=91";
	}
	/*
	* Submit to SMS API
	*/
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	/*
	* Insert into database
	*/
	$db->execute("INSERT INTO sent_sms(sent_id, api_id, mobile, txt_message, sent_date, status, sent_details) values('', '".$api_id."', '".$mobile."', '".$message."', NOW(), '0', '".mysql_real_escape_string($output)."') ");			
	return true;
}

function smsSendUrl($mobile, $message, $api) {
	global $db;	
	$url = "";
	$sms_api = $db->queryUniqueObject("SELECT * FROM sms_api WHERE sms_api_id = '".$api."' ");
	if($sms_api) {
		if($sms_api->sms_api_id == '1') {
			$api_id = '1';
			//$url = "http://sms.shubhsandesh.com/sendsms.asp?user=erechargeasia&password=ANKIT&sender=RECHRG&text=".urlencode($message)."&PhoneNumber=".$mobile."&track=1";
			$url = "http://sms.clickecharge.com/api/sendmsg.php?user=clickecharge&pass=123456&sender=CLICKR&phone=".$mobile."&text=".urlencode($message)."&priority=ndnd&stype=normal";
		} else if ($sms_api->sms_api_id == '2') {
			$api_id = '2';
			//$url = "http://dndopen.dove-sms.com/TransSMS/SMSAPI.jsp?username=erecharge&password=576127835&sendername=RECHRG&mobileno=".$mobile."&message=".urlencode($message);
			$url = "http://49.50.67.32/smsapi/httpapi.jsp?username=clickecharge&password=vinod-560&from=CLICKR&to=".$mobile."&text=".urlencode($message);
		} else if ($sms_api->sms_api_id == '3') {
			$api_id = '3';
			//$url = "http://203.212.70.200/smpp/sendsms?username=ankitsenderid&password=hotmail02&to=".$mobile."&from=RECHRG&udh=&text=".urlencode($message)."&dlr-mask=19&dlr-url";
			$url = "http://203.212.70.200/smpp/sendsms?username=ankitsenderid&password=hotmail02&to=".$mobile."&from=RECHRG&udh=&text=".urlencode($message)."&dlr-mask=19&dlr-url";
		} else if ($sms_api->sms_api_id == '4') {
			$api_id = '4';
			$url="http://bulksms.clickecharge.com/api/sendhttp.php?authkey=218607Aq0elvjbB4ol5b13f181&mobiles=".$mobile."&message=".urlencode($message)."&sender=CLICKR&route=4&country=91";
		} 
	}
	if($url!="") {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		$db->execute("INSERT INTO sent_sms(sent_id, api_id, mobile, txt_message, sent_date, status, sent_details) values('', '".$api_id."', '".$mobile."', '".$message."', NOW(), '0', '".mysql_real_escape_string($output)."') ");			
		return true;
	} else {
		return false;
	}
}

function smsUserActivation($name, $userid, $password, $date) {
	$result = "Welcome to ".$name."! Account activated on ".$date.". Your USERID: ".$userid.", passsword: ".$password;
	return $result;
}

function smsPinChange($name, $pin) {
	$result = $name.": Pin has been successfully changed, Your new Pin is ".$pin;
	return $result;
}

function smsPasswordChange($name, $password) {
	$result = $name.": Password has been successfully changed, Your new Password is ".$password;
	return $result;
}

function smsBalanceCheck($name, $balance) {
	$result = $name.", Your current Balance is Rs.".$balance;
	return $result;
}

function smsFundTransfer($amount, $from, $to){
  $result = "Rs. ".$amount." transferred successfully from ".$from." to ".$to;
	return $result;
}

function smsFundDeduct($amount, $from, $to){
  $result = "Rs. ".$amount." deduct successfully from ".$from." to ".$to;
	return $result;
}

function smsRechargeStatus($status, $mobile, $amount, $date, $txn_no) {	
  $result = "Recharge Status: ".$status." on ".$mobile." Rs.".$amount.", (".$date.") Txn:".$txn_no;
	return $result;
}

function smsRechargeSuccess($operator, $mobile, $amount, $txn_no, $balance){
  $result = "Recharge success on ".$mobile." (".$operator.") Txn:".$txn_no.", Rs.".$amount.". Ur Bal Rs.".$balance;
	return $result;
}

function smsRechargeFail($operator, $mobile, $amount, $txn_no, $balance, $reason){
  $result = "Recharge fail on ".$mobile." (".$operator.") Txn:".$txn_no.", Rs.".$amount.", (".$reason.") Ur Bal Rs.".$balance;
	return $result;
}

function getProductsWelcome($product,$amount,$txn_no){
	$result = "Dear Customer, Thanks for purchasing with us! Purchase summary: ".$product.", Amount: ".$amount." Rs, Reference No.:".$txn_no;
	return $result;
}