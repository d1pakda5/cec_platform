<?php
error_reporting(E_ALL);
//error_reporting(1);
//code for recharge system
//by pksingh on 25 may 2014
//define global valiables

define('CP_SD','1003829');
define('CP_AP','1003851');
define('CP_OP','1003852');
define('CP_PASSWORD','3333333333');

//Airtel
$check_url="http://ru-demo.cyberplat.com/cgi-bin/DealerSertification/de_pay_check.cgi";
$pay_url="http://ru-demo.cyberplat.com/cgi-bin/DealerSertification/de_pay.cgi";
$verify_url="http://ru-demo.cyberplat.com/cgi-bin/DealerSertification/de_pay_status.cgi";

//Reliance 

/*$check_url="https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi";//live urls for relience
$pay_url="https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi";
$verify_url="https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi";*/

$phNbr="4950000003";
$amount="10";

$secKey = file_get_contents('secret.key');
$pubKey = file_get_contents('pubkeys.key');
$passwd = CP_PASSWORD;


$sessPrefix=rand(100,300);
$sess=$sessPrefix.$phNbr.time();
$sess=substr($sess,-20);


echo "<br>==============Number Verification==========<br/>";

$querString="SD=".CP_SD."\n\rAP=".CP_AP."\n\rOP=".CP_OP."\n\rSESSION=$sess\n\rNUMBER=$phNbr\n\rAMOUNT=$amount\n\rAMOUNT_ALL=$amount\n\rCOMMENT=Test recharge";

echo "<br>Sign In:<br/>";
$signInRes = ipriv_sign($querString, $secKey, $passwd);
print_r($signInRes);
$signInMsg=$signInRes[1];

echo "<br>Sign In Verification:<br/>";
$verifyRes = ipriv_verify($signInMsg, $pubKey);

print_r($verifyRes);

echo "<br>==============Phone Verification Response===================<br/>";
$verifyRes=get_query_result($signInMsg,$check_url);
print_r($verifyRes);



echo "<br>==============Phone Recharge Request=======================<br/>";

$phoneRechargeRes=get_query_result($signInMsg,$pay_url);	
print_r($phoneRechargeRes);



echo "<br/>==============Phone Recharge Verification=====================<br/>";

$querString="SESSION=$sess";
$signInRes = ipriv_sign($querString, $secKey, $passwd);
$signInMsg=$signInRes[1];

$paymentVerificationRes=get_query_result($signInMsg,$verify_url);
print_r($paymentVerificationRes);



function get_query_result($qs,$url){
		
	$url=$url."?inputmessage=".urlencode($qs);
	$opts = array( 
	  'http'=>array( 
		'method'=>"GET", 
		'header'=>array("Content-type: application/x-www-form-urlencoded\r\n") 
	  ) 
	); 
	
	$context = stream_context_create($opts); 	
	$phoneVerificationRes = file_get_contents($url,false,$context);		
	return $phoneVerificationRes;
}	
?>
