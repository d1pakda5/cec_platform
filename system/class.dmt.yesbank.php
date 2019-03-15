<?php
define('CP_SD','245840');
define('CP_AP','256750');
define('CP_OP','256751');
define('CP_PASSWORD','Vinod@123');

class CyberPlatYesBank {	
	
	function cpUrl() {
		$cp_url = array(
			'check'=>'https://in.cyberplat.com/cgi-bin/pwdmt/pwdmt_pay_check.cgi',
			'pay'=>'https://in.cyberplat.com/cgi-bin/pwdmt/pwdmt_pay.cgi',
			'status'=>'https://in.cyberplat.com/cgi-bin/pwdmt/pwdmt_pay_status.cgi'
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
	
	//CUSTOMER VALIDATION
	function customerValidate($account,$request_txn_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=5\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=DMT";
		return $result;
	}	
	
	//CUSTOMER VALIDATION (CUSTOMER NOT REGISTERED)
	function cpSenderValidation($account,$request_txn_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=5\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=DMT";
		return $result;
	}	
	//ADD PAN CARD
	function cpPanAdd($account,$request_txn_no,$pan_card,$fname,$lname) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=".$fname."\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=".$lname."\n\rbenName=\n\rbenCode=\n\rPanCardNo=".$pan_card."\n\rType=20\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=test";
		return $result;
	}
	
	//CUSTOMER REGISTRATION
	function cpSenderRegistration($account,$request_txn_no,$fname,$lname) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=".$fname."\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=".$lname."\n\rbenName=\n\rbenCode=\n\rType=0\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=Sender Registration";
		return $result;
	}
	
	//ADD BENEFICIARY
	function cpBeneficiaryAdd($account,$request_txn_no,$ben_name,$bank_account,$ifsc) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=IMPS\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=".$bank_account."\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=".$ifsc."\n\rlName=\n\rbenName=".$ben_name."\n\rbenCode=\n\rType=4\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=test";
		return $result;
	}
	
	//VALIDATE BENEFICIARY ACCOUNTS
	function cpBeneficiaryValidate($account,$request_txn_no,$ben_type,$ifsc,$ben_code,$amount) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=".$ben_type."\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=".$ifsc."\n\rlName=\n\rbenName=\n\rbenCode=".$ben_code."\n\rType=10\n\rACCOUNT=\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount."\n\rCOMMENT=Beneficiary Validation";
		return $result;
	}
	
	//DELETE BENEFICIARY ACCOUNTS
	function cpBeneficiaryDelete($account,$request_txn_no,$ifsc,$ben_code) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=".$ifsc."\n\rlName=\n\rbenName=\n\rbenCode=".$ben_code."\n\rType=6\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=Beneficiary Validation";
		return $result;
	}
	
	//REMITTANCE
	function cpRemittance($account,$request_txn_no,$ben_type,$ifsc,$ben_code,$amount,$amount_all) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=".$ben_type."\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=".$ifsc."\n\rlName=\n\rbenName=\n\rbenCode=".$ben_code."\n\rType=3\n\rACCOUNT=\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount_all."\n\rCOMMENT=Beneficiary Validation";
		return $result;
	}
		
	//RE-INITIALIZE FAILED TRANSACTION
	function cpReInitiateRemittance($account,$request_txn_no,$trans_ref_no,$ben_type,$ifsc,$ben_code,$amount) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=".$ben_type."\n\rtransRefId=".$trans_ref_no."\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=".$ifsc."\n\rlName=\n\rbenName=\n\rbenCode=".$ben_code."\n\rType=7\n\rACCOUNT=\n\rAMOUNT=".$amount."\n\rAMOUNT_ALL=".$amount."\n\rCOMMENT=Beneficiary Validation";
		return $result;
	}
		
	//OTC CONFIRMATION
	function cpOtcConfirm($account,$request_txn_no,$otc,$otc_ref_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=".$otc."\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=".$otc_ref_no."\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=2\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=OTC";
		return $result;
	}
	
	//OTC RESENDING
	function cpOtcResend($account,$request_txn_no,$otc_ref_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=".$otc_ref_no."\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=9\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=OTC";
		return $result;
	}	
	
	//CUSTOMER BALANCE
	function cpSenderBalance($account,$request_txn_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=13\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=DMT";
		return $result;
	}
	
	//CUSTOMER TRANSACTION HISTORY
	function cpSenderTransaction($account,$request_txn_no) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rbenIFSC=\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=14\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=DMT";
		return $result;
	}	
	
	//GET BANK DETAILS
	function cpBankIfsc($account,$request_txn_no,$bank,$branch) {
		$result = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rpin=\n\rotc=\n\rfName=\n\rroutingType=\n\rtransRefId=\n\rmothersMaidenName=\n\rstate=\n\rbenAccount=\n\rTERM_ID=".CP_AP."\n\rbenMobile=\n\raddress=\n\rbirthday=\n\rNUMBER=".$account."\n\rgender=\n\rotcRefCode=\n\rbenNick=\n\rBankName=".$bank."\n\rbenIFSC=\n\rBranchName=".$branch."\n\rlName=\n\rbenName=\n\rbenCode=\n\rType=15\n\rACCOUNT=\n\rAMOUNT=1.00\n\rAMOUNT_ALL=1.00\n\rCOMMENT=test";
		return $result;
	}	
}
?>