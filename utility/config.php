<?php
error_reporting(0);
date_default_timezone_set('Asia/Calcutta');
ini_set('memory_limit','2048M');
set_time_limit(9999999999);
define('VERSION','3.0.1');
define("DIR",__DIR__);
define("HTTP",'http://localhost/9960499605');
define("HTTPS",'https://localhost/9960499605');

//exit("Website is under maintenance, please try after some time.");

// define constant of the projects
$db_user = 'erecharg_usr';			//DB USERNAME
$db_password = 'vinod@2017';			//DB PASSWORD (TO,NoKScrhS 
$db_name = 'erecharg_tst';		//DB NAME
$db_host = 'localhost';		//DB SERVER

// Include the class:
include(DIR."/db.class.php");
$db = new DB($db_name, $db_host, $db_user, $db_password);
include(DIR."/class.common.php");

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
$exioms = array('uname'=>'9595134455', 'key'=>'tzP9G9xiWmqa98cZ');
$aarav = array('usercode'=>'AP00039', 'key'=>'8600250250');
$ambika = array('userid'=>'8600250250', 'pass'=>'7454');
$arroh = array('uid'=>'2064', 'pass'=>'66vvv1');
?>