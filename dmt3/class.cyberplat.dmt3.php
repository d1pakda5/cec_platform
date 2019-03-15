<?php
define('CP_SD','245840');
define('CP_AP','256750');
define('CP_OP','256751');
define('CP_PASSWORD','Vinod@123');
define('CP_AID','CEC001');
class CyberPlatdmt3 {	
	
	function cpUrl() {
		$cp_url = array(
			'check'		=>	'https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi',
			'pay'			=> 'https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi',
			'status'	=>	'https://in.cyberplat.com/cgi-bin/instp/instp_pay_status.cgi'
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
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=5\r\nNUMBER=".$account."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//CUSTOMER REGISTRATION
	function cpCustomerRegistration($account,$request_txn_no,$fname,$lname,$pin) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=0\r\nNUMBER=".$account."\r\nlName=".$lname."\r\nfName=".$fname."\r\nPin=".$pin."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//ADD BENEFICIARY
	function cpBeneficiaryAdd($account,$request_txn_no,$ben_mobile,$ben_fname,$ben_lname,$remId,$bank_account,$ifsc) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=4\r\nremId=".$remId."\r\nlName=".$ben_lname."\r\nfName=".$ben_fname."\r\nNUMBER=".$account."\r\nbenAccount=".$bank_account."\r\nbenIFSC=".$ifsc."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//DELETE BENEFICIARY
	function cpBeneficiaryDelete($account,$request_txn_no,$benId,$remId) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=6\r\nremId=".$remId."\r\nbenId=".$benId."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//VALIDATE BENEFICIARY
	function cpBeneficiaryValidate($account,$request_txn_no,$ben_account,$ifsc) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=10\r\nNUMBER=".$account."\r\nbenAccount=".$ben_account."\r\nbenIFSC=".$ifsc."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//MONEY REMITTANCE
	function cpBeneficiaryRemittance($account,$request_txn_no,$benId,$amount,$amount_all) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=3\r\nNUMBER=".$account."\r\nroutingType=IMPS\r\nbenId=".$benId."\r\nAMOUNT=".$amount."\r\nAMOUNT_ALL=".$amount_all;
		return $result;
	}
	
	//OTP VERIFY
	function cpOtpVerify($account,$request_txn_no,$otc,$benId,$remId,$otc_for) {
	    if($otc_for=='beneficiary')
	    {
	        $type=2;
	    }
	    else if($otc_for=='bendelete')
	    {
	        $type=23;
	    }
	    
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=".$type."\r\nremId=".$remId."\r\nbenId=".$benId."\r\notc=".$otc."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//OTP GENERATE
	function cpOtpGenerate($account,$request_txn_no,$benId,$ben_account,$fName,$lName,$remId,$ben_bank,$ifsc,$pincode) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nType=9\r\nremId=".$remId."\r\nNUMBER=".$account."\r\nlName=".$lName."\r\nfName=".$fName."\r\nPin=".$pincode."\r\nbenAccount=".$ben_account."\r\nbenIFSC=".$ifsc."\r\nbenId=".$benId."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//TRANSACTION HISTORY
	function cpTransactionHistory($account,$request_txn_no,$from,$to) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nAID=".CP_AID."\r\nNUMBER=".$account."\r\nType=14\r\ntransFrom=".$from."\r\ntransTo=".$to."\r\nAMOUNT=1.00\r\nAMOUNT_ALL=1.00";
		return $result;
	}
	
	//REFUND TRANSACTION
	function cpRefundTransaction($account,$request_txn_no,$otp_ref_code,$otp,$txn_id,$amount) {
		$result = "SD=".CP_SD."\r\nAP=".CP_AP."\r\nOP=".CP_OP."\r\nSESSION=".$request_txn_no."\r\nAID=".CP_AID."\r\nType=22\r\nNUMBER=".$account."\r\notc=".$otp."\r\noriginalmerchantTranId=".$txn_id."\r\nRcode=".$otp_ref_code."\r\nAMOUNT=".$amount."\r\nAMOUNT_ALL=".$amount;
		return $result;
	}
}
?>