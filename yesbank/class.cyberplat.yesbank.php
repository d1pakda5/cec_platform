<?php
define('CP_SD','245840');
define('CP_AP','256750');
define('CP_OP','256751');
define('CP_PASSWORD','Vinod@123');
define('CP_AID','CEC001');
class CyberPlatYesBank {	
	
	function cpUrl() {
		$cp_url = array(
			'check'		=>	'https://in.cyberplat.com/cgi-bin/yb/yb_pay_check.cgi',
			'pay'			=>	'https://in.cyberplat.com/cgi-bin/yb/yb_pay.cgi',
			'status'	=>	'https://in.cyberplat.com/cgi-bin/yb/yb_pay_status.cgi'
		);
		return $cp_url;
	}
	
	function cpRequest($qs,$url){		
		$urln = $url."?inputmessage=".urlencode($qs);
		$opts = array( 
			'http'=>array( 
			'method'=>"GET", 
			'header'=>array("Content-type: application/x-www-form-urlencoded\r\n") 
			) 
		);		
		$context = stream_context_create($opts); 	
		$response = file_get_contents($urln,false,$context);
		return $response;
	}
	
	function cpIprivSign($qstring){
		$secret_key = file_get_contents(DIR."/library/secret.key");
		return ipriv_sign($qstring, $secret_key, CP_PASSWORD);
	}
	
	function cpIprivVerify($qstring){
		$public_key = file_get_contents(DIR."/library/pubkeys.key");
		return ipriv_verify($qstring, $public_key);
	}
	
	//CUSTOMER VALIDATION (CUSTOMER NOT REGISTERED)
	function cpCustomerValidation($account,$request_txn_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=5\n\rNUMBER=".$account."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//CUSTOMER REGISTRATION
	function cpCustomerRegistration($account,$request_txn_no,$fname,$lname,$dob,$city,$state,$pincode,$ben_mobile,$ben_name,$ben_account,$ben_ifsc) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=0\n\rNUMBER=".$account."\n\rPin=".$pincode."\n\rlName=".$lname."\n\rfName=".$fname."\n\rstate=".$state."\n\rbenName=".$ben_name."\n\rbenAccount=".$ben_account."\n\rCity=".$city."\n\rbenIFSC=".$ben_ifsc."\n\rbenMobile=".$ben_mobile."\n\rCustDOB=".$dob."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//ADD BENEFICIARY
	function cpBeneficiaryAdd($account,$request_txn_no,$ben_mobile,$ben_name,$bank_account,$ifsc) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=4\n\rbenName=".$ben_name."\n\rNUMBER=".$account."\n\rbenIFSC=".$ifsc."\n\rbenMobile=".$ben_mobile."\n\rbenAccount=".$bank_account."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//DELETE BENEFICIARY
	function cpBeneficiaryDelete($account,$request_txn_no,$ben_id,$otp_ref_code,$otp) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=6\n\rNUMBER=".$account."\n\rotc=".$otp."\n\rRcode=".$otp_ref_code."\n\rbenId=".$ben_id."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//VALIDATE BENEFICIARY
	function cpBeneficiaryValidate($account,$request_txn_no,$ben_id,$ben_mobile,$ben_name,$ben_account,$ben_bank,$ben_ifsc,$kyc_status) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=10\n\rbenName=".$ben_name."\n\rbenAccount=".$ben_account."\n\rbenIFSC=".$ben_ifsc."\n\rroutingType=IMPS\n\rbenBankName=".$ben_bank."\n\rNUMBER=".$account."\n\rbenMobile=".$ben_mobile."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=4.00\n\rbenId=".$ben_id."\n\rKeyKycStatus=".$kyc_status;
		return $result;
	}
	
	//MONEY REMITTANCE
	function cpBeneficiaryRemittance($account,$request_txn_no,$ben_id,$ben_name,$ben_account,$ben_bank,$ben_ifsc,$kyc_status,$amount,$amount_all) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=3\n\rbenName=".$ben_name."\n\rbenAccount=".$ben_account."\n\rbenIFSC=".$ben_ifsc."\n\rroutingType=IMPS\n\rbenBankName=".$ben_bank."\n\rNUMBER=".$account."\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount_all."\n\rbenId=".$ben_id."\n\rKeyKycStatus=".$kyc_status;
		return $result;
	}
	
	//OTP VERIFY
	function cpOtpVerify($account,$request_txn_no,$otp,$otp_ref_code,$ben_id,$request_for) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=2\n\rNUMBER=".$account."\n\rotc=".$otp."\n\rRcode=".$otp_ref_code."\n\rbenId=".$ben_id."\n\rRequestFor=".$request_for."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//OTP GENERATE
	function cpOtpGenerate($account,$request_txn_no,$request_for) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rType=9\n\rNUMBER=".$account."\n\rRequestFor=".$request_for."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//TRANSACTION HISTORY
	function cpTransactionHistory($account,$request_txn_no,$from,$to) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rAID=".CP_AID."\n\rNUMBER=".$account."\n\rType=14\n\rtransFrom=".$from."\n\rtransTo=".$to."\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00";
		return $result;
	}
	
	//REFUND TRANSACTION
	function cpRefundTransaction($account,$request_txn_no,$otp_ref_code,$otp,$txn_id,$amount) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nAID=".CP_AID."\r\nType=22\r\nNUMBER=".$account."\r\notc=".$otp."\r\noriginalmerchantTranId=".$txn_id."\r\nRcode=".$otp_ref_code."\r\nAMOUNT=".$amount."\r\nAMOUNT_ALL=".$amount;
		return $result;
	}
}
?>