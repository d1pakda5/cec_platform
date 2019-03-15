<?php
header('Content-type: text/html; charset=utf-8');
session_start();
define("BROWSE", "browse");
include("../config.php");
//include(DIR."/yesbank/class.cyberplat.yesbank.php");
//$yb = new CyberPlatYesBank();
$ip = $_SERVER['REMOTE_ADDR'];
$mode = "WEB";
$reference_txn_no = '';
//
$agent_info = $db->queryUniqueObject("SELECT user_id,uid,user_type,dist_id,pin,tds_deduct,tds_per,gst_deduct,has_gst,gst_type FROM apps_user WHERE user_id='".$_SESSION['retailer']."'");
if($agent_info) {
	if(isset($_GET["mobile"]) && $_GET["mobile"]!='') {
		$account = isset($_GET['mobile']) && $_GET['mobile'] ? mysql_real_escape_string($_GET['mobile']) : '0';
		$request_txn_no = time();	
		if(isset($_GET['request']) && $_GET['request']=='searchCustomer') {		
			if($_GET['mobile']=='7738890261') {	
				$jsDmtResponse = '{"RESP_CODE":200,"RESPONSE":"EPMONEY_CUST_VALIDATE_SUCCESS","RESP_MSG":"Customer validate successfully.","DATA":{"SENDER_TITLE":"Mr","SEDNER_FNAME":"Kuyte","SENDER_LNAME":"Saarang","SENDER_CUSTTYPE":"NON-KYC","SEDNER_GENDER":"Male","SENDER_EMAIL":"","SENDER_MOBILENO":7738890261,"SENDER_ALTMOBILENO":0,"SENDER_ADDRESS1":"Mumbai","SENDER_ADDRESS2":"","STATE":"Maharashtra","CITY":"Badlapur","PINCODE":"421503","SENDER_AVAILBAL":25000.0,"SENDER_MONTHLYBAL":25000.0,"SENDER_VERIFICATIONCODE":true,"SENDER_KYCSTATUS":"PENDING","SENDER_KYCTYPE":"NA","SENDER_REGISTERDATE":"2018-02-28 11:16:03.0","SENDER_ACTIVATIONDATE":"2018-02-28 11:18:31.0","SENDER_DOB":"1987-02-27","PREPAID_INSTRUMENTFLAG":false,"PANCARD_FLAG":false,"PANCARD_NO":"","BENEFICIARY_DATA":[{"BENE_ID":98780,"BENE_MOBILENO":"9766279517","BENE_NAME":"Samir","BENE_NICKNAME":"","BENE_BANKNAME":"CANARA BANK","BANK_ACCOUNTNO":"0215101031043","BANKIFSC_CODE":"CNRB0009999","BENE_OTP_VERIFIED":true,"IS_BENEVERIFIED":false}],"CUSTDOC_REJECT_FLAG":false,"PANCARD_REJECT_REASON":"NA","PANCARD_REJET_REMARKS":"NA","PANCARD_STATUS":"NA"}}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();			
		} else if(isset($_GET['request']) && $_GET['request']=='customerRegistration') {
			$fname = isset($_GET['fname']) && $_GET['fname']!='' ? mysql_real_escape_string($_GET['fname']) : '';		
			$lname = isset($_GET['lname']) && $_GET['lname']!='' ? mysql_real_escape_string($_GET['lname']) : '';
			$dob = isset($_GET['dob']) && $_GET['dob']!='' ? mysql_real_escape_string($_GET['dob']) : '';
			$city = isset($_GET['city']) && $_GET['city']!='' ? mysql_real_escape_string($_GET['city']) : '';
			$state = isset($_GET['state']) && $_GET['state']!='' ? mysql_real_escape_string($_GET['state']) : '';
			$pincode = isset($_GET['pincode']) && $_GET['pincode']!='' ? mysql_real_escape_string($_GET['pincode']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			if($_GET['mobile']=='9826386456') {	
				$jsDmtResponse = '{"benId":98780,"RESP_CODE":200,"Rcode":123776,"RESPONSE":"EPMONEY_ADD_CUSTOMER_BENEFICIARY_SUCCESS","RESP_MSG":"Customer and Beneficiary added successfully, OTP sent to registered number"}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryAdd') {
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			if($_GET['mobile']=='9826386456') {	
				$jsDmtResponse = '{"benId":98780,"RESP_CODE":200,"Rcode":123776,"RESPONSE":"EPMONEY_ADD_CUSTOMER_BENEFICIARY_SUCCESS","RESP_MSG":"Customer and Beneficiary added successfully, OTP sent to registered number"}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
			
		}  else if(isset($_GET['request']) && $_GET['request']=='ekycAdd') {
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';
			$bank_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '';
			$ifsc = isset($_GET['ifsc']) && $_GET['ifsc']!='' ? mysql_real_escape_string($_GET['ifsc']) : '';
			include(DIR."/yesbank/e-kyc-add.php");
			echo json_encode($jsDmtResponse);
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryValidate') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';			
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '0';
			$ben_bank = isset($_GET['ben_bank']) && $_GET['ben_bank']!='' ? mysql_real_escape_string($_GET['ben_bank']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			$kyc_status = isset($_GET['kyc_status']) && $_GET['kyc_status']!='' ? mysql_real_escape_string($_GET['kyc_status']) : '';			
			if($_GET['mobile']=='7738890261') {	
				$jsDmtResponse = '{"RESP_CODE":300,"RESPONSE":"SUCCESS","RESP_MSG":"Transaction successful","DATA":{"TRANSACTION_DATE":"Tue February.27.2018 07:07:41 PM","CUSTOMER_REFERENCE_NO":"1001453075753","SENDER_AVAILBAL":24498.0,"TRANSACTION_DETAILS":[{"RESP_CODE":300,"RESPONSE":"SUCCESS","RESP_MSG":"Transaction successful","VERSION":"2.0","UNIQUE_RESPONSENO":"5cb71d6a1bc311e8b6a40a0047330000","ATTEPMTNO":"1","TRANSFER_TYPE":"IMPS","LOW_BALANCE_ALERT":false,"STATUS_CODE":"COMPLETED","SUB_STATUS_CODE":"0","EP_REFERENCE_NO":"EP949613","BANK_REFERENCE_NO":"805819690440","REQUEST_REFERENCE_NO":"I15197386462660","RESPONSE_REFERENCE_NO":455958,"TRANSFER_AMOUNT":1.0,"INIT_PENDING":false,"TRANSACTIONN_FEE":3.0,"PAID_AMOUNT":1.0,"TXN_BENENAME":"TEJAS PRAKASH WADKAR"}],"BENEFICIARY_DETAILS":{"BENE_NAME":"TEJAS WADKAR","BANK_ACCOUNTNO":"02281540092065","BANKIFSC_CODE":"HDFC0CYNSBL","BENE_MMID":"","BENE_MOBILENO":"9004658687","BENE_BANKNAME":"HDFC BANK"},"CUSTOMER_DETAILS":{"CUST_NAME":"Kuyte Saarang","CUSTOMER_MOBILE":"7738890262","PANCARD_FLAG":false,"PANCARD_NO":""}}}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
			
		} else if(isset($_GET['request']) && $_GET['request']=='beneficiaryDelete') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_ref_code = isset($_GET['ben_ref_code']) && $_GET['ben_ref_code']!='' ? mysql_real_escape_string($_GET['ben_ref_code']) : '';
			$otp = isset($_GET['otp']) && $_GET['otp']!='' ? mysql_real_escape_string($_GET['otp']) : '';
			if($_GET['mobile']=='7738890261') {	
				$jsDmtResponse = '{"RESP_CODE":200,"RESPONSE":"EPMONEY_DELETE_BENEFICIARY_SUCCESS","RESP_MSG":"Beneficiary delete successfully."}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='moneyRemittance') {
			$ben_id = isset($_GET['ben_id']) && $_GET['ben_id']!='' ? mysql_real_escape_string($_GET['ben_id']) : '';
			$ben_mobile = isset($_GET['ben_mobile']) && $_GET['ben_mobile']!='' ? mysql_real_escape_string($_GET['ben_mobile']) : '';
			$ben_name = isset($_GET['ben_name']) && $_GET['ben_name']!='' ? mysql_real_escape_string($_GET['ben_name']) : '';			
			$ben_account = isset($_GET['ben_account']) && $_GET['ben_account']!='' ? mysql_real_escape_string($_GET['ben_account']) : '0';
			$ben_bank_name = isset($_GET['ben_bank_name']) && $_GET['ben_bank_name']!='' ? mysql_real_escape_string($_GET['ben_bank_name']) : '';
			$ben_ifsc = isset($_GET['ben_ifsc']) && $_GET['ben_ifsc']!='' ? mysql_real_escape_string($_GET['ben_ifsc']) : '';
			if($_GET['mobile']=='7738890261') {			
				$jsDmtResponse = '{"RESP_CODE":300,"RESPONSE":"SUCCESS","RESP_MSG":"Transaction successful","DATA":{"TRANSACTION_DATE":"Tue February.27.2018 05:40:10 PM","CUSTOMER_REFERENCE_NO":"1001452981224","SENDER_AVAILBAL":24599.0,"TRANSACTION_DETAILS":[{"RESP_CODE":300,"RESPONSE":"SUCCESS","RESP_MSG":"Transaction successful","VERSION":"2.0","UNIQUE_RESPONSENO":"221a15c41bb711e8a9c10a0047340000","ATTEPMTNO":"1","TRANSFER_TYPE":"IMPS","LOW_BALANCE_ALERT":false,"STATUS_CODE":"COMPLETED","SUB_STATUS_CODE":"0","EP_REFERENCE_NO":"EP844124","BANK_REFERENCE_NO":"805817667354","REQUEST_REFERENCE_NO":"I15197333951000","RESPONSE_REFERENCE_NO":455410,"TRANSFER_AMOUNT":100.0,"INIT_PENDING":false,"TRANSACTIONN_FEE":5.13,"PAID_AMOUNT":100.0,"TXN_BENENAME":"AJAY DUBEY"}],"BENEFICIARY_DETAILS":{"BENE_NAME":"Ajay","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_MMID":"","BENE_MOBILENO":"7738890262","BENE_BANKNAME":"HDFCBANK"},"CUSTOMER_DETAILS":{"CUST_NAME":"Kuyte Saarang","CUSTOMER_MOBILE":"7738890262","PANCARD_FLAG":false,"PANCARD_NO":""}}}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otpVerify') {
			$otp_ref_code = isset($_GET['otp_ref_code']) && $_GET['otp_ref_code']!='' ? mysql_real_escape_string($_GET['otp_ref_code']) : '';
			$otp = isset($_GET['otp']) && $_GET['otp']!='' ? mysql_real_escape_string($_GET['otp']) : '';
			if($_GET['mobile']=='9826386456') {	
				$jsDmtResponse = '{"RESP_CODE":200,"RESPONSE":"EPMONEY_VERIFY_OTP_SUCCESS","RESP_MSG":"OTP verification successfully."}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='otpResend') {
			$otp_request_for = isset($_GET['otp_request_for']) && $_GET['otp_request_for']!='' ? mysql_real_escape_string($_GET['otp_request_for']) : '';
			$otp_ref_code = isset($_GET['otp_ref_code']) && $_GET['otp_ref_code']!='' ? mysql_real_escape_string($_GET['otp_ref_code']) : '';
			if($_GET['mobile']=='9826386456') {	
				$jsDmtResponse = '{"RESP_CODE":200,"Rcode":122479,"RESPONSE":"EPMONEY_GENERATE_OTPBENEVALIDATION_SUCCESS","RESP_MSG":"OTP sent to registered number for beneficiary verification"}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
		} elseif(isset($_GET['request']) && $_GET['request']=='otpGenerate') {
			$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '';
			$request_for = isset($_GET['request_for']) && $_GET['request_for']!='' ? mysql_real_escape_string($_GET['request_for']) : '';
			if($_GET['mobile']=='7738890261') {	
				$jsDmtResponse = '{"RESP_CODE":200,"Rcode":122479,"RESPONSE":"EPMONEY_GENERATE_OTPBENEVALIDATION_SUCCESS","RESP_MSG":"OTP sent to registered number for beneficiary verification"}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();			
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionHistory') {
			if($_GET['mobile']=='7738890261') {	
			$jsDmtResponse = '{"RESP_CODE":200,"RESPONSE":"EPMONEY_LIST_TRANSACTION_SUCCESS","RESP_MSG":"Transaction fetch successfully.","DATA":{"TRANSACTION_DETAILS":[{"TXN_ID":126684,"EP_REFERENCE_NO":"EP957678","BANK_REFERENCE_NO":"805714388630","CUSTOMER_REFERENCE_NO":"1001452072761","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-26 02:00:24","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15196338208670","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":127358,"EP_REFERENCE_NO":"EP152323","BANK_REFERENCE_NO":"805717449615","CUSTOMER_REFERENCE_NO":"1001452239470","BENE_MOBILENO":"9004658687","BENE_NAME":"TEJASWADKAR","BENE_ID":94700,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"USHA KIRAN COMPLEX BARSHI ROAD LATUR","BANK_ACCOUNTNO":"02281540092065","BANKIFSC_CODE":"HDFC0CYNSBL","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":1.0,"TRANSFER_AMOUNT":1.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-26 05:59:29","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15196481677920","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"BENEVALIDATE","REINIT_TXN_ID":""},{"TXN_ID":127580,"EP_REFERENCE_NO":"EP718465","BANK_REFERENCE_NO":"805719465062","CUSTOMER_REFERENCE_NO":"1001452308374","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-26 07:02:32","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15196519511190","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":129878,"EP_REFERENCE_NO":"EP564797","BANK_REFERENCE_NO":"","CUSTOMER_REFERENCE_NO":"1001452874348","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-27 03:06:48","TRANSACTION_STATUS":"REFUNDED","TRANSACTION_STATUSMESSAGE":"Forbidden: The identity provided does not have the required authority","ORDER_ID":"I15197242060700","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":130131,"EP_REFERENCE_NO":"EP627280","BANK_REFERENCE_NO":"","CUSTOMER_REFERENCE_NO":"1001452935914","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-27 04:43:44","TRANSACTION_STATUS":"REFUNDED","TRANSACTION_STATUSMESSAGE":"Invalid Benificiary MMID/Mobile Number","ORDER_ID":"I15197300238840","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":130193,"EP_REFERENCE_NO":"EP108745","BANK_REFERENCE_NO":"805817659738","CUSTOMER_REFERENCE_NO":"1001452956897","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-27 05:11:13","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15197316688020","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":130280,"EP_REFERENCE_NO":"EP844124","BANK_REFERENCE_NO":"805817667354","CUSTOMER_REFERENCE_NO":"1001452981224","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-27 05:39:57","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15197333951000","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""},{"TXN_ID":130331,"EP_REFERENCE_NO":"EP154839","BANK_REFERENCE_NO":"805817671789","CUSTOMER_REFERENCE_NO":"1001452997871","BENE_MOBILENO":"9769670622","BENE_NAME":"AJAY DUBEY","BENE_ID":91352,"BENE_NICKNAME":"","BENE_BANKNAME":"HDFC BANK","BANK_ADDRESS":"GROUND FLOOR,EXPRESS TOWERS,NARIMAN POINTMUMBAIMAHARASHTRA400021","BANK_ACCOUNTNO":"50100096717088","BANKIFSC_CODE":"HDFC0000291","BENE_CODE":"","BENE_STATUS":"ACTIVE","BENE_OTP_VERIFIED":true,"PAID_AMOUNT":100.0,"TRANSFER_AMOUNT":100.0,"CHARGE_AMOUNT":0.0,"TRANSACTION_DATE":"2018-02-27 05:58:02","TRANSACTION_STATUS":"SUCCESS","TRANSACTION_STATUSMESSAGE":"Transaction successful","ORDER_ID":"I15197344793100","AID":"CP1234","MID":"EP00009","CP":"CYBPR4211","ST":"REMDOMESTIC","REINIT_TXN_ID":""}]}}';
			} else {
				$jsDmtResponse = '{"RESP_CODE":"224","RESP_MSG":"Customer not exist","RQST_MOBILE":"'.$account.'"}';
			}
			echo $jsDmtResponse;
			exit();
		} elseif(isset($_GET['request']) && $_GET['request']=='transactionRefund') {
			include(DIR."/yesbank/transaction-refund.php");
			echo $jsDmtResponse;
			exit();
			
		} elseif(isset($_GET['request']) && $_GET['request']=='bankIfsc') {
			$bank = isset($_GET['bank']) && $_GET['bank']!='' ? mysql_real_escape_string($_GET['bank']) : '';
			$branch = isset($_GET['branch']) && $_GET['branch']!='' ? mysql_real_escape_string($_GET['branch']) : '';
			include(DIR."/yesbank/bank-ifsc.php");
			echo $jsDmtResponse;
			exit();
			
		} else {
			echo '{"RESP_CODE":"20","RESP_MSG":"Invalid request"}';		
			exit();
		}
	} else {
		echo '{"RESP_CODE":"21","RESP_MSG":"Invalid sender mobile number"}';		
		exit();
	}
} else {
	echo '{"RESP_CODE":"22","RESP_MSG":"Invalid agent/retailer detail"}';		
	exit();
}