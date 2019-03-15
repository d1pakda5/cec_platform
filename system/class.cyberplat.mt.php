<?php
class CyberPlatMT {
	function cyberplatUrl() {
		$cp_url = array(
			'check'=>'https://in.cyberplat.com/cgi-bin/mrp/mrp_pay_check.cgi',
			'pay'=>'https://in.cyberplat.com/cgi-bin/mrp/mrp_pay.cgi',
			'status'=>'https://in.cyberplat.com/cgi-bin/mrp/mrp_pay_status.cgi'
		);
		return $cp_url;
	}
	
	function cyberplatRequest($qs,$url){		
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
	
	//CUSTOMER VALIDATION (CUSTOMER NOT REGISTERED)
	
	
	
	//CUSTOMER REGISTRATION
	
	//CUSTOMER VALIDATION
	
	//ADD BENEFICIARY
	
	//OTC CONFIRMATION
	
	//FETCH BENEFICIARY DETAIL
	
	//GET SERVICE CHARGE
	
	//REMITTANCE
	
	//STATUS CHECK API
	
	//DELETE BENEFICIARY ACCOUNTS
		
	//OTC CONFIRMATION
	
	//OTC RESENDING
	
	//RE-INITIALIZE

}
?>