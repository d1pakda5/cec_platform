<?php
error_reporting(0);
date_default_timezone_set('Asia/Calcutta');
define('VERSION','3.0.1');
define("DIR",__DIR__);
define("HTTP",'http://99-604-99-605.com');
define("HTTPS",'https://99-604-99-605.com');
//exit("Website is under maintenance, please vist after some time.");
//exit();
// define constant of the projects
$db_user = 'recharge_click';			//DB USERNAME
$db_password = 'ZeU(OiQ+qsJ7';			//DB PASSWORD (TO,NoKScrhS 
$db_name = 'recharge_db';		//DB NAME
$db_host = 'localhost';		//DB SERVER

// Include the class:
include(DIR."/system/db.class.php");
$db = new DB($db_name, $db_host, $db_user, $db_password);
include(DIR."/system/class.common.php");
include(DIR."/system/mail.function.php");
include(DIR."/system/sms.function.php");

//OLD CONSTANT
define('UPLOADS',DIR.'/uploads/');
/*
* Application Details
*/
define('SITENAME','CLICK E-CHARGE');
define('SITEPHONE','8600000648');
define('SITEEMAIL','ankitsales@gmail.com');
define('SITEURL',HTTP);
define('SITELOGO','logo.png');
define('AUTHKEY','dfdc8d423f69fa9eKorDXZ8fgeDVEvpuiVMG2KoicPiKvCy');
define('TXNPREFIX','200000000000');

$eg_pay = array('uid'=>'ANKITMARKETING', 'pass'=>'anchul560', 'subuid'=>'SUBANKITMARKETING', 'subupass'=>'SUBANKIT@MARKETING');
$achariya = array('uid'=>'616e6b697473616c6573', 'pin'=>'5375d40e5c093');
$modem_rp = array('userid'=>'8600250250', 'pass'=>'7114');
$roundpay = array('userid'=>'8600250250', 'pass'=>'5824');
$rechargea2z = array('userid'=>'8600250250', 'pass'=>'773680');
$exioms = array('uname'=>'9595134455', 'key'=>'tzP9G9xiWmqa98cZ');
$aarav = array('usercode'=>'AP00039', 'key'=>'8600250250');
$ambika = array('userid'=>'8600250250', 'pass'=>'7454');
$arroh = array('acc_no'=>'ACC12354', 'pass'=>'9w3qvh', 'key'=>'251e340f-baf4-4f23-92d4-2ed987e67892');
$paymentall = array('username'=>'clickecharge', 'password'=>'vinod@9988');
$easy = array('userid'=>'XTG69U', 'pass'=>'VINOD-560' , 'pin'=>'5I3G80');
$esure = array('userid'=>'8600250250', 'pass'=>'1306');
?>