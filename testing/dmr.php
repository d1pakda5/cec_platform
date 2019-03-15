<?php
define("BROWSE", "browse");
include('../config.php');
$ip = $_SERVER['REMOTE_ADDR'];
include(DIR."/system/class.cyberplat.spaisa.php");
$sp = new CyberPlatSPaisa();
$mode = "API";

$account = "8770789403";
$request_txn_no = time();
$ben_type = 'IMPS';
$ifsc = 'SBIN0005855';
$ben_code = 'O3GFmEf';
$amount = '150';
$amount_all = '34';

$moneyRemittance = $sp->cpRemittance($account,$request_txn_no,$ben_type,$ifsc,$ben_code,$amount,$amount_all);	
$signInResult = $sp->cpIprivSign($moneyRemittance);
$verifyResult = $sp->cpIprivVerify($signInResult[1]);	
$cpUrl = $sp->cpUrl();	
echo date("d-m-Y H:i:s")."<br>";

$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'TEST PRE CHECK', '".$signInResult[1]."::".$cpUrl['check']."')");

$checkOutput = $sp->cpRequest($signInResult[1], $cpUrl['check']);

$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'TEST RSP CHECK', '".$checkOutput."')");	

$checkExplode = explode("\n",$checkOutput);	
echo "<br>==============================================================================<br>";
echo $signInResult[1];
echo "<br>==============================================================================<br>";
echo $checkOutput;
echo "<br>".date("d-m-Y H:i:s")."<br>";
echo "<br>==============================================================================<br>";

$dmt_check_status = "2";
$dmt_value_error = '';
$dmt_value_result = '';
$dmt_value_transid = '';
$dmt_value_addinfo = '';
$dmt_value_errmsg = '';

foreach($checkExplode as $chk_data) {
	if(strpos($chk_data, 'ERROR=') !== false) {
		$chk_data_error = explode("=",$chk_data);
		$dmt_value_error = trim($chk_data_error[1]);
	}
	if(strpos($chk_data, 'RESULT=') !== false) {
		$chk_data_result = explode("=",$chk_data);
		$dmt_value_result = trim($chk_data_result[1]);
	}
	if(strpos($chk_data, 'TRANSID=') !== false) {
		$chk_data_transid = explode("=",$chk_data);
		$dmt_value_transid = trim($chk_data_transid[1]);
	}
	if(strpos($chk_data, 'ADDINFO=') !== false) {
		$chk_data_addinfo = explode("=",$chk_data);
		$dmt_value_addinfo = trim($chk_data_addinfo[1]);
	}
	if(strpos($chk_data, 'ERRMSG=') !== false) {
		$chk_data_err = explode("=",$chk_data);
		$dmt_value_errmsg = trim($chk_data_err[1]);
	}
}
echo "<br>==============================================================================<br>";
echo "ERROR : ".$dmt_value_error;
echo "<br>";
echo "RESULT : ".$dmt_value_result;
echo "<br>";
echo "TRANSID : ".$dmt_value_transid;
echo "<br>";
echo "ADDINFO : ".$dmt_value_addinfo;
echo "<br>";
echo "ERRMSG : ".$dmt_value_errmsg;
echo "<br>==============================================================================<br>";
echo "<br>";

exit();

if($dmt_value_error=='0' && $dmt_value_result=='0') {
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'TEST PRE PAY', '".$signInResult[1]."::".$cpUrl['pay']."')");
	
	$payOutput = $sp->cpRequest($signInResult[1], $cpUrl['pay']);
	
	$db->execute("INSERT INTO `mt_logs`(`mt_logs_id`, `mt_logs_date`, `mt_logs_type`, `mt_logs`) VALUES ('', NOW(), 'TEST RSP PAY', '".mysql_real_escape_string($payOutput)."')");	
	
	$payExplode = explode("\n",$payOutput);
	echo "PAYMENT OUTPUT";
	echo "<br>==============================================================================<br>";
	echo $signInResult[1];
	echo "<br>==============================================================================<br>";
	echo "<br>".date("d-m-Y H:i:s")."<br>";
	echo $payOutput;
	echo "<br>==============================================================================<br>";
}