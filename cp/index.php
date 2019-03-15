<?php
	error_reporting(E_ALL);
	$request_txn_no = time();
	
	echo "Balance Check";
	echo "<br>";
	echo "<hr>";
	
	function cyberplatRequest($qs,$url){	
		global $db;	
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
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents("secret.key");
	$public_key = file_get_contents("pubkeys.key");
	$passwd = CP_PASSWORD;	
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no;
	echo "Parameter";
	echo "<br>";
	echo "<hr>";
	print_r($cp_string);
	echo "<br>";
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	echo "Sign In Result";
	echo "<br>";
	echo "<hr>";
	print_r($signin_result);
	echo "<br>";
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	echo "Verify Result";
	echo "<br>";
	echo "<hr>";
	print_r($verify_result);
	echo "<br>";
	//Mobile Verification	
	$cpurl = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
	$check_output = cyberplatRequest($signin_result[1], $cpurl);
	$output = $check_output;
	echo "Result Output";
	echo "<br>";
	echo "<hr>";
	print_r($output);
	echo "<br>";
	echo "<br>";
	
	$check_rsp = explode("\n",$check_output);	
	$cp_val_error = '';
	$cp_val_result = '';
	$cp_val_transid = '';
	$cp_val_msg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'REST=') !== false) {
			$_data = explode("=",$data);
			$cp_balance = trim($_data[1]);
		}
	}
	
	echo $cp_balance;
	
?>