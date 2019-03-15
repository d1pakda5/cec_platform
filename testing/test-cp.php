<?php
include("../config.php");
include(DIR."/system/class.cyberplat.php");
$cp = new CyberPlat();

define('CP_SD','245840');
define('CP_AP','256750');
define('CP_OP','256751');
define('CP_PASSWORD','Vinod@123');	

$secret_key = file_get_contents(DIR."/library/secret.key");
$public_key = file_get_contents(DIR."/library/pubkeys.key");
$passwd = CP_PASSWORD;

$request_txn_no = time();
$operator = isset($_GET['o']) && $_GET['o']!='' ? $_GET['o'] : 0;
$account = isset($_GET['m']) && $_GET['m']!='' ? $_GET['m'] : 0;
$amount = isset($_GET['a']) && $_GET['a']!='' ? $_GET['a'] : 0;
$addl_param = "";


$cp_string = "SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=".$request_txn_no."\n\rNUMBER=".trim($account)."\n\rAMOUNT=".trim($amount)."\n\rAMOUNT_ALL=".trim($amount)."\n\r".$addl_param."COMMENT=Recharge";

$signin_result = ipriv_sign($cp_string, $secret_key, $passwd);
$verify_result = ipriv_verify($signin_result[1], $public_key);

$op_url = $cp->cyberplatUrl($operator);

$qs = $signin_result[1];

print_r($op_url['check']);
echo "<hr>";
print_r($qs);
echo "<hr>";
$url = $op_url['check']."?inputmessage=".urlencode($qs);
echo "<hr>";
//exit();
$opts = array( 
	'http'=>array( 
	'method'=>"POST", 
	'header'=>array("Content-type: application/x-www-form-urlencoded\r\n") 
	) 
);		
$context = stream_context_create($opts); 	
$response = file_get_contents($url,false,$context);
var_dump($response);
echo "<hr>";
?>