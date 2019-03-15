<?php
	include(DIR."/system/class.cyberplat.php");
	$cp = new CyberPlat();
	include(DIR."/library/cyberplat-error-code.php");
	
	$request_txn_no = time();
	$op_cyberplat = $operator_info->operator_longcode;
	
	define('CP_SD','245840');
	define('CP_AP','256750');
	define('CP_OP','256751');
	define('CP_PASSWORD','Vinod@123');	
	
	$secret_key = file_get_contents(DIR."/library/secret.key");
	$public_key = file_get_contents(DIR."/library/pubkeys.key");
	$passwd = CP_PASSWORD;
	
	$addl_param = "";
	if($op_cyberplat=='TD' || $op_cyberplat=='XO' || $op_cyberplat=='RK' || $op_cyberplat=='YU') {
		//Docomo Special, Uninor Special
		$addl_param = "ACCOUNT=2\n\r";
	}
	
	if($op_cyberplat=='LB') {
		$addl_param = "ACCOUNT=".$customer_account."\n\rAuthenticator3=".$bsnl_service_type."\n\r";		
	}
	
	if($op_cyberplat == 'EMS') {
		$addl_param = "ACCOUNT=".$billing_unit."\n\rAuthenticator3=".$pc_number."\n\r";		
	}
	
	$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rNUMBER=".trim($account)."\n\rAMOUNT=".trim($amount)."\n\rAMOUNT_ALL=".trim($amount)."\n\r".$addl_param."\n\rTERM_ID=".CP_AP."\n\rCOMMENT=Recharge";
	
	$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
	
	$verify_result = ipriv_verify($signin_result[1], $public_key);
	
	//echo "<br>";
	//print_r($cp_string);
	//echo "<br>";
	
	//Mobile Verification	
	$cpurl = $cp->cyberplatUrl($op_cyberplat);
	$check_output = $cp->cyberplatRequest($signin_result[1], $cpurl['check']);
	$output = $check_output;
	
	$check_rsp = explode("\n",$check_output);	
	
	$cp_val_error = '';
	$cp_val_result = '';
	$cp_val_transid = '';
	$cp_val_addinfo = '';
	$cp_val_price = '';
	$cp_val_errmsg = '';
	
	foreach($check_rsp as $data) {
		if(strpos($data, 'ERROR=') !== false) {
			$_data = explode("=",$data);
			$cp_val_error = trim($_data[1]);
		}
		if(strpos($data, 'RESULT=') !== false) {
			$_data = explode("=",$data);
			$cp_val_result = trim($_data[1]);
		}
		if(strpos($data, 'TRANSID=') !== false) {
			$_data = explode("=",$data);
			$cp_val_transid = trim($_data[1]);
		}
		if(strpos($data, 'ADDINFO=') !== false) {
			$_data = explode("=",$data);
			$cp_val_addinfo = trim($_data[1]);
		}
		if(strpos($data, 'PRICE=') !== false) {
			$_data = explode("=",$data);
			$cp_val_price = trim($_data[1]);
		}
		if(strpos($data, 'ERRMSG=') !== false) {
			$_data = explode("=",$data);
			$cp_val_errmsg = trim($_data[1]);
		}
	}
	echo "<br>";
	if($cp_val_addinfo!='') {
		echo "<b>Response Details:</b>";
		echo "<br>";
		$_adInfo = explode("%20",$cp_val_addinfo);
		$_adReplace = array("%3C"=>"", "%3E"=>"");
		$_ad_bd = strtr($_adInfo[1], $_adReplace);
		$_ad_bdd = strtr($_adInfo[2], $_adReplace);
		echo "Bill Number: ".strtr($_adInfo[0], $_adReplace)."<br>";
		if($_ad_bd!='NA') {
			echo "Bill Date: ".substr($_ad_bd, 6, 2)."-".substr($_ad_bd, 4, 2)."-".substr($_ad_bd, 0, 4)."<br>";
		} else {
			echo "Bill Date: ".$_ad_bd."<br>";
		}
		if($_ad_bdd!='NA') {
			echo "Bill Due Date: ".substr($_ad_bdd, 6, 2)."-".substr($_ad_bdd, 4, 2)."-".substr($_ad_bdd, 0, 4)."<br>";
		} else {
			echo "Bill Due Date: ".$_ad_bdd."<br>";
		}		
		echo "Outstanding Bill Amount: ".strtr($_adInfo[3], $_adReplace)."<br>";
		echo "Partial Bill: ".strtr($_adInfo[4], $_adReplace)."<br>";
		echo "Customer Name as applicable: ".strtr($_adInfo[5], $_adReplace)."<br>";	
		echo "<br>";
	}
	if($cp_val_error=='0') {
		echo "<i class='fa fa-check-circle text-success'></i> <b>".cpCode($cp_val_error)."</b>";	
	} else {
		echo "<i class='fa fa-times-circle text-danger'></i> <b>".cpCode($cp_val_error)."</b>";	
	}
	echo "<br><br>";
?>